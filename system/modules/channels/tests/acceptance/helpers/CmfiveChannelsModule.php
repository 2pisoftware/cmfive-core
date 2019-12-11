<?php

namespace Helper;

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
        $I->click("Add Web Channel");
        $I->waitForElement("#channelform");
        $I->fillField("#name", $channel_name);

        if ($is_active) {
            $I->checkOption("#is_active");
        } else {
            $I->uncheckOption("#is_active");
        }

        if ($do_processing) {
            $I->checkOption("#do_processing");
        } else {
            $I->uncheckOption("#do_processing");
        }

        $I->fillField("#url", $web_api_url);
        $I->click("Save");
    }

    /**
     * Created an Email Channel.
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
        $I->waitForElement("#channelform");
        $I->fillField("#name", $channel_name);

        if ($is_active) {
            $I->checkOption("#is_active");
        } else {
            $I->uncheckOption("#is_active");
        }

        if ($do_processing) {
            $I->checkOption("#do_processing");
        } else {
            $I->uncheckOption("#do_processing");
        }

        $I->selectOption("form select[name=protocol]", strtoupper($email_protocol));
        $I->fillField("#server", $server_url);
        $I->fillField("#s_username", $username);
        $I->fillField("#s_password", $password);
        $I->fillField("#port", $port);

        if ($use_auth) {
            $I->checkOption("#use_auth");
        } else {
            $I->uncheckOption("#use_auth");
        }

        if ($verify_peer) {
            $I->checkOption("#verify_peer");
        } else {
            $I->uncheckOption("#verify_peer");
        }

        if ($allow_self_signed_certificates) {
            $I->checkOption("#allow_self_signed");
        } else {
            $I->uncheckOption("#allow_self_signed");
        }


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
        $I->wait(1);
        $I->fillField("#name", $channel_name);

        if ($is_active) {
            $I->checkOption("#is_active");
        } else {
            $I->uncheckOption("#is_active");
        }

        if ($do_processing) {
            $I->checkOption("#do_processing");
        } else {
            $I->uncheckOption("#do_processing");
        }

        $I->fillField("#url", $web_api_url);
        $I->click("Save");
    }

    /**
     * Deletes a Web Channel.
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
        $I->waitForElement("#channelform");
        $I->seeInField("#name", $channel_name);

        if ($is_active) {
            $I->seeCheckboxIsChecked("#is_active");
        } else {
            $I->dontSeeCheckboxIsChecked("#is_active");
        }

        if ($do_processing) {
            $I->seeCheckboxIsChecked("#do_processing");
        } else {
            $I->dontSeeCheckboxIsChecked("#do_processing");
        }

        $I->seeInField("#url", $web_api_url);
        $I->click("Cancel");
    }
}
