<?php

//Send summary of form instance to designated email address

class EmailNotificationEventProcessor extends EventProcessorType {
	
	function getSettingsForm($current_settings = null) {
		if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        $template_select_options = TemplateService::getInstance($this->w)->findTemplates('form', 'event');

        return ["Settings" => [
        	[["Email To Notify", "text", "email_to_notify", @$current_settings->email_to_notify],
            ["Template (Optional)", "select", "template_id", @$current_settings->template_id, $template_select_options ]]

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
        $form = FormService::getInstance($this->w)->getForm($form_instance->form_id);
        if (empty($form)) {
        	return;
        }
        
        $subject = ''; 
        $message = '';
        $tmp_message = '';
        $attachments = [];

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
            $template = TemplateService::getInstance($this->w)->getTemplate($settings->template_id);
        }
        if (!empty($template)) {
            $tmp_message .= TemplateService::getInstance($this->w)->render($template, $data);
        } else {
            if (!empty($data['fields'])) {
                foreach ($data['fields'] as $key=>$value) {
                    //handle attachments
                    if ($key == 'attachments') {
                        
                        foreach($value as $field_name=>$att) {
                            $attachment_names = [];
                            $message .= "<b>" . $field_name . ":</b> "; // . $value . "<br/>";
                            foreach($att as $attachment) {
                                $attachment_names[] = basename($attachment);
                            }
                            $attachments = array_merge($attachments,$att);
                            
                            $message .= implode(', ', $attachment_names);
                            $message .= "</br>";
                        }
                    }
                    //need to add functionality for sub forms
                    elseif (is_array($value)) {
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
        MailService::getInstance($this->w)->sendMail(
                            $settings->email_to_notify, 
                            Config::get('main.company_support_email'),
                            $subject, $final_message,null,null,$attachments
                        );
	}
}