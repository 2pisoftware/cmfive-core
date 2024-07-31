<?php

function delete_GET(Web &$w)
{
    $p = $w->pathMatch("id", "type");
    $return_url = "/admin-lookups/index?type=" . $p['type'];
    $lookup = LookupService::getInstance($w)->getLookup($p['id']);

    if (!$lookup) {
        $w->msg("Lookup Item not found", $return_url);
    }

    $contacts_used_by_lookup = AdminService::getInstance($w)->getObjects('Contact', ['title_lookup_id' => $p['id']]);
    if (!empty($contacts_used_by_lookup)) {
        $list_of_contacts = [];
        foreach ($contacts_used_by_lookup as $contact) {
            $list_of_contacts[] = $contact->getFullName();
        }

        $w->msg("Cannot delete lookup as it is used as a title for the contacts: " . implode(', ', $list_of_contacts), $return_url);
    }

    $lookup->is_deleted = 1;
    $lookup->update();
    $w->msg("Lookup Item deleted", $return_url);
}
