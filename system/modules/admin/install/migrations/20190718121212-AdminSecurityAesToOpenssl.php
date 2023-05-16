<?php
/***
 * Deprecated at 202305 / v5.0 / PHP 8.1
 * This migration nolonger viable, because:
 *  - it errors out depending on case sensitivity settings of MySQL DB
 *  - the error state is intermittent depending on sequence of module installation vs migration execution
 *  - it can't run anyway, from any recent merged branches, because "system->functions->AES_encryption" code has been purged * 
 */

 
class AdminSecurityAesToOpenssl extends CmfiveMigration
{

    // private function migrate($up = true)
    // {

    //     /* STEPS:
    //         Ensuring the client is on PHP 7.0
    //         Ensuring the migration has not already run in the past
    //         When the migration runs, it must decrypt all DbObject fields starting with "s_" using PHPAES, and reencrypt using openssl
    //         Encryption should be verified by decrypting using openssl and verifying that the original content is preserved
    //         The entire encryption operation should be atomic, if one entry fails to re-encrypt for any reason, the entire operation must rollback
    //         If it is successful, we would then need to do a few spot checks in modules that use the encryption service like channels and the hosting module
    //         */

    //     // get only tables with encrypted values

    //     $db = Config::get("database.database");
    //     if (empty($db)) {
    //         $err = 'Database config not set';
    //         LogService::getInstance($this->w)->error($err);
    //         throw new Exception($err);
    //     }

    //     $encryption_key = Config::get('system.encryption.key');
    //     //$encryption_iv = Config::get('system.encryption.iv');

    //     if (empty($encryption_key)) { // || empty($encryption_iv)) {
    //         $err = 'Encryption key/iv is not set';
    //         LogService::getInstance($this->w)->error($err);
    //         throw new Exception($err);
    //     }

    //     /* DB table_name and ObjectName don't always match 
    //     * (e. g. table name: channel_email_option, object name: EmailChannelOption), 
    //     * therefore it is not always possible to get a DB object from table name.
    //     * to do: make them match each other? (e. g. channel_email_option -> ChannelEmailOption)*/
    //     $table = $this->w->db->query("select table_name, column_name from information_schema.columns 
    //         where table_schema='$db' and column_name like 's\_%';")->fetchAll();

    //     if (empty($table)) {
    //         return;
    //     }

    //     $this->w->db->startTransaction();

    //     try {
    //         foreach ($table as $row) {
    //             foreach ($row as $key => $val) {
    //                 if (is_numeric($key)) {
    //                     unset($row[$key]);
    //                 }
    //             }

    //             $passwordSalt = null;

    //             $tableName = $row['table_name'];
    //             $columnName = $row['column_name'];

    //             if ($tableName == "channel_email_option") {
    //                 $passwordSalt = hash("md5", $this->w->moduleConf("channels", "__password"));
    //             } else if ($tableName == "report_connection") {
    //                 $passwordSalt = hash("md5", $this->w->moduleConf("report", "__password"));
    //             } else {
    //                 // Config::set('system.password_salt', md5('override this in your project config'));
    //                 $passwordSalt = Config::get('system.password_salt');
    //             }

    //             $tbl = $this->w->db->query("select id, $columnName from $tableName")->fetchAll();
    //             foreach ($tbl as $r) {
    //                 foreach ($r as $k => $v) {
    //                     if (is_numeric($k)) {
    //                         unset($r[$k]);
    //                     }
    //                 }

    //                 $decrypted = null;
    //                 $encrypted = null;

    //                 if ($up) {
    //                     $decrypted = AESdecrypt($r[$columnName], $passwordSalt);
    //                     $encrypted = SystemSSLencrypt($decrypted);
    //                 } else {
    //                     $decrypted = SystemSSLdecrypt($r[$columnName]);
    //                     $encrypted = AESencrypt($decrypted, $passwordSalt);
    //                 }

    //                 $this->w->db->update($tableName, [$columnName => $encrypted])->where('id', $r['id'])->execute();
    //             }
    //         }

    //         $this->w->db->commitTransaction();
    //     } catch (Exception $e) {
    //         $this->w->db->rollbackTransaction();
    //         throw $e;
    //     }
    // }

    public function up()
    {

        // if ($this->checkBlankContemporyInstall()) {
        //     return $this->w->migrating = true;
        // }

        // if (!($this->checkMigrationClass()['pass'] // config must name this migration
        //     && $this->checkMigrationStatus()['pass'] // named migration(this) must never have run
        //     && $this->checkPHPversion()['pass'] // <5.3 and SSL won't work! >7.0 AES wont work!
        //     && $this->checkSSLKeys()['pass'])) { // no SSL without a key
        //     $err = "System is not suitable for " . get_class($this) . " migration";
        //     LogService::getInstance($this->w)->error($err);
        //     throw new Exception($err);
        // }
        // $this->w->migrating = true;
        // $this->migrate();
        // // finally, if no EXCEPTION --> migration named in config will be in table under 'ADMIN'
        // // DbObject --> detects migration entry & uses SSL wrappers from functions.PHP
    }

