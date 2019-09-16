<?php

class FormAddingIsSingletonToFormModel extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("form", "is_singleton", "boolean", ["default" => false, "null" => true]);
    }

    public function down()
    {
        $this->removeColumnFromTable("form", "is_singleton");
    }
}
