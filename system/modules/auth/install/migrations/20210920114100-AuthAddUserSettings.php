<?php

class AuthAddUserSettings extends CmfiveMigration
{
    public function up()
    {
        if (!$this->hasTable('user_setting')) {
            $this->tableWithId('user_setting')
                ->addBigIntegerColumn('user_id')
                ->addStringColumn('setting_key', false)
                ->addStringColumn('setting_value', true, 1024)
                ->addCmfiveParameters()
                ->create();
        }
    }

    public function down()
    {
        if ($this->hasTable('user_setting')) {
            $this->dropTable('user_setting');
        }
    }
}
