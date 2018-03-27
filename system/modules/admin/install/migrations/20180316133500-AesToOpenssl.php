<?php

class AesToOpenssl extends CmfiveMigration {

    public function up() {
        $this->w->migrating = true;
        $availableTables = $this->w->db->getAvailableTables();
        foreach ($availableTables as $tableName) {
            $table = $this->w->db->get($tableName);
            $data = $table->fetchAll();
            
            foreach ($data as $row) {
                foreach ($row as $k => $v) {
                    if (strpos($k, "s_") === 0) {
                        $decrypted = AESdecrypt($v, Config::get('system.password_salt'));
                        $encrypted = openssl_encrypt($decrypted, "AES-256-CBC", Config::get('system.password_salt'), 0, "ash17hr39fu12cva");
                        $row[$k] = $encrypted;
                        $this->w->db->update($tableName, $row)->execute();
                    }
                }
            }
        }

        $this->w->ctx("migrationMessage", '<div data-alert class="alert-box success radius"><h3 class="text-center"><strong style="color: #ffffff;">Migration from AES to openSSL was successful</strong></h3><a href="#" class="close">&times;</a></div>');
    }

    public function down() {
        $this->w->migrating = false;
        $availableTables = $this->w->db->getAvailableTables();
        foreach ($availableTables as $tableName) {
            $cname = str_replace('_', '', ucwords($tableName, '_'));
            $classname = lcfirst($cname);
            $object = $this->w->getObject($classname);

            if (!empty($object)) {
                $object->decrypt();
                $object->update();
            }
        }
    }
}