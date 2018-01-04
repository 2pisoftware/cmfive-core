<?php


function favorite_core_template_menu(Web $w) {
	$response = '<link rel="stylesheet" href="/system/modules/favorite/assets/css/favorite.css" />';
	$response .= '<script src="/system/modules/favorite/assets/js/favoriteButton.js"></script>';
    $response .= '<li>' . Html::box("/favorite", "<span class='show-for-medium-up fi-star'></span><span class='show-for-small'>Favourites</span>") . '</li>';
	
	return $response;
}

function favorite_core_dbobject_after_delete($w, $obj) {
    //delete favorite for deleted object
    $class_name = get_class($obj);
    $favorites = $w->Favorite->getAllFavoritesForObject($class_name, $obj->id);
    if (!empty($favorites)) {
        foreach ($favorites as $favorite) {
            $favorite->delete();
        }
    }
}