
<?php

class InstallDatabaseService extends DbService
{
    public $_PDO;
    public $_rootPDO;
    public $_user;
    
    function getURL()
    {
        return $_SESSION['install']['saved']['db_driver'] . ":host=" .
                $_SESSION['install']['saved']['db_host'] . ";port=" .
                $_SESSION['install']['saved']['db_port'];
    }
    
    
    function getPDO($database=false)
    {
        // instance organisational structure
        // it'll kill the old connection if the username has changed
        if(isset($this->_PDO) && strcmp($_SESSION['install']['saved']['db_username'], $this->_user) === 0)
        {
            return $this->_PDO;
        }
     
        //'mysql:host=mysql1.alwaysdata.com;port=3306;dbname=xxx'
        $url = $this->getURL();
    
        if(empty($_SESSION['install']['saved']['db_username']))
        {
            throw new Exception("A username is required to connect to the database.");
        }
        else
        {
            $_user = $_SESSION['install']['saved']['db_username'];
            $this->_PDO = new PDO($url, $_SESSION['install']['saved']['db_username'],
                                  $_SESSION['install']['saved']['db_password']);
            $this->_PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        if($database)
        {
            $this->_PDO->exec("USE " .  $_SESSION['install']['saved']['db_database']  . ";");
        }
        
        //error_log('connected to : ' . $_SESSION['install']['saved']['db_username'] . '@' . $this->getURL());
        
        return $this->_PDO;
    }
    
    function getRootPDO()
    {
        // instance organisational structure
        if(isset($this->_rootPDO))
        {
            return $this->_rootPDO;
        }
        
        // root password
        $db_root = isset($_POST['db_root']) ? $_POST['db_root'] : '';
        
        //'mysql:host=mysql1.alwaysdata.com;port=3306;dbname=xxx'
        $url = $this->getURL();
        $this->_rootPDO =  new PDO($url, 'root', $db_root);
        $this->_rootPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // now return the newly created PDO
        return $this->_rootPDO;
    }
    
    function databaseExists($db)
    {
        return $this->checkDatabases($db);
    }
    
    function getDatabases()
    {
        return $this->checkDatabases(false);
    }
    
    function getDatabasesWithMigrations()
    {
        return $this->checkDatabases(true);
    }
    
    function checkDatabases($db=true)
    {
        $pdo = $this->getPDO();
        //error_log("PDO: " . print_r($pdo, true));
        
        // then check it now exists in the databases
        $databases = array();
        $db_results = $pdo->query('SHOW DATABASES;');
        if(isset($db_results) && !empty($db_results))
        {
            // use the names of the databases as the key in an associative array
            foreach($db_results as $row)
            {
                if($row[0] != 'information_schema')
                    $databases[$row[0]] = array();
            }
            
            //error_log("DATABASES: " . print_r($databases, true));
            
            // check if database is in the databases
            if(is_string($db) && !empty($db))
                return isset($databases[$db]);
            
            // then get the tables inside each database
            foreach(array_keys($databases) as $database)
            {
                $pdo->exec("USE $database;");
                $tb_results = $pdo->query('SHOW TABLES;');
                if(isset($tb_results) && !empty($tb_results))
                {
                    $total = 0;
                    foreach($tb_results as $row)
                    {
                        // if migrations
                        if(is_bool($db) && $db)
                        {
                            if(strcmp($row[0], 'migration') === 0)
                            {
                                $databases[$database]['migration'] = array();
                                $mi_results = $pdo->query("SELECT `classname`, `dt_created`, `batch` FROM `migration` WHERE 1;");
                                foreach($mi_results as $migrations)
                                {
                                    $databases[$database]['migration'][] = array(
                                         'classname'   => $migrations[0],
                                         'dt_created'  => $migrations[1],
                                         'batch'       => $migrations[2]);
                                }
                            }
                        }
                        else // just add the table names
                        {
                            $databases[$db][] = $row[0];
                        }
                        $total++;
                    }
                    $databases[$database]['total'] = $total;
                }
            }
            
            //error_log("DATABASES: " . print_r($databases, true));
        }
        
        return $databases;
    }
    
    function getAdmins()
    {
        $pdo = $this->getPDO(true);
        
        // then
        $admins = array();
        // then get the admins inside the database
        $ad_results = $pdo->query("SELECT  `user`.`id`, `user`.`login`, `contact`.`firstname`, `contact`.`lastname`, `contact`.`email`, `contact`.`dt_created` FROM `user` INNER JOIN `contact` ON `user`.`id`=`contact`.`id` WHERE  `user`.`is_admin`=1;");
        
        if(isset($ad_results) && !empty($ad_results))
        {
            foreach($ad_results as $ad_result)
            {
                $admins[] = array(
                                  'id'           => $ad_result[0],
                                  'login'        => $ad_result[1],
                                  'firstname'    => $ad_result[2],
                                  'lastname'     => $ad_result[3],
                                  'email'        => $ad_result[4],
                                  'dt_created'   => $ad_result[5]);
            }
            
            //error_log("ADMINS: " . print_r($admins, true));
        }

        return $admins;
    }
    
    function getConfig()
    {
        return array("driver"  => $_SESSION['install']['saved']['db_driver'],
                    "hostname" => $_SESSION['install']['saved']['db_host'],
                    "port"     => $_SESSION['install']['saved']['db_port'],
                    "username" => $_SESSION['install']['saved']['db_username'],
                    "password" => $_SESSION['install']['saved']['db_password'],
                    "database" => $_SESSION['install']['saved']['db_database']);
    }
    
    function importTables(Web &$w)
    {
        $config = $this->getConfig();
        
        $pdo = new DBPDO($config);
        
        if(empty($config['database']))
            throw new Exception("Cannot install tables into a database without a name");
        
        $pdo->exec("USE `{$config['database']}`;");
        $tb_results = $pdo->query("SHOW TABLES;");
        if(isset($tb_results))
        {
            if($tb_results->rowCount() > 0)
            {
                // drop each table
                foreach($tb_results as $row)
                {
                    $pdo->exec("DROP TABLE {$row[0]};");
                    //echo "{$row[0]}<br/>";
                }
                
                $tb_results_second_opinion = $pdo->query("SHOW TABLES;");
                if(isset($tb_results_second_opinion))
                {
                    if($tb_results_second_opinion->rowCount() > 0)
                        throw new Exception("Database \"". $config['database'] . "\" could not be emptied. " .
                                            "Currently has " . $tb_results_second_opinion->rowCount() . " rows");
                }
                else
                    throw new Exception("Could not perform sql recount of tables for \"". $config['database'] . "\".");
            }
            // else it's empty... all is well... proceed...
        }
        else
            throw new Exception("Could not retrieve tables for \"". $config['database'] . "\"");
        
        
        // Run migrations
        Config::set('database.database', $config['database']);
        
        // this won't work as it assigns the database to be a PDO object and not a DbPDO object
        $w->db = $pdo;
        $w->Migration->installInitialMigration();
        $w->Migration->runMigrations("all");
    }
}

