<?php

class FormAddingIsSingletonToFormMappingModels extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("form_mapping", "is_singleton", "boolean", ["default" => false, "null" => true]);
        $this->addColumnToTable("form_application_mapping", "is_singleton", "boolean", ["default" => false, "null" => true]);
    }

    public function down()
    {
        $this->removeColumnFromTable("form_mapping", "is_singleton");
        $this->removeColumnFromTable("form_application_mapping", "is_singleton");
    }
}
