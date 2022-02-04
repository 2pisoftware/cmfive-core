<?php
/**
 * Service class for Favorite
 * 
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 */
class FavoriteService extends DbService {

	public function getFavorites() {
		return $this->getObjects("Favorite");
	}
	
	public function getFavorite($id) {
		return $this->getObject("Favourite", $id);
	}
	
	public function getFavoritesForUser($user_id) {
		return $this->getObjects("Favorite", [
			"user_id" => $user_id
		]);
	}
	
	public function getFavoritesForUserAndClass($user_id, $class) {
		return $this->getObjects("Favorite", [
			"is_deleted" => 0,
			"user_id" => $user_id,
			"object_class" => $class
		]);
	}
	
	public function getFavoriteForUserAndObject($user_id, $class, $id) {
		return $this->getObject("Favorite", [
			"is_deleted" => 0,
			"user_id" => $user_id,
			"object_class" => $class,
			"object_id" => $id
		]);
	}
	
	function getFavoriteForObject($class, $object_id) {
		return $this->getObject("Favorite", [
			"is_deleted" => 0,
			"object_id" => $object_id,
			"object_class" => $class
		]);
	}

	public function getFavoriteButton($object) {
		$response = '';
		$user = AuthService::getInstance($this->w)->user();
		if (!empty($user)){
			$favorite = FavoriteService::getInstance($this->w)->getFavoriteForUserAndObject($user->id, get_class($object), $object->id);
			
			$response .= '<i data-class="' . get_class($object) . '" data-id="' .$object->id . '" class="fi-star favorite_flag ' . (!empty($favorite) ? 'favorite_on' : '') .'"></i>';
		}
		return $response;
	}

    //returns favorites for all users for specific object. 
    function getAllFavoritesForObject($class, $object_id) {
        return $this->getObjects("Favorite", [
			"is_deleted" => 0,
			"object_id" => $object_id,
			"object_class" => $class
		]);
    }
    
}
