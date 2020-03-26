<?php

class FormIncreasingFormFieldMetadataMetaValueMaxLength extends CmfiveMigration
{
    public function up()
    {
        $this->changeColumnInTable("form_field_metadata", "meta_value", "text");
    }

    public function down()
    {
        $this->changeColumnInTable("form_field_metadata", "meta_value", "string");
    }
}
