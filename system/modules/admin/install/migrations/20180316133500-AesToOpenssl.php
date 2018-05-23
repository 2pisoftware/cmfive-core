<?php

class AesToOpenssl extends CmfiveMigration {
    
    private function migrate($up = true) {
        /* DB table_name and ObjectName don't always match 
        * (e. g. table name: channel_email_option, object name: EmailChannelOption), 
        * therefore it is not always possible to get a DB object from table name.
        * to do: make them match each other? (e. g. channel_email_option -> ChannelEmailOption)*/
        
        // throw exception if php version >= 70100
        /*if (PHP_VERSION_ID >= 70100) {
            $this->w->ctx("migrationMessage", 'Migration from AES to Openssl or backwards is only supported on PHP version <= 7.1');
            return;
        }*/
        
        // get only tables with encrypted values
        $db = Config::get("database")["database"];
        $table = $this->w->db->query("select table_name, column_name from information_schema.columns 
            where table_schema='$db' and column_name like 's\_%';")->fetchAll();
        
        if (empty($table)) {
            return;
        }
        
        foreach ($table as $row) {
            foreach ($row as $key => $val) {
                if (is_numeric($key)) {
                    unset($row[$key]);
                }
            }
            
            $passwordSalt = null;
            
            $tableName = $row['table_name'];
            $columnName = $row['column_name'];
            
            if ($tableName == "channel_email_option") {
                $passwordSalt = hash("md5", $this->w->moduleConf("channels", "__password"));
            }
            
            else if ($tableName == "report_connection") {
                $passwordSalt = hash("md5", $this->w->moduleConf("report", "__password"));
            }
            
            else {
                $passwordSalt = md5('override this in your project config');
            }
            
            $tbl = $this->w->db->query("select id, $columnName from $tableName")->fetchAll();
            foreach ($tbl as $r) {
                foreach ($r as $k => $v) {
                    if (is_numeric($k)) {
                        unset($r[$k]);
                    }
                }   
                
                $decrypted = null;
                $encrypted = null;
                
                if ($up) {
                    $decrypted = AESdecrypt($r[$columnName], $passwordSalt);
                    $encrypted = openssl_encrypt($decrypted, "AES-256-CBC", Config::get("openssl")["key"], 0, Config::get("openssl")["iv"]);
                }
                
                else {
                    $decrypted = openssl_decrypt($r[$columnName], "AES-256-CBC", Config::get("openssl")["key"], 0, Config::get("openssl")["iv"]);
                    $encrypted = AESencrypt($decrypted, $passwordSalt);
                }
                
                $this->w->db->update($tableName, [$columnName => $encrypted])->where('id', $r['id'])->execute();
            }
        }
        
        /*foreach ($availableTables as $tableName) {
            $table = $this->w->db->query("select * from " . $tableName)->fetchAll();*/
            // $classname = str_replace('_', '', ucwords($tableName, '_'));
            /*if (class_exists($classname)) {
                $objects = $this->w->Admin->getObjects($classname);
            }*/
            
            /*foreach ($objects as $object) {
                if ($object) {
                    $object->decrypt();
                    $object->update();
                    if (!$object->update()) {
                        $this->down();
                        $this->w->ctx("migrationMessage", 'Migration from AES to openSSL was not successful');
                        return;
                    }
                }
            }*/
        //}
    }

    public function up() {
        $this->w->migrating = true;
        $this->migrate();
        $this->w->ctx("migrationMessage", 'Migration from AES to openSSL was successful');
    }

    public function down() {
        $this->w->migrating = false;
        $this->migrate(false);
    }
}