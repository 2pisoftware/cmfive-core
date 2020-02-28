<?php

class AuthAddingMfaSupport extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("user", "is_mfa_enabled", "boolean", ["null" => false, "default" => false]);
        $this->addColumnToTable("user", "mfa_secret", "string", ["null" => true]);
    }

    public function down()
    {
        $this->removeColumnFromTable("user", "is_mfa_enabled");
        $this->removeColumnFromTable("user", "mfa_secret");
    }
}
