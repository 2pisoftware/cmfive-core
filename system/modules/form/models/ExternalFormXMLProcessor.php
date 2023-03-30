<?php

class ExternalFormXMLProcessor extends ProcessorType
{

    public function getSettingsForm($current_settings = null)
    {
        // Check if json
        if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        return ["Settings" => [
            [["Target Form Application", "select", "target_application_id", @$current_settings->target_application_id, FormService::getInstance($this->w)->getFormApplications()]],
            [["Target Form", "select", "target_form_id", @$current_settings->target_form_id, FormService::getInstance($this->w)->getForms()]],
        ]];
    }

    /**
     * create HelpdeskTicket from an email
     *
     * @see ProcessorType::process()
     */
    public function process($processor)
    {

        if (empty($processor->id)) {
            return;
        }

        $settings = null;
        if (!empty($processor->settings)) {
            $settings = json_decode($processor->settings);
        }

        if (empty($settings->target_application_id)) {
            LogService::getInstance($processor->w)->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Invalid settings for processor, application must be selected");
            return;
        }

        $application = FormApplicationService::getInstance($processor->w)->getFormApplication($settings->target_application_id);
        if (empty($application->id)) {
            LogService::getInstance($processor->w)->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Cannot find application with ID: " . $settings->target_application_id);
            return;
        }

        if (empty($settings->target_form_id)) {
            LogService::getInstance($processor->w)->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Invalid settings for processor, form must be selected");
            return;
        }

        $form = FormService::getInstance($processor->w)->getForm($settings->target_form_id);
        if (empty($form->id)) {
            LogService::getInstance($processor->w)->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Cannot find form with ID: " . $settings->target_form_id);
            return;
        }

        // Get the object that form is mapped to
        $messages = $processor->getNewOrFailedMessages(); // w->Channel->getNewOrFailedMessages($processor->channel_id, $processor->id);

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
                $attachments = FileService::getInstance($processor->w)->getAttachments($message);

                if (!empty($attachments)) {
                    $non_standard_attachments = array_filter($attachments, function ($attachment) {
                        return $attachment->mimetype != "application/xml" && $attachment->mimetype != "text/xml" && $attachment->type_code == "channel_email_attachment";
                    });

                    foreach ($attachments as $attachment) {
                        if ($attachment->mimetype == "application/xml" || $attachment->mimetype == "text/xml") {
                            try {
                                // Load XML
                                $xml = file_get_contents(FILE_ROOT . $attachment->fullpath);

                                if (empty($xml)) {
                                    LogService::getInstance($processor->w)->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Cannot validate XML attachment for form: " . $form->title);
                                    $messagestatus->message = "Cannot validate XML attachment for form: " . $form->title;
                                    $messagestatus->is_successful = 0;
                                    $messagestatus->insertOrUpdate();

                                    break;
                                }

                                // Converted all tags in XML to lower case as our technical names in FormFields WERE all lower case
                                /*
                                $xml = preg_replace_callback("/(<\/?\w+)(.*?>)/", function ($m) {
                                return strtolower($m[1]) . $m[2];
                                }, $xml);
                                 */

                                // Persist values to instance
                                $xml_doc = simplexml_load_string($xml);

                                $return_value = $this->attachFormFromXMLToObject($xml_doc, $form, $application, $non_standard_attachments);

                                // Mark message as complete
                                $messagestatus->is_successful = 1;
                                $messagestatus->insertOrUpdate();

                            } catch (Exception $e) {
                                LogService::getInstance($processor->w)->setLogger("EXTERNAL_FORM_PROCESSOR")->error("Exception: " . $e->getMessage());
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

    private function attachFormFromXMLToObject($current_document, $form, $object, $non_standard_attachments)
    {
        $instance = null;
        $is_existing_instance = false;
        $unique_id_field = $form->getUniqueIdField();

        if (!empty($unique_id_field)) {
            // Get unique id field value from XML
            $unique_field_value = $current_document->xpath('//' . $unique_id_field->technical_name . '[1]/text()');
            if (!empty($unique_field_value) && is_array($unique_field_value)) {
                $instance = $form->getFormInstanceByUniqueIdentifierFieldValue((string) $unique_field_value[0]);
                $is_existing_instance = !empty($instance->id);
            }
        };

        // Create/Find form instance
        if (!$is_existing_instance) {
            $instance = new FormInstance($form->w);
            $instance->form_id = $form->id;
            $instance->object_class = get_class($object);
            $instance->object_id = $object->id;
            $instance->insert();
        }

        $fields = $form->getFields();
        $result = null;
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $xml_value = '';

                // Try and get a value from XML
                switch ($field->type) {
                    case "latlong": {
                        // Expect the names to start with "lat" and "lon" under the field xpath
                        $latitude = $current_document->xpath('//' . $field->technical_name . '//*[starts-with(name(), "lat")]');
                        $longitude = $current_document->xpath('//' . $field->technical_name . '//*[starts-with(name(), "lon")]');

                        if (!empty($latitude[0])) {
                            $xml_value .= (string) $latitude[0];
                        }

                        if (!empty($longitude[0])) {
                            if (!empty($xml_value)) {
                                $xml_value .= ', ';
                            }

                            $xml_value .= (string) $longitude[0];
                        }

                        $this->createFormValue($form->w, $is_existing_instance, $instance, $field, $xml_value);
                        break;
                    };
                    case "attachment":{
                        $xml_path_attachments = $current_document->xpath('//' . $field->technical_name . '//photo');
                        if (empty($xml_path_attachments)) {
                            $xml_path_attachments = $current_document->xpath('//' . $field->technical_name);
                        }
                        if (!empty($xml_path_attachments) && !empty($non_standard_attachments)) {
                            foreach ($xml_path_attachments as $xml_path_attachment) {
                                foreach ($non_standard_attachments as $non_standard_attachment) {
                                    if ($non_standard_attachment->filename == $xml_path_attachment) {
                                        $xml_value .= (!empty($xml_value) ? ',' : '') . $non_standard_attachment->id;
                                        break;
                                    }
                                }
                            }
                        }
                        $this->createFormValue($form->w, $is_existing_instance, $instance, $field, $xml_value);
                        break;
                    };
                    case "subform":{
                        // Subform is a special case because the instances attach to the form value so it needs to exist first
                        $form_value = $this->createFormValue($form->w, $is_existing_instance, $instance, $field, '');

                        $result = $current_document->xpath('//' . $field->technical_name);

                        // Get form from metadata
                        $metadata = $field->getMetadata();
                        if (empty($metadata)) {
                            // Handle issue with missing metadata
                            // NOTE: is equiv to "continue;" as the switch&for blocks are immediately nested
                            // with closing '} ... }'
                            break;
                        }

                        $subform = null;
                        foreach ($metadata as $metadata_row) {
                            if ($metadata_row->meta_key === "associated_form") {
                                $subform = FormService::getInstance($form->w)->getForm($metadata_row->meta_value);
                            }
                        }

                        if (empty($subform)) {
                            // Handle issue with missing form
                            // NOTE: is equiv to "continue;" as the switch&for blocks are immediately nested
                            // with closing '} ... }'
                            break;
                        }

                        // Clear any entries if existing instance
                        if ($is_existing_instance === true) {
                            $subform_instances = $subform->getFormInstancesForObject($form_value);
                            if (!empty($subform_instances)) {
                                array_map(function ($subform_instance) {
                                    $subform_instance->delete();
                                }, $subform_instances ?: []);
                            }
                        }

                        if (!empty($result) && is_array($result)) {
                            // var_dump($result);
                            foreach ($result as $_index => $subform_row) {
                                if (is_a($subform_row, 'SimpleXMLElement')) {
                                    $this->attachFormFromXMLToObject($subform_row, $subform, $form_value, $non_standard_attachments);
                                }
                            }
                        }
                        break;
                    };
                    case "multivalue":{
                        $mutlivalue_string = '';
                        $values = $current_document->xpath('//' . $field->technical_name . '/text()');

                        if (!empty($values)) {
                            foreach ($values as $value) {
                                $mutlivalue_string .= (!empty($mutlivalue_string) ? ',' : '') . ((string) $value);
                            }
                        }

                        $this->createFormValue($form->w, $is_existing_instance, $instance, $field, $mutlivalue_string);
                        break;
                    };
                    default:
                        $this->createFormValue($form->w, $is_existing_instance, $instance, $field, $this->getFirstOf($current_document, $field->technical_name, $current_document));
                }
            }
        }
        //run 'on created' or 'on modified' processors here
        if ($is_existing_instance) {
            //run 'on modified processor'
            FormService::getInstance($form->w)->processEvents($instance, 'On Modified', $form);
        } else {
            //run 'on created processor'
            FormService::getInstance($form->w)->processEvents($instance, 'On Created', $form);
        }
    }

    private function createFormValue($w, $is_existing_instance, $instance, $field, $value)
    {
        $form_value = null;
        if ($is_existing_instance) {
            $form_value = FormService::getInstance($w)->getFormValueForInstanceAndField($instance->id, $field->id);

            if (!empty($form_value->id)) {
                $form_value->value = $value;
                $form_value->update();
            }
        }

        if (!$is_existing_instance || empty($form_value->id)) {
            $form_value = new FormValue($w);
            $form_value->form_instance_id = $instance->id;
            $form_value->form_field_id = $field->id;
            $form_value->value = $value;
            $form_value->insert();
        }

        return $form_value;
    }

    private function getFirstOf($xml, $attr, $refnode = null)
    {
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
