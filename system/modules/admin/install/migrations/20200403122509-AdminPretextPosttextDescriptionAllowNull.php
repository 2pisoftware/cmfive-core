<?php

class AdminPretextPosttextDescriptionAllowNull extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("migration", "pretext", "string", ["default" => null, "null" => true]);
        $this->addColumnToTable("migration", "posttext", "string", ["default" => null, "null" => true]);
        $this->addColumnToTable("migration", "description", "string", ["default" => null, "null" => true]);
    }

    public function down()
    {
    }
}
