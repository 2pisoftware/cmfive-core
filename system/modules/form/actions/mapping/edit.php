<?php

function edit_POST(Web $w)
{
    $form_id = Request::int("form_id");
    $current_mappings = FormService::getInstance($w)->getObjects("FormMapping", ["form_id" => $form_id, "is_deleted" => 0]) ?? [];

    foreach ($_POST as $key => $value) {
        switch ($value) {
            case "none":
                foreach ($current_mappings as $current_mapping) {
                    if ($key === $current_mapping->object) {
                        $current_mapping->delete(true);
                    }
                }
                break;
            case "single":
                $was_mapping_found = false;

                foreach ($current_mappings as $current_mapping) {
                    if ($key === $current_mapping->object) {
                        $was_mapping_found = true;
                        $current_mapping->is_singleton = true;

                        if (!$current_mapping->update()) {
                            LogService::getInstance($w)->setLogger("FORM")->error("Failed to update FormMapping with id: {$current_mapping->id}");
                        }
                    }
                }

                if ($was_mapping_found) {
                    break;
                }

                $new_mapping = new FormMapping($w);
                $new_mapping->form_id = $form_id;
                $new_mapping->object = $key;
                $new_mapping->is_singleton = true;

                if (!$new_mapping->insert()) {
                    LogService::getInstance($w)->setLogger("FORM")->error("Failed to create new FormMapping for Form with id: {$form_id}");
                }
                break;
            case "multiple":
                $was_mapping_found = false;

                foreach ($current_mappings as $current_mapping) {
                    if ($key === $current_mapping->object) {
                        $was_mapping_found = true;
                        $current_mapping->is_singleton = false;

                        if (!$current_mapping->update()) {
                            LogService::getInstance($w)->setLogger("FORM")->error("Failed to update FormMapping with id: {$current_mapping->id}");
                        }
                    }
                }

                if ($was_mapping_found) {
                    break;
                }

                $new_mapping = new FormMapping($w);
                $new_mapping->form_id = $form_id;
                $new_mapping->object = $key;
                $new_mapping->is_singleton = false;

                if (!$new_mapping->insert()) {
                    LogService::getInstance($w)->setLogger("FORM")->error("Failed to create new FormMapping for Form with id: {$form_id}");
                }
                break;
            default:
                LogService::getInstance($w)->setLogger("FORM")->error("Unknown mapping type: {$value}, no action taken");
                break;
        }
    }

    $w->msg("Form mappings updated", "/form/show/" . $form_id . "#mapping");
}
