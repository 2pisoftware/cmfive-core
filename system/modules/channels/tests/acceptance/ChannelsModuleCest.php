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

        $I->wantTo("Verify that Channels and Processors can be created, edited and deleted");
        $I->login($I, "admin", "admin");

        // This is a rush ... what if the login is still settling!
        // $I->amOnPage("channels/listchannels");
        // Let's be more patient:
        $I->clickCmfiveNavbar($I, 'Channels', 'List Channels');

        $I->createWebChannel($I, $channel_name, false, false, $web_api_url);
        $I->verifyWebChannel($I, $channel_name, false, false, $web_api_url);
        $I->waitForElementNotVisible("#channelform");

        $I->editWebChannel($I, $channel_name_edited, true, true, $web_api_url_edited);
        $I->verifyWebChannel($I, $channel_name_edited, true, true, $web_api_url_edited);
        $I->waitForElementNotVisible("#channelform");

        $I->amOnPage("channels/listprocessors");

        $processor_name = "processor_name_$uniqid";

        $I->createProcessor($I, $processor_name, $channel_name_edited, "channels.TestProcessor");
        $I->verifyProcessor($I, $processor_name, $channel_name_edited, "channels.TestProcessor");
        $I->waitForElementNotVisible("#processor_form");
        $I->deleteProcessor($I);

        $I->amOnPage("channels/listchannels");
        $I->deleteChannel($I);

        $channel_url = "channel_server_url_$uniqid";
        $channel_url_edited = "channel_server_url_{$uniqid}_edited";
        $channel_username = "channel_username_$uniqid";
        $channel_username_edited = "channel_username_{$uniqid}_edited";
        $channel_password = "channel_password_$uniqid";
        $channel_password_edited = "channel_password_{$uniqid}_edited";
        $channel_folder = "channel_folder_directory_$uniqid";
        $channel_folder_edited = "channel_folder_directory_{$uniqid}_edited";
        $to = "to@$uniqid.com";
        $to_edited = "to_edited@$uniqid.com";
        $from = "from@$uniqid.com";
        $from_edited = "from_edited@$uniqid.com";
        $cc = "cc@$uniqid.com";
        $cc_edited = "cc_edited@$uniqid.com";
        $channel_subject = "channel_subject_$uniqid";
        $channel_subject_edited = "channel_subject_{$uniqid}_edited";
        $channel_body = "channel_body_$uniqid";
        $channel_body_edited = "channel_body_{$uniqid}_edited";
        $channel_post_read_data = "channel_post_read_data_$uniqid";
        $channel_post_read_data_edited = "channel_post_read_data_{$uniqid}_edited";
        $port = "563";
        $I->createEmailChannel($I, $channel_name, false, false, "POP3", $channel_url, $channel_username, $channel_password, $port, true, false, true, $channel_folder, $to, $from, $cc, $channel_subject, $channel_body, "Archive", $channel_post_read_data);
        $I->verifyEmailChannel($I, $channel_name, false, false, "POP3", $channel_url, $channel_username, $channel_password, $port, true, false, true, $channel_folder, $to, $from, $cc, $channel_subject, $channel_body, "Archive", $channel_post_read_data);
        $I->waitForElementNotVisible("#channelform");
        $I->editEmailChannel($I, $channel_name_edited, true, true, "IMAP", $channel_url_edited, $channel_username_edited, $channel_password_edited, $port, false, true, false, $channel_folder_edited, $to_edited, $from_edited, $cc_edited, $channel_subject_edited, $channel_body_edited, "Move to Folder", $channel_post_read_data_edited);
        $I->verifyEmailChannel($I, $channel_name_edited, true, true, "IMAP", $channel_url_edited, $channel_username_edited, $channel_password_edited, $port, false, true, false, $channel_folder_edited, $to_edited, $from_edited, $cc_edited, $channel_subject_edited, $channel_body_edited, "Move to Folder", $channel_post_read_data_edited);
        $I->waitForElementNotVisible("#channelform");
        $I->deleteChannel($I);
    }
}
