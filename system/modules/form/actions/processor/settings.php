<?php 

function settings_GET (Web $w) {
	$w->setLayout(null);
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
    	$w->error('No id found');
    }
    $processor = $w->Form->getEventProcessor($p['id']);
    if (empty($processor->id)) {
        $w->error("Invalid processor ID");
    }
    $form_id = $w->request('form_id');
    if (empty($form_id)) {
    	$w->error('no form id found');
    }
    

    // Instantiate processor
    $class = new $processor->class($w);
    if (method_exists($class, "getSettingsForm")) {
        // Call getSettingsForm
        $form = $class->getSettingsForm($processor->settings, $w);

        if (!empty($form)) {
            $w->out(Html::multiColForm($form, "/form-processor/settings/{$processor->id}?form_id=" . $form_id));
        } else {
            $w->error("Form implementation is empty");
        }
    } else {
    	echo "test";
        // $w->error("Generic form settings function is missing");
    }
}

function settings_POST (Web $w) {
	$w->setLayout(null);
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
    	$w->error('No id found');
    }
    $form_id = $w->request('form_id');
    if (empty($form_id)) {
    	$w->error('no form id found');
    }
    $processor = $w->Form->getEventProcessor($p['id']);
    if (empty($processor->id)) {
        $w->error("Invalid processor ID");
    }


    // Remove CSRF token from request
    $post = $_POST;
    if (!empty($post[CSRF::getTokenID()])) {
        unset($post[CSRF::getTokenID()]);
    }

    $processor->settings = json_encode($post);
    $processor->update();

    $w->msg("Processor settings saved", "/form/show/" . $form_id . '#events');

}