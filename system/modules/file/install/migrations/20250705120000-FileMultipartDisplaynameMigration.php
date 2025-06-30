<?php

class FileMultipartDisplaynameMigration extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable("file_s3_object", "display_name", "text", ["null" => true]);
    }

    public function down()
    {
        $this->removeColumnFromTable("file_s3_object", "display_name");
    }
}
