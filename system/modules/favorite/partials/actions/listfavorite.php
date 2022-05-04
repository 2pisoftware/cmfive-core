<?php
/**
 * Partial action that lists favorite objects
 * @author Steve Ryan steve@2pisoftware.com 2015
 */
function listfavorite_ALL(Web $w,$params) {
	$user = AuthService::getInstance($w)->user();
	if (!empty($user))  {
		$results = FavoriteService::getInstance($w)->getFavoritesForUser($user->id );
		$favoritesCategorised=array();
		$service=new DBService($w);
		if (!empty($results)) {
			foreach ($results as $k => $favorite) {
				if (!array_key_exists($favorite->object_class,$favoritesCategorised)) $favoritesCategorised[$favorite->object_class]=array();
				$realObject=$service->getObject($favorite->object_class,$favorite->object_id);
				if (!empty($realObject))  {
					$templateData=array();
					$templateData['title']=$realObject->printSearchTitle();
					$templateData['url']=$realObject->printSearchUrl();
					$templateData['listing']=$realObject->printSearchListing();
					if ($realObject->canList($user) && $realObject->canView($user)) array_push($favoritesCategorised[$favorite->object_class],$templateData);
				}
			}
		}
		$w->ctx('categorisedFavorites',$favoritesCategorised);
	}
}
