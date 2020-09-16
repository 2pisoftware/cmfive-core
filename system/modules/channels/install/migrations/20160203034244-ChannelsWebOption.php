<?php

class ChannelsWebOption extends CmfiveMigration
{
    public function up()
    {
        $column = parent::Column();
        $column->setName('id')
            ->setType('biginteger')
            ->setIdentity(true);

        if (!$this->hasTable("channel_web_option")) {
            $this->table("channel_web_option", [
                'id' => false,
                'primary_key' => 'id'
            ])->addColumn($column)
                ->addColumn("channel_id", "biginteger")
                ->addColumn("url", "string", ["limit" => 1024])
                ->addCmfiveParameters()
                ->create();
        }
    }

    public function down()
    {
        $this->hasTable("channel_web_option") ? $this->dropTable("channel_web_option") : null;
    }
}
