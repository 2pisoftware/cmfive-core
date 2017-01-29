<?php namespace System\Modules\Favorite;

// Replacement function for listFavourite!

function display_favorites(\Web $w) {	
	$results = $w->Favorite->getFavoritesForUser($w->Auth->user()->id);
	$categorisedFavorites = array();
	if (!empty($results)) {
		foreach ($results as $k => $favorite) {
			if (!array_key_exists($favorite->object_class, $categorisedFavorites)) {
				$categorisedFavorites[$favorite->object_class] = array();
			}

			$object = $favorite->getLinkedObject();
			if (!empty($object))  {
				$categorisedFavorites[$favorite->object_class][] = $object;
			}
		}
		
		// Sort data
		ksort($categorisedFavorites);
		foreach($categorisedFavorites as &$categorisedFavorite) {
			usort($categorisedFavorite, function($a, $b) {
				return strcmp($a, $b);
			});
		}
	}
	
	
	
	
	$w->ctx('categorisedFavorites', $categorisedFavorites);
}
