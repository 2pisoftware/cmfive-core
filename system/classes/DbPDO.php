<?php

/**
 * PDO Extension class for Cmfive
 *
 * See: http://www.php.net/manual/en/book.pdo.php for the PDO Class reference
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class DbPDO extends PDO
{
    public $sql = null;
    public $total_results = 0;

    private $table_names = [];
    private static $_QUERY_CLASSNAME = ["\Envms\FluentPDO\Queries\Insert", "\Envms\FluentPDO\Queries\Select", "\Envms\FluentPDO\Queries\Update", "Envms\FluentPDO\Queries\Insert", "Envms\FluentPDO\Queries\Select", "Envms\FluentPDO\Queries\Update"];
    private $query = null;
    private $fpdo = null;
    private static $trx_token = 0;
    private $config;
    private $migration_mode = 0;

    public function __construct($config = [], $override = false)
    {
        // Set up our PDO class
        switch ($config['driver']) {
                // MsSQL
            case 'sqlsrv':
                $port = isset($config['port']) && !empty($config['port']) ? "," . $config['port'] : "";
                $url = "{$config['driver']}:Server={$config['hostname']}{$port};Database={$config['database']}";
                break;
                // Linux Apache2 driver
            case 'dblib':
                $port = isset($config['port']) && !empty($config['port']) ? "," . $config['port'] : "";
                $url = "{$config['driver']}:host={$config['hostname']}{$port};dbname={$config['database']}";
                break;
                // MySQL
            case 'mysql':
            default:
                $port = isset($config['port']) && !empty($config['port']) ? ";port=" . $config['port'] : "";
                $url = "{$config['driver']}:host={$config['hostname']};dbname={$config['database']}{$port}";
        }

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4', time_zone='" . date("e") . "'",
        ];

        if (!empty($config['ssl_cert_path'])) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $config['ssl_cert_path'];
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        parent::__construct($url, $config["username"], $config["password"], $options);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Instantiate a FluentPDO class and init vars
        $this->fpdo = new \Envms\FluentPDO\Query($this);
        $this->fpdo->convertTypes(true, true);

        $this->sql = 'getSql';
        $this->config = $config;

        $this->getAvailableTables();

        if (in_array("custom_stopwords_override", $this->table_names) && $override == true && $config['driver'] == 'mysql') {
            $this->disableStopwords();
        }
    }

    public function disableStopwords()
    {
        $this->sql("SET SESSION innodb_ft_user_stopword_table = '{$this->getDatabase()}/custom_stopwords_override';");
    }

    public function getDatabase()
    {
        return $this->config['database'];
    }

    public function getHost()
    {
        return $this->config['host'];
    }

    public function getPort()
    {
        return $this->config['port'];
    }

    public function getDriver()
    {
        return $this->config['driver'];
    }

    public function setMigrationMode($value)
    {
        $this->migration_mode = $value ? 1 : 0;
    }

    /**
     * Returns a cached list of available tables in the database
     *
     * @return array DbPDO::$table_names all table names
     */
    public function getAvailableTables()
    {
        if ($this->migration_mode || empty($this->table_names)) {
            $this->table_names = [];
            $query = 'show tables';
            if ($this->config['driver'] == 'sqlsrv') {
                $query = 'select TABLE_NAME from INFORMATION_SCHEMA.TABLES';
            }
            foreach ($this->query($query)->fetchAll(PDO::FETCH_NUM) as $table) {
                $this->table_names[] = $table[0];
            }
        }
        return $this->table_names;
    }

    /**
     * This function sets up a FluentPDO query with the given table name, an
     * error will be thrown if the table name doesn't exist in the database
     *
     * @param string $table_name
     * @return \DbPDO|null
     */
    public function get($table_name)
    {
        if (!in_array($table_name, $this->getAvailableTables())) {
            trigger_error("Table $table_name does not exist in the database", E_USER_ERROR);
            return null;
        }
        $this->query = $this->fpdo->from($table_name);
        return $this;
    }

    /**
     * Adds a select rule to the current query.
     *
     * Note: A single call to select will append your select rule onto the default
     * select query which is '*'. Here are some examples to demonstrate:
     *      $this->get('users')->select('login')
     * would result in the following SQL being executed
     *      "SELECT *, login FROM users..."
     * This becomes an issue if you are joining tables and only want to select certain values
     *      $this->get('users')->select('contact.email')->leftJoin('contact on user.contact_id = contact.id')
     * would result in
     *      "SELECT users.*, contact.email FROM users LEFT JOIN..."
     *
     * To stop this behaviour, first make a call to select() with no parameters. Then method
     * chain on the select rule that you actually want. The null value in the first call forces FluentPDO
     * to wipe all select rules.
     *      From the above example, here is the issue fixed to demonstrate:
     *      $this->get('users')->select()->select('contact.email')->leftJoin('contact on user.contact_id = contact.id')
     * results in
     *      "SELECT contact.email FROM users LEFT JOIN..."
     *
     * @param string|null $select select query
     * @return \DbPDO $this
     */
    public function select($select = null)
    {
        if ($this->query !== null) {
            $this->query = $this->query->select($select);
        }
        return $this;
    }

    /**
     * A helper method to count the number of rows on a given query.
     * Note you don't need to execute your query or add any special "count(*)"
     * select calls, this does it all for you.
     *
     * @return int|null $result number of results
     */
    public function count()
    {
        if ($this->query !== null) {
            $result = $this->select()->select("count(*)")->fetchElement("count(*)");
            return intval($result);
        }
    }

    /**
     * Adds left join to the current query
     *
     * @param string $leftjoin left join rule to apply
     * @return \DbPDO $this
     */
    public function leftJoin($leftJoin)
    {
        if ($this->query !== null && !empty($leftJoin)) {
            $this->query = $this->query->leftJoin($leftJoin);
        }
        return $this;
    }

    /**
     * Adds left outer join to the current query
     *
     * @param string $leftjoin left join rule to apply
     * @return \DbPDO $this
     */
    public function leftOuterJoin($leftOuterJoin)
    {
        if ($this->query !== null && !empty($leftOuterJoin)) {
            $this->query = $this->query->leftOuterJoin($leftOuterJoin);
        }
        return $this;
    }

    /**
     * Adds inner join to the current query
     *
     * @param string $innerjoin inner join rule to apply
     * @return \DbPDO $this
     */
    public function innerJoin($innerJoin)
    {
        if ($this->query !== null && !empty($innerJoin)) {
            $this->query = $this->query->innerJoin($innerJoin);
        }
        return $this;
    }

    /**
     * This function appends where clauses to the query, the where part of the
     * statement can be reset by passing NULL as the first parameter
     *
     * @param string|array $column
     * @param string $equals
     * @return \DbPDO|null
     */
    public function where($column, $equals = null)
    {
        if ($this->query !== null) {
            if (empty($column)) {
                // Resets the where part of the statement
                $this->query = $this->query->where(null);
            } else {
                if (is_array($column)) {
                    $this->query = $this->query->where($column);
                } else {
                    if (func_num_args() == 2) {
                        $this->query = $this->query->where($column, $equals);
                    } else {
                        $this->query = $this->query->where($column);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Orders result set in query (SQL ORDER BY)
     *
     * @param string $orderby what to order result set by
     * @return \DbPDO $this
     */
    public function orderBy($orderby)
    {
        if ($this->query !== null && !empty($orderby)) {
            $this->query = $this->query->orderBy($orderby);
        }
        return $this;
    }

    /**
     * Limits the results returned in a query (SQL LIMIT)
     *
     * @param mixed $limit how many records to limit the query to
     * @return \DbPDO $this
     */
    public function limit($limit)
    {
        if ($this->query !== null and !is_null($limit)) {
            $this->query = $this->query->limit($limit);
        }
        return $this;
    }

    /**
     * Offsets the result set of a query (SQL OFFSET)
     *
     * @param mixed $offset how many records to offset by
     * @return \DbPDO $this
     */
    public function offset($offset)
    {
        if ($this->query !== null and !is_null($offset)) {
            $this->query = $this->query->offset($offset);
        }
        return $this;
    }

    /**
     * Groups results in query (SQL GROUP BY)
     *
     * @param string $grouping what to group results by
     * @return \DbPDO $this
     */
    public function groupBy($grouping)
    {
        if ($this->query !== null and !is_null($grouping)) {
            $this->query = $this->query->groupBy($grouping);
        }
        return $this;
    }

    /**
     * Prepares a statement with a query
     * Note that in the migration from Crystal, the sql function executed RAW SQL
     * Which is what this is emulating
     *
     * @param string $query
     * @return DbPDO
     */
    public function sql($query)
    {
        $this->query = $this->query($query);
        return $this;
    }

    /**
     * Executes a prepared statement
     *
     * @return mixed
     */
    public function execute()
    {
        $this->query = $this->query->execute();
        return $this->query;
    }

    /**
     * Fetches $element from the first matching row in the query
     *
     * @param string $element row key to return
     * @return mixed|null element
     */
    public function fetchElement($element)
    {
        $row = $this->fetchRow();
        if (empty($row)) {
            return null;
        }

        return (!is_null($row[$element]) ? $row[$element] : null);
    }

    /**
     * Fetches the first matching row from the query
     *
     * @return array rowk
     */
    public function fetchRow()
    {
        return $this->query->fetch();
    }

    /**
     * Fetches all matching rows from the query
     *
     * @return array rows
     */
    public function fetchAll()
    {
        if (!empty($this->query)) {
            return $this->query->fetchAll();
        }

        return [];
    }

    /**
     * Sets up class with a PDO insert query and required array of values
     *
     * @param string $table_name Name of data table
     * @param array $data Data to insert
     * @return \DbPDO
     */
    public function insert($table_name, $data)
    {
        $this->query = $this->fpdo->insertInto($table_name, $data);
        return $this;
    }

    /**
     * Sets up class with a PDO update query, also appends optional
     * update data if needed
     *
     * @param string $table_name
     * @param array $data
     * @return \DbPDO
     */
    public function update($table_name, $data = null)
    {
        $this->query = $this->fpdo->update($table_name);
        if (!empty($data)) {
            $this->query = $this->query->set($data);
        }

        return $this;
    }

    /**
     * Sets up class with a PDO delete query
     *
     * @param string $table_name
     * @return \DbPDO
     */
    public function delete($table_name)
    {
        $this->query = $this->fpdo->deleteFrom($table_name);
        return $this;
    }

    /**
     * Helper functions to help with sorting and pagination
     */

    /**
     * Paginates data, pages are expected to start at 1
     *
     * @param int $page
     * @param int $page_size
     * @return \DbPDO
     */
    public function paginate($page = null, $page_size = null)
    {
        if ($this->query && !is_null($page) && !is_null($page_size) && is_numeric($page) && is_numeric($page_size)) {
            $this->query = $this->query->offset(($page - 1) * $page_size)->limit($page_size);
        }
        return $this;
    }

    /**
     * Sorts data
     *
     * @param string $sort_field
     * @param string $sort_direction
     * @return \DbPDO
     */
    public function sort($sort_field = null, $sort_direction = null)
    {
        if (!is_null($this->query) && !is_null($sort_field) && !is_null($sort_direction) && in_array(strtolower($sort_direction), ['asc', 'desc'])) {
            $this->query = $this->query->orderBy($sort_field . ' ' . $sort_direction);
        }
        return $this;
    }

    /**
     * Magic method call so we can use reserved words in this class
     * such as "and"
     *
     * This method can also be used to force calls down to PDO, bypassing this class and FluentPDO.
     * To do that, prefix only the first intended PDO call with an underscore.
     *
     * @param string $func
     * @param array $args
     * @return \DbPDO
     */
    public function __call($func, $args)
    {
        if ($func[0] == "_") {
            $func = substr($func, 1);
        }
        switch ($func) {
            case 'and':
                return $this->where($args[0], $args[1]);
                break;

            default:
                /**
                 * What this does is palm off unknown function calls to the parent
                 * which will still throw an error if the method doesnt exist BUT
                 * with the code above that strips off the leading underscore
                 * (if present) will mean that we can bypass this DbPDO/FluentPDO
                 * class and go straight to the underlying PDO implementation,
                 * just by prefixing underscores to the first method call.

                 * NOTE: You only need to prefix the first method when chaining as the return value for
                 * the first call is a PDOStatement
                 */
                return call_user_func_array("parent::" . $func, $args);
        }
    }

    /**
     * Returns the SQL query string
     *
     * @return string|null the sql query
     */
    public function getSql()
    {
        if (!empty($this->query) && in_array(get_class($this->query), DbPDO::$_QUERY_CLASSNAME)) {
            return $this->query->getQuery();
        }
        return null;
    }

    public function columnCount()
    {
        return $this->query->columnCount();
    }

    public function getColumnMeta($i)
    {
        return $this->query->getColumnMeta($i);
    }

    // Completely clears the select statement (removes table.*)
    public function clearSelect()
    {
        if (!empty($this->query) && (is_a($this->query, 'Envms\FluentPDO\Queries\Select'))) {
            $this->query = $this->query->select(null);
        }
        return $this;
    }

    public function clearSql()
    {
        // Clear everything
        if (!empty($this->query) && is_a($this->query, "PDOStatement")) {
            $this->query = $this->query->where(null);
            $this->query = $this->query->orderBy(null);
            $this->query = $this->query->limit(null);
            $this->query = $this->query->offset(null);
            $this->query = $this->query->fetch(null);
            $this->query = $this->query->select(null);
        }
        return $this;
    }

    /**
     * Warning: do not implement PSR2 rules for last_insert_id. Overriding the
     * PDO::lastInsertId will cause an infinite loop via FluentPDOs use of
     * the same function.
     */
    // public function lastInsertId($seqname = null)
    // {
    //
    // }

    /**
     * Returns the lsat inserted id
     *
     * @return int|null
     */
    public function last_insert_id()
    {
        if ($this->query !== null) {
            // Checks if execute hasn't been called yet, and calls it
            if ($this->query instanceof \Envms\FluentPDO\Queries\Insert) {
                $this->execute();
            }

            $last_id = $this->query;
			$this->query = null;
			return $last_id;
        }

        return null;
    }

    /**
     * Start a transaction
     *
     * @return null
     */
    public function startTransaction()
    {
        // start if there is no current transaction
        if (self::$trx_token == 0) {
            $this->beginTransaction();
        }
        // raise the transaction counter by one
        self::$trx_token++;
    }

    /**
     * Commit a transaction
     *
     * @return null
     */
    public function commitTransaction()
    {
        // only do anything if there is an active transaction
        if (self::$trx_token == 0) {
            return;
        }
        // only the first transaction will be committed
        if (self::$trx_token == 1 && $this->inTransaction()) {
            $this->commit();
        }
        // decrease the transaction counter
        self::$trx_token--;
    }

    /**
     * Rollback a transaction!
     * This includes a call to clear_sql()
     *
     * A transaction can be rolled back any time
     *
     * @return null
     */
    public function rollbackTransaction()
    {
        // only do anything if there is an active transaction
        if (self::$trx_token == 0) {
            return;
        }
        $this->clearSql();
        if ($this->inTransaction()) {
            $this->rollBack();
        }
        self::$trx_token = 0;
    }

    /**
     * Returns true if there is an active transaction
     *
     * @return boolean
     */
    public function activeTransaction()
    {
        return self::$trx_token > 0;
    }
}
