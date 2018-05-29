<?php

//Send summary of form instance to designated email address

class EmailNotificationEventProcessor extends EventProcessorType {
	
	function getSettingsForm($current_settings = null) {
		if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        $template_select_opotions = $this->w->Template->findTemplates('form', 'event');

        return ["Settings" => [
        	[["Email To Notify", "text", "email_to_notify", @$current_settings->email_to_notify],
            ["Template (Optional)", "select", "template_id", @$current_settings->template_id, $template_select_opotions ]]

        ]];
	}

    //$processor = EventProcessorType
	public function process($form_event,$form_instance) {
		if (empty($form_event->id)) {
            return;
        }
        
        $settings = null;
        if (!empty($form_event->settings)) {
            $settings = json_decode($form_event->settings);
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
        $tmp_message = '';
        

        //generate subject and massage line based on event type
        if ($form_event->event_type == 'On Created') {
            $subject .= 'New ' . $form->title . ' Submitted';
            $message .= 'A new ' . $form->title . ' form has been submitted.<br/><br/>';
            $data['header'] = 'A new ' . $form->title . ' form has been submitted.';
        } else if ($form_event->event_type == 'On Modified') {
            $subject .= $form->title . ' Modified'; 
            $message .= $form->title . ': ' . $form_instance->id . ' Has been modified.<br/><br/>';
            $data['header'] = $form->title . ': ' . $form_instance->id . ' Has been modified.';
        } else if ($form_event->event_type == 'On Deleted') {
            $subject .= $form->title . ' Deleted';
            $message .= $form->title . ': ' . $form_instance->id . ' has been deleted.<br/><br/>';
            $data['header'] = $form->title . ': ' . $form_instance->id . ' has been deleted.';
        }

        //if template is set then use it, otherwise generate simple list of form values
        $template = '';
        $data['fields'] = $form_instance->getValuesForGenericTemplate();
        if (!empty($settings->template_id)) {
            $template = $this->w->Template->getTemplate($settings->template_id);
        }
        if (!empty($template)) {
            $tmp_message .= $this->w->Template->render($template, $data);
        } else {
            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $key=>$value) {
                    //need to add functionality for sub forms
                    if (is_array($value)) {
                        $message .= "<b>" . $key . ":</b> <br/>";
                        foreach ($value as $sub_form) {
                            foreach ($sub_form as $sub_key=>$sub_value) {
                                $message .= "   <b>" . $sub_key . ":</b> " . $sub_value . "<br/>";
                            }
                        }
                    } else {
                        $message .= "<b>" . $key . ":</b> " . $value . "<br/>";
                    }
                    
                }
            }
        }
        

        //check which version of message body to send 
        if (!empty($template)) {
            $final_message = $tmp_message;
        } else {
            $final_message = $message;
        }
        $this->w->Mail->sendMail(
                            $settings->email_to_notify, 
                            Config::get('main.company_support_email'),
                            $subject, $final_message
                        );
	}
}