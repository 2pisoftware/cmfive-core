<?php

//Send summary of form instance to designated email address

class EmailNotificationEventProcessor extends EventProcessorType {
	
	function getSettingsForm($current_settings = null) {
		if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        return ["Settings" => [
        	[["Email To Notify", "text", "email_to_notify", @$current_settings->email_to_notify]]
        ]];
	}

	public function process($processor,$form_instance) {
		if (empty($processor->id)) {
            return;
        }
        
        $settings = null;
        if (!empty($processor->settings)) {
            $settings = json_decode($processor->settings);
        }

        if (empty($form_instance)) {
        	return;
        }

        //check if form has a summary template
        $form = $w->Form->getForm($form_instance->form_id);
        if (empty($form)) {
        	return;
        }
        if (!empty($from->summary_template)) {
        	//use the form summary template

        } else {
        	//generate list of form fields and values from sql view
        }

        //for now just 
        echo "test"; die;
	}
}