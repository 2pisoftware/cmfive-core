<?php

class ChannelsModuleCest
{
    /**
     * Tests the Channels module.
     *
     * @param CmfiveUI $I
     * @return void
     */
    public function testChannelsModule($I)
    {
        $uniqid = uniqid();
        $channel_name = "channel_name_$uniqid";
        $channel_name_edited = "channel_name_{$uniqid}_edited";
        $web_api_url = "channel_web_api_url_$uniqid";
        $web_api_url_edited = "channel_web_api_url_{$uniqid}_edited";

        $I->wantTo("Verify that Channels can be created, edited and deleted");
        $I->login($I, "admin", "admin");
        $I->amOnPage("channels/listchannels");

        $I->createWebChannel($I, $channel_name, false, false, $web_api_url);
        $I->verifyWebChannel($I, $channel_name, false, false, $web_api_url);
        $I->waitForElementNotVisible("#channelform");

        $I->editWebChannel($I, $channel_name_edited, true, true, $web_api_url_edited);
        $I->verifyWebChannel($I, $channel_name_edited, true, true, $web_api_url_edited);
        $I->waitForElementNotVisible("#channelform");

        $I->deleteChannel($I);

        $I->createEmailChannel($I, $channel_name, false, false, "POP3", "channel_server_url_$uniqid", "channel_username_$uniqid", "channel_password_$uniqid", 563, true, false, true, "channel_folder_directory_$uniqid", "to@$uniqid.com", "from@$uniqid.com", "cc@$uniqid.com", "channel_subject_$uniqid", "channel_body_$uniqid", "Archive", "channel_post_read_data_$uniqid");
        $I->deleteChannel($I);
    }
}
