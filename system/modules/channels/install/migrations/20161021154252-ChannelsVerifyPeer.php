<?php

class ChannelsVerifyPeer extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('channel_email_option', 'verify_peer', 'boolean', ['default' => true]);
        $this->addColumnToTable('channel_email_option', 'allow_self_signed', 'boolean', ['default' => false]);
    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable('channel_email_option', 'verify_peer');
        $this->removeColumnFromTable('channel_email_option', 'allow_self_signed');
    }
}
