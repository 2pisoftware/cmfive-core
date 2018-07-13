<?php 

function settings_GET (Web $w) {
	$w->setLayout(null);
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
    	$w->error('No id found');
    }
    $event = $w->Form->getFormEvent($p['id']);
    if (empty($event->id)) {
        $w->error("Invalid Form Event ID");
    }
    $form_id = $w->request('form_id');
    if (empty($form_id)) {
    	$w->error('no form id found');
    }
    

    // Instantiate processor
    $class = new $event->class($w);
    if (method_exists($class, "getSettingsForm")) {
        // Call getSettingsForm
        $form = $class->getSettingsForm($event->settings, $w);

        if (!empty($form)) {
            $w->out(Html::multiColForm($form, "/form-event/settings/{$event->id}?form_id=" . $form_id));
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
    $event = $w->Form->getFormEvent($p['id']);
    if (empty($event->id)) {
        $w->error("Invalid form event ID");
    }


    // Remove CSRF token from request
    $post = $_POST;
    if (!empty($post[CSRF::getTokenID()])) {
        unset($post[CSRF::getTokenID()]);
    }

    $event->settings = json_encode($post);
    $event->update();

    $w->msg("Form Event settings saved", "/form/show/" . $form_id . '#events');

}