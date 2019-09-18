<?php

class FormAddingIsSingletonToFormModel extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("form_mapping", "is_singleton", "boolean", ["default" => false, "null" => true]);
    }

    public function down()
    {
        $this->removeColumnFromTable("form_mapping", "is_singleton");
    }
}
