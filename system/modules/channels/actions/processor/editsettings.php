<?php

function editsettings_GET(Web $w)
{
    $w->setLayout(null);
    $p = $w->pathMatch("id");
    $id = $p["id"];

    if (!$id) {
        $w->error("Missing parameter in request", "/channels/listprocessors");
    }

    $processor = ChannelService::getInstance($w)->getProcessor($id);
    if (empty($processor->id)) {
        $w->error("Invalid processor ID", "/channels/listprocessors");
    }

    // Instantiate processor
    $class = new $processor->class($w);
    if (method_exists($class, "getSettingsForm")) {
        // Call getSettingsForm
        $form = $class->getSettingsForm($processor->settings, $w);

        if (!empty($form)) {
            $w->out(HtmlBootstrap5::multiColForm($form, "/channels-processor/editsettings/{$processor->id}"));
        } else {
            $w->error("Form implementation is empty", "/channels/listprocessors");
        }
    } else {
        $w->error("Generic form settings function is missing", "/channels/listprocessors");
    }
}

function editsettings_POST(Web $w)
{
    $w->setLayout(null);
    $p = $w->pathMatch("id");
    $id = $p["id"];

    if (!$id) {
        $w->error("Missing parameter in request", "/channels/listprocessors");
    }

    // Remove CSRF token from request
    $post = $_POST;
    if (!empty($post[CSRF::getTokenID()])) {
        unset($post[CSRF::getTokenID()]);
    }

    $processor = ChannelService::getInstance($w)->getProcessor($id);
    if (empty($processor->id)) {
        $w->error("Invalid processor ID", "/channels/listprocessors");
    }

    $processor->settings = json_encode($post);
    $processor->update();

    $w->msg("Processor settings saved", "/channels/listprocessors");
}
