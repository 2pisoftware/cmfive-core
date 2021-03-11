<?php

class InsightsInsightsInitialMigration extends CmfiveMigration
{

    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
            ->setType('biginteger')
            ->setIdentity(true);

        if (!$this->hasTable("insight_members")) { //it can be helpful to check that the table name is not used
            $this->table("insight_members", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addColumn('insight_class_name', 'string')
                ->addColumn('user_id', 'biginteger')
                ->addColumn('type', 'string')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }
    }


    public function down()
    {
        // DOWN
        $this->hasTable('insight_members') ? $this->dropTable('insight_members') : null;
    }

    public function preText()
    {
        return null;
    }

    public function postText()
    {
        return null;
    }

    public function description()
    {
        return null;
    }
}
