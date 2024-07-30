<?php

namespace System\Modules\Favorite;

function display_favorites(\Web $w)
{
    $results = \FavoriteService::getInstance($w)
        ->getFavoritesForUser(\AuthService::getInstance($w)->user()->id);
    $categorisedFavorites = array();
    if (!empty($results)) {
        foreach ($results as $k => $favorite) {
            if (!array_key_exists($favorite->object_class, $categorisedFavorites)) {
                $categorisedFavorites[$favorite->object_class] = array();
            }

            $object = $favorite->getLinkedObject();
            if (!empty($object) && property_exists($object, "is_deleted") && !$object->is_deleted) {
                $categorisedFavorites[$favorite->object_class][] = $object;
            }
        }

        // Sort data
        ksort($categorisedFavorites);
        foreach ($categorisedFavorites as &$categorisedFavorite) {
            usort($categorisedFavorite, function ($a, $b) {
                return strcmp($a, $b);
            });
        }
    }

    $w->ctx('categorisedFavorites', $categorisedFavorites);
}
