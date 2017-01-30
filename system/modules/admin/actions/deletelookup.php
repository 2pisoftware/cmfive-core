<?php
function deletelookup_ALL(Web &$w) {
	$p = $w->pathMatch("id","type");
		
	$lookup = $w->Admin->getLookupbyId($p['id']);
		
	if ($lookup) {
		$arritem['is_deleted'] = 1;
		$lookup->fill($arritem);
		$lookup->update();
		$w->msg(__("Lookup Item deleted"),"/admin/lookup/?type=".$p['type']);
	}
	else {
		$w->msg(__("Lookup Item not found?"),"/admin/lookup/?type=".$p['type']);
	}
}