    public function down()
    {

        // if ($this->checkBlankContemporyInstall()) {
        //     return $this->w->migrating = true;
        // }

        // if (($this->checkMigrationStatus()['pass'] || (!$this->checkPHPversion()['pass']))) {
        //     // this MUST throw exception, or migration will be registered as rollback,
        //     // regardless that encryption was not reversed
        //     // BECAUSE : down() cannot return failure!
        //     $err = "System cannot rollback " . get_class($this) . " migration";
        //     LogService::getInstance($this->w)->error($err);
        //     throw new Exception($err);
        // } else {
        //     $this->w->migrating = true;
        //     $this->migrate(false);
        // }
    }


    // private function checkMigrationStatus()
    // {
    //     $result = $this->w->db->query("select id from migration 
    //     where module = 'admin' and classname = '" . Config::get('system.encryptionMigration') . "' ; ")->fetchAll();

    //     $checked = [
    //         'pass' => false,
    //         'info' => ""
    //     ];
    //     if (!empty($result)) {
    //         $checked['info'] = "Migration has been run.";
    //     } else {
    //         $checked['pass'] = true;
    //         $checked['info'] = "Migration not previously run.";
    //     }

    //     return $checked;
    // }


    // private function checkMigrationClass()
    // {
    //     $systemClass = Config::get('system.encryptionMigration', "");

    //     $checked = [
    //         'pass' => false,
    //         'info' => ""
    //     ];

    //     if ($systemClass == get_class($this)) {
    //         $checked['pass'] = true;
    //         $checked['info'] = "Migration class is configured.";
    //     } else {
    //         $checked['info'] = "System config does not recognise this encryption migration.";
    //     }

    //     return $checked;
    // }

    // private function checkBlankContemporyInstall()
    // {

    //     // can we ever run SSL?
    //     if (PHP_VERSION_ID < "050300") {
    //         return false;
    //     }

    //     // is the DB empty of encryption?
    //     $db = Config::get("database.database");
    //     if (empty($db)) {
    //         $err = 'Database config not set';
    //         LogService::getInstance($this->w)->error($err);
    //         throw new Exception($err);
    //     }

    //     $table = $this->w->db->query("select table_name, column_name from information_schema.columns 
    //          where table_schema='$db' and column_name like 's\_%';")->fetchAll();

    //     if (empty($table)) {
    //         return true;
    //     }

    //     try {
    //         foreach ($table as $row) {
    //             foreach ($row as $key => $val) {
    //                 if (is_numeric($key)) {
    //                     unset($row[$key]);
    //                 }
    //             }
    //             $tableName = $row['table_name'];
    //             $columnName = $row['column_name'];
    //             $tbl = $this->w->db->query("select id, $columnName from $tableName")->fetchAll();
    //             if (!empty($tbl)) {
    //                 return false;
    //             }
    //         }
    //     } catch (Exception $e) {
    //         throw $e;
    //     }

    //     return true;
    // }

    private function checkPHPversion()
    {

        $serial = PHP_VERSION_ID;
        $version = substr(PHP_VERSION_ID, -4, 2);
        $version = str_replace(substr(PHP_VERSION_ID, -4, 4), "", PHP_VERSION_ID) . "." . $version;

        $checked = [
            'pass' => false,
            'version' => $version,
            'info' => ""
        ];

        if ($serial <= "080000") {
            $checked['info'] = 'Migration can not function for PHP ver. before 8.0';
        } else {
            $checked['info'] = 'Migration is not indicated for PHP ver. after 8.0';
        }

        return $checked;
    }

    private function checkSSLKeys()
    {
        $encryption_key = Config::get('system.encryption.key', null);
        //$encryption_iv = Config::get('system.encryption.iv',null);

        $checked = [
            'pass' => false,
            'info' => ""
        ];

        if (!empty($encryption_key)) { // || empty($encryption_iv))) {
            $checked['pass'] = true;
            $checked['info'] = "SSL Key exists.";
        } else {
            $checked['info'] = "No SSL Key found.";
        }

        return $checked;
    }

    public function preText()
    {
        // $configured = $this->checkMigrationClass();
        // if (!$configured['pass']) {
        //     return "Encryption changes require migration named in config.";
        // } else {
        //     return "Detected PHP ver." . $this->checkPHPversion()['version']
        //         . ", " . $this->checkSSLKeys()['info'];
        // }
        return "Detected PHP ver." . $this->checkPHPversion()['version']
            . ", " . $this->checkSSLKeys()['info'];
    }

    public function postText()
    {
        // if ($this->checkBlankContemporyInstall()) {
        //     return "Encryption will be SSL.";
        // }

        // return "Encryption will " . (
        //     ($this->checkMigrationClass()['pass']
        //         && $this->checkPHPversion()['pass']
        //         && $this->checkSSLKeys()['pass'])
        //     ? "be SSL." : "not change.");

        return "Encryption will be SSL. ".$this->checkPHPversion()['info'];
    }

    public function description()
    {
        // if ($this->checkBlankContemporyInstall()) {
        //     return "New install does not need migration.";
        // }
        // $configured = $this->checkMigrationClass();
        // if (!$configured['pass']) {
        //     return $configured['info'];
        // } else {
        //     return $this->checkPHPversion()['info'] . ", " . $this->checkMigrationStatus()['info'];
        // }
            return "Only SSL encryption is supported for PHP8, no migration is available.";
    }
}
