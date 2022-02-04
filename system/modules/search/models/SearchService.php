<?php

class SearchService extends DbService
{
    /**
     * Returns an array of search indeces that the currently logged in user
     * has access to. This uses the global module configuration and auth system
     * to check.
     *
     * @return array
     */
    public function getIndexes()
    {
        $indexes = array();
        foreach ($this->w->modules() as $module) {
            $search = Config::get("{$module}.search");
            if (!empty($search)) {
                $indexes = array_merge($indexes, $search);
            }
        }
        asort($indexes);
        return $indexes;
    }
    public function reindex($index)
    {
        $indexes = $this->getIndexes();
        if (!empty($index) && in_array($index, $indexes)) {
            // first delete all entries in the index table for this index
            $sql = "DELETE FROM object_index WHERE class_name = '{$this->_db->quote($index)}'";
            $this->_db->sql($sql)->execute();

            $objects = $this->getObjects($index, array("is_deleted" => 0));
            if (!empty($objects)) {
                foreach ($objects as $object) {
                    $object->_searchable->insert();
                }
            }
        }
    }
    public function reindexAll()
    {
        // delete all index entries
        $this->_db->sql("DELETE FROM object_index")->execute();

        // go over each index and reindex
        foreach ($this->getIndexes() as $index) {
            $o = new $index($this->w);
            $table = $o->getDbTableName();
            if ($this->_db->get($table)) {
                $objects = $this->getObjects($index, array("is_deleted" => 0));
                if (!empty($objects)) {
                    foreach ($objects as $object) {
                        if (property_exists($object, "_searchable")) {
                            $object->_searchable->insert();
                        }
                    }
                }
            }
        }
    }
    public function reindexAllFulltextIndex()
    {
        $this->_db->sql("ALTER TABLE object_index DROP INDEX object_index_content;");
        $this->_db->sql("CREATE FULLTEXT INDEX object_index_content ON object_index(content);");
    }

    /**
     * Returns the  results for a given query.
     *
     * If $index !== null -> limit search to this index.
     *
     * If $page !== null -> display only results for this page.
     *
     * @param string $query
     * @param string $index
     * @param integer $page
     * @param integer $pageSize
     *
     * @return array
     */
    public function getResults($query, $index = null, $page = null, $pageSize = null)
    {

        // sanity check
        if (empty($query) || strlen($query) < 3) {
            return null;
        }

        // sanitise query string!
        // Remove all xml/html tags
        $str = strip_tags($query);

        // Remove case
        $str = strtolower($str);

        // Remove line breaks
        $str = str_replace("\n", " ", $str);

        // Remove all characters except A-Z, a-z, 0-9, dots, commas, hyphens, spaces and forward slashes (for dates)
        // Note that the hyphen must go last not to be confused with a range (A-Z)
        // and the dot, being special, is escaped with backslash
        $str = preg_replace("/[^A-Za-z0-9 \.,\-\/@'\*\+:]/", '', $str);

        // Replace sequences of spaces with one space
        $str = preg_replace('/  +/', ' ', $str);

        // Fixed crash caused by extra spaces
        $str = trim($str);

        // Now, default to AND searching, that means we prefix every word with a '+' unless 'OR' is specified, then we leave it
        // And add a '-' if an occurence of 'NOT' is found
        $str_array = explode(' ', $str);

        // The first word will always be a keyword, so prefix that automatically

        if (count($str_array) > 1 && $str_array[1] !== "OR") {
            $str_array[0] = '+' . $str_array[0];
        }

        if (count($str_array) > 1) {
            for ($i = 1; $i < count($str_array); $i++) {
                if (strtoupper($str_array[$i]) === "AND") {
                    $str_array[$i + 1] = '+' . $str_array[$i + 1];
                    array_splice($str_array, $i, 1);
                } elseif (strtoupper($str_array[$i]) === "OR") {
                    array_splice($str_array, $i, 1);
                } elseif (strtoupper($str_array[$i]) === "NOT") {
                    $str_array[$i + 1] = '-' . $str_array[$i + 1];
                    array_splice($str_array, $i, 1);
                } else {
                    $str_array[$i] = '+' . $str_array[$i];
                }
            }
        }

        $str = implode(' ', $str_array);
        LogService::getInstance($this->w)->setLogger("SEARCH")->info("Query: " . $str);

        $index_mode = "BOOLEAN MODE";
        $index_all_limit = 10;

        $select = "SELECT class_name, object_id ";
        $from = " FROM object_index WHERE object_id != 0 AND MATCH (content) AGAINST (" . $this->_db->quote($str) . " IN $index_mode) ";
        $select .= $from;

        $select_count = "select count(*) as MAX_RESULT " . $from;
        $max_result = 0;

        // Setup limit and offset string
        $limitBy = '';
        if (!empty($page) && !empty($pageSize)) {
            if (is_numeric($page) && is_numeric($pageSize)) {
                // Set page and pagesize to within valid constraints
                $page = ($page <= 0 ? 1 : $page);
                $pageSize = ($pageSize <= 0 ? 20 : $pageSize);
                $limitBy .= " LIMIT " . (($page - 1) * $pageSize) . ", $pageSize ";
            }
        }

        // check if search is constrained to an index
        if ($index && in_array($index, array_values($this->getIndexes()))) {
            // $limitBy will just be an empty string if page and pageSize are invalid
            $select .= " AND class_name = '" . $index . "' " . $limitBy;
            $select_count .= " AND class_name = '" . $index . "'";
            $max_result = $this->_db->sql($select_count)->fetchElement('MAX_RESULT');
        } else {
            // if searching over all indexes, limit the results for each index to 10
            foreach ($this->getIndexes() as $title => $classname) {
                $s2[] = "(" . $select . " AND class_name = '" . $classname . "' LIMIT 0, $index_all_limit )";
            }
            $select = implode(" UNION ", $s2);
        }

        $results = $this->_db->sql($select)->fetchAll();

        return array($results, $max_result);
    }
}
