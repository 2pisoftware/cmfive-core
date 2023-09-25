<?php

namespace Tests\Support\Helper;

class CmfiveChannelsModule extends \Codeception\Module
{
    /**
     * Creates a Web Channel.
     *
     * @param CmfiveUI $I
     * @param string $channel_name
     * @param boolean $is_active
     * @param boolean $do_processing
     * @param string $web_api_url
     * @return void
     */
    public function createWebChannel($I, $channel_name, $is_active, $do_processing, $web_api_url)
    {
        // $I->click("Add Web Channel");
        $I->waitForElement("//button[contains(text(),'Add Web Channel')]");
        $I->click("//button[contains(text(),'Add Web Channel')]");
        // $I->waitForElement("#channelform");
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->fillField("//input[@id='name']", $channel_name);
        $is_active ? $I->checkOption("#is_active") : $I->uncheckOption("#is_active");
        $do_processing ? $I->checkOption("#do_processing") : $I->uncheckOption("#do_processing");
        $I->fillField("#url", $web_api_url);
        $I->click("Save");
    }

    /**
     * Creates an Email Channel.
     *
     * @param CmfiveUI $I
     * @param string $channel_name
     * @param boolean $is_active
     * @param boolean $do_processing
     * @param string $email_protocol
     * @param string $server_url
     * @param string $username
     * @param string $password
     * @param integer $port
     * @param boolean $use_auth
     * @param boolean $verify_peer
     * @param boolean $allow_self_signed_certificates
     * @param string $folder_directory
     * @param string $to
     * @param string $from
     * @param string $cc
     * @param string $subject
     * @param string $body
     * @param string $post_read_action
     * @param string $post_read_data
     * @return void
     */
    public function createEmailChannel($I, $channel_name, $is_active, $do_processing, $email_protocol, $server_url, $username, $password, $port, $use_auth, $verify_peer, $allow_self_signed_certificates, $folder_directory, $to, $from, $cc, $subject, $body, $post_read_action, $post_read_data)
    {
        $I->click("Add Email Channel");
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']"); //"#channelform"
        $I->waitForElement("//input[@id='name']");
        $I->fillField("//input[@id='name']", $channel_name);
        $is_active ? $I->checkOption("#is_active") : $I->uncheckOption("#is_active");
        $do_processing ? $I->checkOption("#do_processing") : $I->uncheckOption("#do_processing");

        $I->selectOption("form select[name=protocol]", strtoupper($email_protocol));
        $I->fillField("#server", $server_url);
        $I->fillField("#s_username", $username);
        $I->fillField("#s_password", $password);
        $I->fillField("#port", $port);
        $use_auth ? $I->checkOption("#use_auth") : $I->uncheckOption("#use_auth");
        $verify_peer ? $I->checkOption("#verify_peer") : $I->uncheckOption("#verify_peer");
        $allow_self_signed_certificates ? $I->checkOption("#allow_self_signed") : $I->uncheckOption("#allow_self_signed");
        $I->fillField("#folder", $folder_directory);

        $I->fillField("#to_filter", $to);
        $I->fillField("#from_filter", $from);
        $I->fillField("#cc_filter", $cc);
        $I->fillField("#subject_filter", $subject);
        $I->fillField("#body_filter", $body);
        $I->selectOption("form select[name=post_read_action]", $post_read_action);
        $I->fillField("#post_read_parameter", $post_read_data);
        $I->click("Save");
    }

    /**
     * Creates a Processor.
     *
     * @param CmfiveUI $I
     * @param string $processor_name
     * @param string $processor_channel
     * @param string $processor_class
     * @return void
     */
    public function createProcessor($I, $processor_name, $processor_channel, $processor_class)
    {
        $I->click("Add Processor");
        // $I->waitForElement("#processor_form");
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->fillField("//input[@id='name']", $processor_name);
        $I->selectOption("form select[name=channel_id]", $processor_channel);
        $I->selectOption("form select[name=processor_class]", $processor_class);
        $I->click("Save");
    }

