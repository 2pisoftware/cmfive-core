<?php

use Phinx\Db\Adapter\MysqlAdapter;

class FileAddingAttachmentExifXmpData extends CmfiveMigration
{
    public function up()
    {
        if ($this->hasTable("attachment")) {
            $this->addColumnToTable("attachment", "exif_data", "text", ["limit" => MysqlAdapter::TEXT_MEDIUM, "null" => true, "default" => null]);
            $this->addColumnToTable("attachment", "xmp_data", "text", ["limit" => MysqlAdapter::TEXT_MEDIUM, "null" => true, "default" => null]);
        }
    }

    public function down()
    {
        if ($this->hasTable("attachment")) {
            $this->removeColumnFromTable("attachment", "exif_data");
            $this->removeColumnFromTable("attachment", "xmp_data");
        }
    }
}
