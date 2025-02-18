<?php

class FileS3MultipartMigration extends CmfiveMigration
{
    public function up()
    {
        if (!$this->hasTable("file_s3_object"))
        {
            $this->tableWithId("file_s3_object")
                ->addColumn("upload_id", "string")
                ->addColumn("bucket", "string")
                ->addColumn("key_path", "string")
                ->addColumn("mime", "string")
                ->addColumn("parent_table", "string", ["null" => true])
                ->addColumn("parent_id", "string", ["null" => true])
                ->addCmfiveParameters()
                ->create();
        }
    }

    public function down()
    {
        $this->dropTable("file_s3_object");
    }
}
