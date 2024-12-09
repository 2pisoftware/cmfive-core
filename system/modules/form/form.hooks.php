<?php

function form_core_template_tab_headers(Web $w, $object)
{
    if (empty($object)) {
        return;
    }

    // Check and see if there are any forms mapped to the object
    if (FormService::getInstance($w)->areFormsMappedToObject($object)) {
        $tabHeaders = [];
        $forms = FormService::getInstance($w)->getFormsMappedToObject($object);
        foreach ($forms as $form) {
            if ($form->is_deleted == 0) {
                $tabHeaders[] = '<a href="#' . toSlug($form->title) . '">' . $form->title . ' <span class="badge rounded-pill bg-secondary text-light ms-1">' .  $form->countFormInstancesForObject($object) . '</span></a>';
            }
        }
        return implode('', $tabHeaders);
    }
    return '';
}

function form_core_template_tab_content(Web $w, $params)
{
    if (empty($params['object']) || empty($params['redirect_url'])) {
        return;
    }

    $forms_list = "";

    $form_mappings = FormService::getInstance($w)->getFormMappingsForObject($params['object']);
    $form_application_mappings = null;

    if ($params['object'] instanceof FormApplication) {
        $form_application_mappings = FormService::getInstance($w)->getFormApplicationMappingsForObject($params['object']);
    }

    if (!empty($form_application_mappings)) {
        $form_mappings = array_merge($form_mappings, $form_application_mappings);
    }

    foreach ($form_mappings ?? [] as $form_mapping) {
        $form = $form_mapping->getForm();
        if (empty($form) || !$form->canList(AuthService::getInstance($w)->user())) {
            continue;
        }

        $partialArguments = [
            "form" => $form,
            "redirect_url" => $params['redirect_url'],
            'object' => $params['object'],
            "display_only" => false,
        ];

        if (!empty($params['paginated'])) {
            $partialArguments['paginated'] = true;
            // $partialArguments['currentpage'] = $params['currentpage'];
            // $partialArguments['numpages'] = $params['numpages'];
            // $partialArguments['pagesize'] = $params['pagesize'];
            // $partialArguments['totalresults'] = $params['totalresults'];
        }

        $forms_list .= '<div id="' . toSlug($form->title) . '" class="pt-3">' . $w->partial(
            $form_mapping->is_singleton ? "show_form" : "listform",
            $partialArguments,
            "form"
        ) . '</div>';
    }

    // var_dump($forms_list);

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
        return FileService::getInstance($form_value->w)->getAttachment($attachment_id);
    }, explode(',', $form_value->value));

    if (!empty($attachments)) {
        // Reassign them to the given form value if needed
        /** @var Attachment $attachment */
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
        return FileService::getInstance($form_value->w)->getAttachment($attachment_id);
    }, explode(',', $form_value->value));

    if (!empty($attachments)) {
        // Reassign them to the given form value if needed
        /** @var Attachment $attachment */
        foreach ($attachments as $attachment) {
            if (!empty($attachment) && $attachment->parent_table != 'form_value' && $attachment->parent_id != $form_value->id) {
                $attachment->parent_table = 'form_value';
                $attachment->parent_id = $form_value->id;
                $attachment->update();
            }
        }
    }
}
