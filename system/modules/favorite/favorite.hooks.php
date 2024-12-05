<?php

function favorite_core_dbobject_after_delete($w, $obj) {
    //delete favorite for deleted object
    $class_name = get_class($obj);
    $favorites = FavoriteService::getInstance($w)->getAllFavoritesForObject($class_name, $obj->id);
    if (!empty($favorites)) {
        foreach ($favorites as $favorite) {
            $favorite->delete();
        }
    }
}