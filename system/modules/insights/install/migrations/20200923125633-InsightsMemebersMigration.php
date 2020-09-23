<?php

class InsightsMemebersMigration extends CmfiveMigration {

    public function up() {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

                (!$this->hasTable("members_table")) { //it can be helpful to check that the table name is not used
                $this->table("members_table", [ // table names should be appended with 'ModuleName_'
                    "id" => false,
                    "primary_key" => "id"
                ])->addColumn($column) // add the id column
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create(); 
    }

    public function down() {
        // DOWN
        $this->hasTable('members_table') ? $this->dropTable('members_table') : null;
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