    /**
     * Edits a Web Channel.
     *
     * @param CmfiveUI $I
     * @param string $channel_name
     * @param boolean $is_active
     * @param boolean $do_processing
     * @param string $web_api_url
     * @return void
     */
    public function editWebChannel($I, $channel_name, $is_active, $do_processing, $web_api_url)
    {
        $I->click("Edit");
        // For some reason waitForElement("#channelform")
        // is fine in the createWebChannel function but not here.
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->fillField("//input[@id='name']", $channel_name);
        $is_active ? $I->checkOption("#is_active") : $I->uncheckOption("#is_active");
        $do_processing ? $I->checkOption("#do_processing") : $I->uncheckOption("#do_processing");
        $I->fillField("#url", $web_api_url);
        $I->click("Save");
    }

        /**
     * Edits an Email Channel.
     *
     * @param CmfiveUI $I
     * @param string $channel_name
     * @param boolean $is_active
     * @param boolean $do_processing
     * @param string $email_protocol
     * @param string $server_url
     * @param string $username
     * @param string $password
     * @param integer $port
     * @param boolean $use_auth
     * @param boolean $verify_peer
     * @param boolean $allow_self_signed_certificates
     * @param string $folder_directory
     * @param string $to
     * @param string $from
     * @param string $cc
     * @param string $subject
     * @param string $body
     * @param string $post_read_action
     * @param string $post_read_data
     * @return void
     */
    public function editEmailChannel($I, $channel_name, $is_active, $do_processing, $email_protocol, $server_url, $username, $password, $port, $use_auth, $verify_peer, $allow_self_signed_certificates, $folder_directory, $to, $from, $cc, $subject, $body, $post_read_action, $post_read_data)
    {
        $I->click("Edit");
        // For some reason waitForElement("#channelform")
        // is fine in the createWebChannel function but not here.
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->fillField("//input[@id='name']", $channel_name);
        $is_active ? $I->checkOption("#is_active") : $I->uncheckOption("#is_active");
        $do_processing ? $I->checkOption("#do_processing") : $I->uncheckOption("#do_processing");

        $I->selectOption("form select[name=protocol]", strtoupper($email_protocol));
        $I->fillField("#server", $server_url);
        $I->fillField("#s_username", $username);
        $I->fillField("#s_password", $password);
        $I->fillField("#port", $port);
        $use_auth ? $I->checkOption("#use_auth") : $I->uncheckOption("#use_auth");
        $verify_peer ? $I->checkOption("#verify_peer") : $I->uncheckOption("#verify_peer");
        $allow_self_signed_certificates ? $I->checkOption("#allow_self_signed") : $I->uncheckOption("#allow_self_signed");
        $I->fillField("#folder", $folder_directory);

        $I->fillField("#to_filter", $to);
        $I->fillField("#from_filter", $from);
        $I->fillField("#cc_filter", $cc);
        $I->fillField("#subject_filter", $subject);
        $I->fillField("#body_filter", $body);
        $I->selectOption("form select[name=post_read_action]", $post_read_action);
        $I->fillField("#post_read_parameter", $post_read_data);
        $I->click("Save");
    }

    /**
     * Deletes a Channel.
     *
     * @param CmfiveUI $I
     * @return void
     */
    public function deleteChannel($I)
    {
        $I->click("Delete");
        $I->acceptPopup();
    }

    /**
     * Deletes a Processor.
     *
     * @param CmfiveUI $I
     * @return void
     */
    public function deleteProcessor($I)
    {
        $I->click("Delete");
    }

