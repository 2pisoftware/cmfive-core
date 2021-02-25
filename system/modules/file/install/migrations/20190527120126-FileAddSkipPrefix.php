<?php

class FileAddSkipPrefix extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("attachment", "skip_path_prefix", "boolean", ["null" => false, "default" => false]);
    }

    public function down()
    {
        $this->removeColumnFromTable("attachment", "skip_path_prefix");
    }
}
