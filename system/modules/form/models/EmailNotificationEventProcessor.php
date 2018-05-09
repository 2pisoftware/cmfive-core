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
        $form = $this->w->Form->getForm($form_instance->form_id);
        if (empty($form)) {
        	return;
        }
        
        $subject = ''; 
        $message = '';
        $event = $processor->getEvent();

        if ($event->type == 'On Created') {
            $subject .= 'New ' . $form->title . 'submitted';
            $message .= 'A new ' . $form->title . ' form has been submitted.<br/>';
        } else if ($event->type == 'On Modified') {
            $subject .= $form->title . ' Modified'; 
            $message .= $form->title . ': ' . $form_instance->id . ' Has been modified.<br/>';
        } else if ($event->type == 'On Deleted') {
            $subject .= $form->title . ' Deleted';
            $message .= $form->title . ': ' . $form_instance->id . ' has been deleted.<br/>';
        }

        //templating may be required. for now just list form fields.
        $form_values = $form_instance->getValuesArray();
        if (!empty($form_values)) {
            foreach ($form_values as $key => $value) {
                
                $message .= '<b>' . $key . ":</b> " . $value . "<br/>";
            }
        }

        //for now just 
        echo $message . "<br/><pre>";
        var_dump($form_values); die;
	}
}