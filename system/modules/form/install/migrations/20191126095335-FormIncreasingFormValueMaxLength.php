<?php

class FormIncreasingFormValueMaxLength extends CmfiveMigration
{

    public function up()
    {
        $this->changeColumnInTable("form_value", "value", "text", ["null" => true, "default" => null]);
    }

    public function down()
    {
        $this->changeColumnInTable("form_value", "value", "VARCHAR(1024)", ["null" => true, "default" => null]);
    }
}
