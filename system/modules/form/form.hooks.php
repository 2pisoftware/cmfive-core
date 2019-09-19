<?php

function form_core_template_tab_headers(Web $w, $object)
{
    if (empty($object)) {
        return;
    }

    // Check and see if there are any forms mapped to the object
    if ($w->Form->areFormsMappedToObject($object)) {
        $tabHeaders = [];
        $forms = $w->Form->getFormsMappedToObject($object);
        foreach ($forms as $form) {
            $form_mapping = $w->Form->getFormMapping($form, get_class($object)) ?? $w->Form->getFormApplicationMapping($form, $object);

            if ($form->is_deleted == 0) {
                $tabHeaders[] = "<a href='#" . toSlug($form->title) . "'>$form->title" . (!empty($form_mapping) && $form_mapping->is_singleton ? "" : "<span class='secondary round label cmfive__tab-label'>" .  $form->countFormInstancesForObject($object) . "</span>") . "</a>";
            }
        }
        return implode("", $tabHeaders);
    }
    return '';
}

function form_core_template_tab_content(Web $w, $params)
{
    if (empty($params['object']) || empty($params['redirect_url'])) {
        return;
    }

    $forms_list = "";

    $form_mappings = $w->Form->getFormMappingsForObject($params['object']);
    $form_application_mapping = null;

    if ($params['object'] instanceof FormApplication) {
        $form_application_mapping = $w->Form->getFormApplicationMappingsForObject($params['object']);
    }

    if (!empty($form_application_mapping)) {
        $form_mappings[] = $form_application_mapping;
    }

    foreach ($form_mappings ?? [] as $form_mapping) {
        $form = $form_mapping->getForm();
        if (empty($form)) {
            continue;
        }

        $forms_list .= '<div id="' . toSlug($form->title) . '">' . $w->partial($form_mapping->is_singleton ? "show_form" : "listform", [
            "form" => $form,
            "redirect_url" => $params['redirect_url'],
            'object' => $params['object']
        ], "form") . '</div>';
    }

    return $forms_list;
}

/**
 * Moves attachments to the given form value based on a comma separated list of IDs
 *
 * (Field must be attachment type)
 *
 * @param  Web       $w
 * @param  FormValue $form_value
 * @return Null
 */
function form_core_dbobject_after_insert_FormValue(Web $w, FormValue $form_value)
{
    $field = $form_value->getFormField();

    if ($field->type != "attachment") {
        return;
    }

    if (empty($form_value->value)) {
        return;
    }

    // Turn a comma separated string of attachment ids into an array of attachment objects
    $attachments = array_map(function ($attachment_id) use ($form_value) {
        return $form_value->w->File->getAttachment($attachment_id);
    }, explode(',', $form_value->value));

    if (!empty($attachments)) {
        // Reassign them to the given form value if needed
        foreach ($attachments as $attachment) {
            if ($attachment->parent_table != 'form_value' && $attachment->parent_id != $form_value->id) {
                $attachment->parent_table = 'form_value';
                $attachment->parent_id = $form_value->id;
                $attachment->update();
            }
        }
    }
}

/**
 * Moves attachments to the given form value based on a comma separated list of IDs
 *
 * (Field must be attachment type)
 *
 * @param  Web       $w
 * @param  FormValue $form_value
 * @return Null
 */
function form_core_dbobject_after_update_FormValue(Web $w, FormValue $form_value)
{
    $field = $form_value->getFormField();

    if ($field->type != "attachment") {
        return;
    }

    if (empty($form_value->value)) {
        return;
    }

    // Turn a comma separated string of attachment ids into an array of attachment objects
    $attachments = array_map(function ($attachment_id) use ($form_value) {
        return $form_value->w->File->getAttachment($attachment_id);
    }, explode(',', $form_value->value));

    if (!empty($attachments)) {
        // Reassign them to the given form value if needed
        foreach ($attachments as $attachment) {
            if (!empty($attachment) && $attachment->parent_table != 'form_value' && $attachment->parent_id != $form_value->id) {
                $attachment->parent_table = 'form_value';
                $attachment->parent_id = $form_value->id;
                $attachment->update();
            }
        }
    }
}
