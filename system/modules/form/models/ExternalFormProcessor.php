<?php

class ExternalFormProcessor extends ProcessorType {

    public function getSettingsForm($current_settings = null) {
        // Check if json
        if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        return ["Settings" => [
        	[["Target Form Application", "select", "target_application_id", @$current_settings->target_application_id, $this->w->FormApplication->getFormApplications()]],
            [["Target Form", "select", "target_form_id", @$current_settings->target_form_id, $this->w->Form->getForms()]],
        ]];
    }

    /**
     * create HelpdeskTicket from an email
     * 
     * @see ProcessorType::process()
     */
    public function process($processor) {
        if (empty($processor->id)) {
            return;
        }
        
        $settings = null;
        if (!empty($processor->settings)) {
            $settings = json_decode($processor->settings);
        }

        if (empty($settings->target_application_id)) {
        	$processor->w->Log->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Invalid settings for processor, application must be selected");
        	return;
        }

        $application = $processor->w->FormApplication->getFormApplication($settings->target_application_id);
        if (empty($application->id)) {
        	$processor->w->Log->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Cannot find application with ID: " . $settings->target_application_id);
        	return;
        }

        if (empty($settings->target_form_id)) {
        	$processor->w->Log->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Invalid settings for processor, form must be selected");
        	return;
        }

        $form = $processor->w->Form->getForm($settings->target_form_id);
        if (empty($form->id)) {
        	$processor->w->Log->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Cannot find form with ID: " . $settings->target_form_id);
        	return;
        }

        // Get the object that form is mapped to
        $messages = $processor->w->Channel->getNewMessages($processor->channel_id, $processor->id);
        if (!empty($messages)) {
            foreach ($messages as $message) {
            	
            	$messagestatus = $message->messagestatus; //$processor->w->Channel->getMessageStatus($message->id, $processor->id);
                if (empty($messagestatus)) {
                    $messagestatus = new ChannelMessageStatus($processor->w);
                    $messagestatus->message_id = $message->id;
                    $messagestatus->processor_id = $processor->id;
                }

                $messagestatus->message = '';

            	// Get attached form
            	$attachments = $processor->w->File->getAttachments($message, (!empty($message->id) ? $message->id : null));
				if (!empty($attachments)) {
					foreach($attachments as $attachment) {
						if ($attachment->mimetype == "text/xml") {

							try {
								// Create form instance
								$instance = new FormInstance($processor->w);
								$instance->form_id = $form->id;
								$instance->object_class = get_class($application);
								$instance->object_id = $application->id;
								$instance->insert();

								// Loop over instance fields and grab them from the XML
								$xml = file_get_contents(FILE_ROOT . $attachment->fullpath);
								
								if (empty($xml)) {
									$processor->w->Log->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Cannot validate XML attachment for form: " . $form->title);
									$messagestatus->message = "Cannot validate XML attachment for form: " . $form->title;
									$messagestatus->is_successful = 0;
									$messagestatus->insertOrUpdate();

	        						break;
								}

								// Persist values to instance
								$xml_doc = simplexml_load_string($xml);

								$fields = $form->getFields();
								if (!empty($fields)) {
									foreach($fields as $field) {
										// Try and get a value from XML
										$xml_value = $this->getFirstOf($xml_doc, $field->technical_name);

										if ($xml_value !== null) {
											$form_value = new FormValue($processor->w);
											$form_value->form_instance_id = $instance->id;
											$form_value->form_field_id = $field->id;
											$form_value->value = $xml_value;
											$form_value->insert();
										}
									}
								}

								// Mark message as complete
								$messagestatus->is_successful = 1;
								$messagestatus->insertOrUpdate();

							} catch(Exception $e) {
								$processor->w->Log->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Exception: " . $e->getMessage());
							}

							break;
						}
					}
				} else {
					$messagestatus->message = "No attachments found";

					// Mark message as complete
					$messagestatus->is_successful = 1;
					$messagestatus->insertOrUpdate();
				}
            }
        }
    }

    private function getFirstOf($xml, $attr, $refnode = null) {
    	 if ($refnode == null) {
            $ret = $xml->xpath('//' . $attr . '[1]/text()');
        } else {
            $ret = $refnode->xpath('.//' . $attr . '[1]/text()');
        }
        if (!empty($ret)) {
            return (string) $ret[0];
        }
        return null;
    }
}