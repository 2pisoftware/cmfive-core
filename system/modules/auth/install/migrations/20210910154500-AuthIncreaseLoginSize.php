<?php

class AuthIncreaseLoginSize extends CmfiveMigration
{
    public function up()
    {
        $this->changeColumnInTable('user', 'login', 'string', ['null' => false, 'limit' => 225]);
    }

    public function down()
    {
    }
}