    /**
     * Verifies the values of a Web Channel are correct.
     *
     * @param CmfiveUI $I
     * @param string $channel_name
     * @param boolean $is_active
     * @param boolean $do_processing
     * @param string $web_api_url
     * @return void
     */
    public function verifyWebChannel($I, $channel_name, $is_active, $do_processing, $web_api_url)
    {
        $I->click("Edit");
        // $I->waitForElement("#channelform");
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->seeInField("//input[@id='name']", $channel_name);
        $is_active ? $I->seeCheckboxIsChecked("#is_active") : $I->dontSeeCheckboxIsChecked("#is_active");
        $do_processing ? $I->seeCheckboxIsChecked("#do_processing") : $I->dontSeeCheckboxIsChecked("#do_processing");
        $I->seeInField("#url", $web_api_url);
        $I->wait(1);
        $I->click("Cancel");
    }

    /**
     * Verifies the values of an Email Channel are correct.
     *
     * @param CmfiveUI $I
     * @param string $channel_name
     * @param boolean $is_active
     * @param boolean $do_processing
     * @param string $email_protocol
     * @param string $server_url
     * @param string $username
     * @param string $password
     * @param integer $portn
     * @param boolean $use_auth
     * @param boolean $verify_peer
     * @param boolean $allow_self_signed_certificates
     * @param string $folder_directory
     * @param string $to
     * @param string $from
     * @param string $cc
     * @param string $subject
     * @param string $body
     * @param string $post_read_action
     * @param string $post_read_data
     * @return void
     */
    public function verifyEmailChannel($I, $channel_name, $is_active, $do_processing, $email_protocol, $server_url, $username, $password, $port, $use_auth, $verify_peer, $allow_self_signed_certificates, $folder_directory, $to, $from, $cc, $subject, $body, $post_read_action, $post_read_data)
    {
        $I->click("Edit");
        // $I->waitForElement("#channelform");
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->seeInField("//input[@id='name']", $channel_name);
        $is_active ? $I->seeCheckboxIsChecked("#is_active") : $I->dontSeeCheckboxIsChecked("#is_active");
        $do_processing ? $I->seeCheckboxIsChecked("#do_processing") : $I->dontSeeCheckboxIsChecked("#do_processing");

        $I->seeOptionIsSelected("#protocol", $email_protocol);
        $I->seeInField("#server", $server_url);
        $I->seeInField("#s_username", $username);
        $I->seeInField("#s_password", $password);
        $I->seeInField("#port", $port);
        $use_auth ? $I->seeCheckboxIsChecked("#use_auth") : $I->dontSeeCheckboxIsChecked("#use_auth");
        $verify_peer ? $I->seeCheckboxIsChecked("#verify_peer") : $I->dontSeeCheckboxIsChecked("#verify_peer");
        $allow_self_signed_certificates ? $I->seeCheckboxIsChecked("#allow_self_signed") : $I->dontSeeCheckboxIsChecked("#allow_self_signed");
        $I->seeInField("#folder", $folder_directory);

        $I->seeInField("#to_filter", $to);
        $I->seeInField("#from_filter", $from);
        $I->seeInField("#cc_filter", $cc);
        $I->seeInField("#subject_filter", $subject);
        $I->seeInField("#body_filter", $body);

        $I->seeOptionIsSelected("#post_read_action", $post_read_action);
        $I->seeInField("#post_read_parameter", $post_read_data);
        $I->click("Cancel");
    }

    /**
     * Verifies the values of a Processor are correct.
     *
     * @param CmfiveUI $I
     * @param string $processor_name
     * @param string $processor_channel
     * @param string $processor_class
     * @return void
     */
    public function verifyProcessor($I, $processor_name, $processor_channel, $processor_class)
    {
        $I->click("Edit");
        // $I->waitForElement("#processor_form");
        $I->waitForElement("//div[contains(@style,'visible')][@id='cmfive-modal']");
        $I->waitForElement("//input[@id='name']");
        $I->seeInField("//input[@id='name']", $processor_name);
        $I->seeOptionIsSelected("#channel_id", $processor_channel);
        $I->seeOptionIsSelected("#processor_class", $processor_class);
        $I->click("Cancel");
    }
}
