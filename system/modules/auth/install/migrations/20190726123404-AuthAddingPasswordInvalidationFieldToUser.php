<?php

class AuthAddingPasswordInvalidationFieldToUser extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("user", "is_password_invalid", "boolean", ["default" => false]);
    }

    public function down()
    {
        $this->removeColumnFromTable("user", "is_password_invalid");
    }

    public function description()
    {
        return "Adds is_password_invalid field to the user table.";
    }
}
