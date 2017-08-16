<?php

/**
 * General Service class for subclassing in modules.
 *
 * @author carsten
 * @property DbPDO $_db
 * @property Web $w
 */
class DbService {

    public $_db;
    public $w;

    /**
     * array for automatic caching of objects for the duration of this
     * invocation.
     *
     * @var <type>
     */
    private static $_cache = array(); // used for single objects
    public static $_cache2 = array();  // used for lists of objects
	public static $_select_cache = array();

    /**
     * This variable keeps track of active transactions
     *
     * @var boolean
     */
    public static $_active_trx = false;

    public function __get($name) {
        return $this->w->$name;
    }
    
    public static function getCache() {
		return self::$_cache;
	}
    public static function getCacheValue($class,$id) {
		if (array_key_exists($class,self::$_cache) && array_key_exists($id,self::$_cache[$class]))  {
			return self::$_cache[$class][$id];
		}
		return null;
	}
    public static function getCacheListValue($class,$key) {
		if (array_key_exists($class,self::$_cache2) && array_key_exists($key,self::$_cache2[$class]))  {
			return self::$_cache2[$class][$key];
		}
		return null;
	}

    function __construct(Web $w) {
        $this->_db = $w->db;
        $this->w = $w;
    }

    /**
     * Formats a timestamp
     * per default MySQL datetime format is used
     *
     * @param $time
     * @param $format
     */
    function time2Dt($time = null, $format = 'Y-m-d H:i:s') {
        return formatDate($time ? $time : time(), $format, false);
    }

    /**
     * Formats a timestamp
     * per default MySQL date format is used
     *
     * @param $time
     * @param $format
     */
    function time2D($time = null, $format = 'Y-m-d') {
        return formatDate($time ? $time : time(), $format, false);
    }

    function time2T($time = null, $format = 'H:i:s') {
        return date($format, $time ? $time : time());
    }

    function dt2Time($dt) {
        return strtotime(str_replace("/", "-", $dt));
    }

    function d2Time($d) {
        return strtotime(str_replace("/", "-", $d));
    }

    function t2Time($t) {
        return strtotime(str_replace("/", "-", $t));
    }

    /**
     * Clear object cache completely!
     *
     */
    function clearCache() {
        self::$_cache = array();
        self::$_cache2 = array();
    }

