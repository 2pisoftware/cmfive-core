<?php

class AuthAddProfileImageToContact extends CmfiveMigration
{

    public function up()
    {
        // UP - @TODO: Set blob medium correctly?
        $this->addColumnToTable("contact", "profile_img", "blob", ["null" => true, "limit" => 16777215]);
    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("contact", "profile_img");
    }
}
