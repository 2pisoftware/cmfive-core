<?php

function ajaxGetMetadata_GET(Web $w)
{

    $w->setLayout(null);
    list($field_id) = $w->pathMatch();
    $type = Request::string("type");

    if (empty($field_id) && empty($type)) {
        // header("HTTP/1.1 404 Not Found");
        return;
    }

    // VueComponentRegister::registerComponent('metadata-subform', new VueComponent('metadata-subform', '/system/modules/form/assets/js/metadata-subform.vue.js'));

    $field = null;
    if (!empty($field_id)) {
        $field = FormService::getInstance($w)->getFormField($field_id);
        if (empty($field->id)) {
            // header("HTTP/1.1 404 Not Found");
            return;
        }
    }

    if (!empty($type)) {
        $interfaces = Config::get('form.interfaces');
        if (!empty($interfaces)) {
            foreach ($interfaces as $interface) {
                if ($interface::respondsTo($type)) {
                    $metadata_form = $interface::metadataForm($type, $w);

                    // If form is an array assume its based on the HtmlBootstrap5::form layout
                    if (is_array($metadata_form)) {
                        // Try and fill existing data in the event that the user changes back to the original field type if field already exists
                        if (!empty($field)) {
                            foreach ($metadata_form as $metadata_form_row_index => &$metadata_form_row) {
                                if (is_array($metadata_form_row)) {
                                    $existing_metadata_field = $field->findMetadataByKey($metadata_form_row[2]);
                                    if (!empty($existing_metadata_field->id)) {
                                        $metadata_form_row[3] = $existing_metadata_field->meta_value;
                                    }
                                }
                            }
                        }


                        $w->out(htmlentities(HtmlBootstrap5::form($metadata_form)));
                        return;
                    } else if (is_a($metadata_form, 'VueComponent')) {
                        // Else assume new Vue.js component layout - will already be in the template
                        // header("HTTP/1.1 404 Not Found");
                        // $w->out(htmlentities($metadata_form->display()));
                        return;
                    }
                }
            }
        }
    } else {
        if (!empty($field)) {
            $metadata_form = $field->getMetadataForm();
            if (!empty($metadata_form)) {
                $w->out(htmlentities(HtmlBootstrap5::form($metadata_form)));
                return;
            }
        } else {
            // header("HTTP/1.1 404 Not Found");
            return;
        }
    }
}