    /**
     * Fetch one object either by id
     * or by passing an array of key,value
     * to be used in a where condition
     *
     * @param <type> $table
     * @param <type> $idOrWhere
     * @return <type>
     */
    function getObject($class, $idOrWhere, $use_cache = true, $order_by = null, $includeDeleted = false) {
        if (!$idOrWhere || !$class)
            return null;

		if ($order_by !== null) $use_cache=false;

        $key = $idOrWhere;
        if (is_array($idOrWhere)) {
            $key = "";
            foreach ($idOrWhere as $k => $v) {
                $key.=$k . "::" . $v . "::";
            }
        }
        $usecache = $use_cache && is_scalar($key);
        // check if we should use the cache
        // this will eliminate 80% of SQL calls per page view!
        if ($usecache) {
            $obj = !empty(self::$_cache[$class][$key]) ? self::$_cache[$class][$key] : null;
            if (!empty($obj)) {
                return $obj;
            }
        }

        // not using cache or object not found in cache

        $o = new $class($this->w);
        $table = $o->getDbTableName();
        
        if (is_scalar($idOrWhere)) {
            $this->_db->get($table)->where($o->getDbColumnName('id'), $idOrWhere);
        } elseif (is_array($idOrWhere)) {
            if (is_complete_associative_array($idOrWhere)) {
                $this->_db->get($table)->where($idOrWhere);
            } else {
                // Warning! If this condition is met it means someone has given a non-complete associative array
                $this->w->Log->setLogger(get_class($this))->error("(getObject) The WHERE condition: " . json_encode($idOrWhere) . " has non-associative elements, this has security implications and is not allowed");
                return null;
            }
			
			// Default is deleted checks to 0
			$columns = $o->getDbTableColumnNames();

			if (!$includeDeleted && (property_exists(get_class($o), "is_deleted") || (in_array("is_deleted", $columns)))) {
				$this->_db->where('is_deleted', 0);
			}
        }

        if (!empty($order_by)) {
            $this->_db->order_by($order_by);
        }
		
		$this->buildSelect($o, $table, $class);
        $result = $this->_db->fetch_row();

        if ($result) {
            $obj = $this->getObjectFromRow($class, $result, true);
            if ($usecache) {
                self::$_cache[$class][$key] = $obj;
                if ($obj->id != $key && !empty(self::$_cache[$class][$obj->id])) {
                    self::$_cache[$class][$obj->id] = $obj;
                }
            }
            return $obj;
        } else {
            return null;
        }
    }
	function buildSelect($object, $table, $class) {
		$this->_db->clearSelect();
		if(!isset(self::$_select_cache[$class])) {
			self::$_select_cache[$class] = array();
		}
		if(!empty(self::$_select_cache[$class][$table])) {
			$this->_db->select(self::$_select_cache[$class][$table]);
			return NULL;
		}
        // Move date conversion to SQL.
        // Automatically converts keys with different database values
		$parts = array();
        foreach ($object->getDbTableColumnNames() as $k) {
            if(0 === strpos($k, 'dt_') || 0 === strpos($k, 'd_')) { //  || 0 === strpos($k, 't_')
                // This is MySQL specific!
                $parts[] = "UNIX_TIMESTAMP($table.`".$object->getDbColumnName($k)."`) AS `$k`";
            } else if($k != $object->getDbColumnName($k)) {
                $parts[] = "`".$object->getDbColumnName($k)."` as `$k`";
            } else {
                $parts[] = $k;
            }
        }
		self::$_select_cache[$class][$table] = implode(',', $parts);
		$this->_db->select(self::$_select_cache[$class][$table]);
		return NULL;
	}
    /**
     *
     * @param String $class
     * @param Mixed $where
     * @param Boolean $useCache
     * 
     * @return <type>
     */
    function getObjects($class, $where = null, $cache_list = false, $use_cache = true, $order_by = null, $offset = null, $limit = null, $includeDeleted = false) {
        if (!$class)
            return null;
		
		if ($order_by !== null || $offset !== null || $limit !== null ) {
			$use_cache=false;
			$cache_list=false;
		}

        // if using the list cache
        if ($cache_list) {
            if (is_array($where)) {
                $key = "";
                foreach ($where as $k => $v) {
                    $key .= $k . "::" . $v . "::";
                }
            } else {
                $key = $where;
            }

            if (isset(self::$_cache2[$class][$key])) {
                return self::$_cache2[$class][$key];
            }
        }

        $o = new $class($this->w);
        $table = $o->getDbTableName();
        $this->_db->get($table);
        if ($where && is_array($where)) {
            if (is_complete_associative_array($where)) {
                foreach ($where as $par => $val) {
                    $dbwhere[$o->getDbColumnName($par)] = $val;
                }
                $this->_db->where($dbwhere);
            } else {
                // Warning! If this condition is met it means someone has given a non-complete associative array
                $this->w->Log->setLogger(get_class($this))->error("(getObjects) The WHERE condition: " . json_encode($where) . " has non-associative elements, this has security implications and is not allowed");
                return null;
            }
        } else if ($where && is_scalar($where)) {
            $this->_db->where($where, false);
        }
		
		// Default is deleted checks to 0
		$columns = $o->getDbTableColumnNames();
		
		if (!$includeDeleted && (property_exists(get_class($o), "is_deleted") || (in_array("is_deleted", $columns)))) {
			$this->_db->where('is_deleted', 0);
		}
		
		// Ordering
        if (!empty($order_by)) {
            $this->_db->order_by($order_by);
        }
		
		// Offset
		if (!empty($offset) && !empty($limit)) {
			$this->_db->offset($offset);
		}
		
		// Limit
		if (!empty($limit)) {
			$this->_db->limit($limit);
		}

        $this->buildSelect($o, $table, $class);
        $result = $this->_db->fetch_all();
        if ($result) {
            $objects = $this->getObjectsFromRows($class, $result, true);
            if ($objects) {

                // store the complete list
                if ($cache_list && !isset(self::$_cache2[$class][$key])) {
                    self::$_cache2[$class][$key] = $objects;
                }

                // also store each individual object
                if ($use_cache) {
                    foreach ($objects as $ob) {
                        if (!isset(self::$_cache[$class][$ob->id])) {
                            self::$_cache[$class][$ob->id] = $ob;
                        }
                    }
                }
            }
            return $objects;
        } else {
            return null;
        }
    }

    /**
     *
     * @param <type> $table
     * @param <type> $id
     * @return <type>
     */
    function getObjectFromRow($class, $row, $from_db = false) {
        if (!$row || !$class)
            return null;
        $o = new $class($this->w);
		
		// The second parameter below is the prompt to convert mysql timestamps into unix timestamps
		// We make this convert in the db query now but there are times when using getObjectFromRow
		// where you may have made the query yourself (and therefore no UNIX() cast in mysql)
		// It also happens that the $from_db flag is inversely related to the convert parameter below
        $o->fill($row, !$from_db);
		 
        // test implementation for preserving original database values
        if ($from_db == true) {
        	$o->__old = $row;
        }
        // Test implementation for a post fill callback
        if (method_exists($o, "afterConstruct")) {
            $o->afterConstruct();
        }

        return $o;
    }

    function getObjectsFromRows($class, $rows, $from_db = false) {
        $list = array();
        if (!empty($class) && !empty($rows) && class_exists($class)) {
            foreach ($rows as &$row) {
                $list[] = $this->getObjectFromRow($class, $row, $from_db);
            }
        }
        return $list;
    }

    // DEPRECATED AS OF 0.7.0
    function fillObjects($class, $rows, $from_db = false) {
        return $this->getObjectsFromRows($class, $rows, $from_db);
    }

    /**
     * Start a transaction
     */
    public function startTransaction() {
        $this->_db->startTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commitTransaction() {
        $this->_db->commitTransaction();
    }

    /**
     * Rollback a transaction!
     */
    public function rollbackTransaction() {
        $this->_db->rollbackTransaction();
    }

    /**
     * Returns true if a transaction is currently active!
     */
    public function isActiveTransaction() {
        return $this->_db->activeTransaction();
    }

    function lookupArray($type) {
        $rows = $this->_db->get("lookup")->where("type", $type)->fetch_all(); // select("code,title")->from
        foreach ($rows as $row) {
            $select[$row['code']] = $row['title'];
        }
        return $select;
    }

}
