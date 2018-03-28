<?php

class AesToOpenssl extends CmfiveMigration {
    
    private function migrate() {
        $availableTables = $this->w->db->getAvailableTables();
        foreach ($availableTables as $tableName) {
            $table = $this->w->db->query("select * from " . $tableName)->fetchAll();
            // $classname = str_replace('_', '', ucwords($tableName, '_'));
            /*if (class_exists($classname)) {
                $objects = $this->w->Admin->getObjects($classname);
            }*/
            
            foreach ($table as $row) {
                foreach ($row as $k => $v) {
                    if (strpos($k, "s_") === 0) {
                        $decrypted = AESdecrypt($v, Config::get('system.password_salt'));
                        $encrypted = openssl_encrypt($decrypted, "AES-256-CBC", "lvewfopkkzsxnjjws1zc66rucgh8lt", 0, "ash17hr39fu12cva");
                        $row[$k] = $encrypted;
                        $this->w->db->update($tableName, $row)->execute();
                    }
                }
            }
            
            /*foreach ($objects as $object) {
                if ($object) {
                    $object->decrypt();
                    $object->update();
                    if (!$object->update()) {
                        $this->down();
                        $this->w->ctx("migrationMessage", '<div data-alert class="alert-box alert radius"><h3 class="text-center"><strong style="color: #ffffff;">Migration from AES to openSSL was not successful</strong></h3><a href="#" class="close">&times;</a></div>');
                        return;
                    }
                }
            }*/
        }
    }

    public function up() {
        $this->w->migrating = true;
        $this->migrate();
        $this->w->ctx("migrationMessage", '<div data-alert class="alert-box success radius"><h3 class="text-center"><strong style="color: #ffffff;">Migration from AES to openSSL was successful</strong></h3><a href="#" class="close">&times;</a></div>');
    }

    public function down() {
        $this->w->migrating = false;
        $this->migrate();
    }
}