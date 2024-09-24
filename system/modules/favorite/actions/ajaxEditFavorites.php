<?php

/**
 * updates or removes favorited item 
 *
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 * @author Adam Buckley, adam@2pisoftware.com, 2015
 */
function ajaxEditFavorites_ALL(Web $w)
{
    $id = Request::int("id");
    $class = Request::string("class");
    $user = AuthService::getInstance($w)->user();

    if (!empty($id) && !empty($class) && !empty($user)) {
        $favorite = FavoriteService::getInstance($w)->getFavoriteForUserAndObject($user->id, $class, $id);
        if (!empty($favorite->id)) {
            $favorite->delete();
        } else {
            $favorite = new Favorite($w);
            $favorite->object_class = $class;
            $favorite->object_id = $id;
            $favorite->user_id = $user->id;
            $favorite->insertOrUpdate();
        }
    } else {
        echo "Invalid request";
    }
}
