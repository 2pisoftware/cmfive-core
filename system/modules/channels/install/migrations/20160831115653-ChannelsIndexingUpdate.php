<?php

class ChannelsIndexingUpdate extends CmfiveMigration
{
    public function up()
    {
        $this->addIndexToTable('channel', 'is_active');
        $this->addIndexToTable('channel', 'notify_user_id');
        $this->addIndexToTable('channel', 'do_processing');
        $this->addIndexToTable('channel', 'is_deleted');

        $this->addIndexToTable('channel_email_option', 'channel_id');
        $this->addIndexToTable('channel_email_option', 'is_deleted');

        $this->addIndexToTable('channel_message', 'channel_id');
        $this->addIndexToTable('channel_message', 'is_processed');
        $this->addIndexToTable('channel_message', 'is_deleted');

        $this->addIndexToTable('channel_message_status', 'message_id');
        $this->addIndexToTable('channel_message_status', 'processor_id');
        $this->addIndexToTable('channel_message_status', 'is_successful');
        $this->addIndexToTable('channel_message_status', 'is_deleted');

        $this->addIndexToTable('channel_processor', 'channel_id');
        $this->addIndexToTable('channel_processor', 'is_deleted');

        $this->addIndexToTable('channel_web_option', 'channel_id');
        $this->addIndexToTable('channel_web_option', 'is_deleted');
    }

    public function down()
    {
        $this->removeIndexFromTable('channel', 'is_active');
        $this->removeIndexFromTable('channel', 'notify_user_id');
        $this->removeIndexFromTable('channel', 'do_processing');
        $this->removeIndexFromTable('channel', 'is_deleted');

        $this->removeIndexFromTable('channel_email_option', 'channel_id');
        $this->removeIndexFromTable('channel_email_option', 'is_deleted');

        $this->removeIndexFromTable('channel_message', 'channel_id');
        $this->removeIndexFromTable('channel_message', 'is_processed');
        $this->removeIndexFromTable('channel_message', 'is_deleted');

        $this->removeIndexFromTable('channel_message_status', 'message_id');
        $this->removeIndexFromTable('channel_message_status', 'processor_id');
        $this->removeIndexFromTable('channel_message_status', 'is_successful');
        $this->removeIndexFromTable('channel_message_status', 'is_deleted');

        $this->removeIndexFromTable('channel_processor', 'channel_id');
        $this->removeIndexFromTable('channel_processor', 'is_deleted');

        $this->removeIndexFromTable('channel_web_option', 'channel_id');
        $this->removeIndexFromTable('channel_web_option', 'is_deleted');
    }
}
