<?php

class AuthAddLoginAttempts extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable('user', 'login_attempts', 'integer', ['default' => 0]);
        $this->addColumnToTable('user', 'is_locked', 'boolean', ['default' => 0]);
    }

    public function down()
    {
        $this->removeColumnFromTable('user', 'login_attempts');
        $this->removeColumnFromTable('user', 'is_locked');
    }
}
