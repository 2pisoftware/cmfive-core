<?php
function deletelookup_ALL(Web &$w) {
	$p = $w->pathMatch("id","type");
	$return_url = "/admin/lookup/?type=".$p['type'];
	$lookup = $w->Admin->getLookupbyId($p['id']);

	if ($lookup) {
		$contacts_used_by_lookup = $w->Admin->getObjects('Contact', ['title_lookup_id' => $p['id']]);
		if (!empty($contacts_used_by_lookup)) {
			$list_of_contacts = "";
			foreach ($contacts_used_by_lookup as $contact) {
				$list_of_contacts .= $contact->getFullName() . ', ';
			}
			$list_of_contacts = substr($list_of_contacts, 0, -2); // remove last comma.
			$w->msg("Cannot delete lookup as it is used as a title for the contacts: " . $list_of_contacts, $return_url);
		}
		$arritem['is_deleted'] = 1;
		$lookup->fill($arritem);
		$lookup->update();
		$w->msg("Lookup Item deleted", $return_url);
	}
	else {
		$w->msg("Lookup Item not found?", $return_url);
	}
}