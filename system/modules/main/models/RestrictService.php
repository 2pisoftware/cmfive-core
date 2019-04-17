<?php

class RestrictService extends DbService {

	public function setOwner($object, $user_id) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		$link = $this->w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "owner"]);
		if (empty($link)) {
			$link = new RestrictedObjectUserLink($this->w);
		}

		$link->object_class = get_class($object);
		$link->object_id = $object->id;
		$link->user_id = $user_id;
		$link->type = "owner";

		if ($link->insertOrUpdate()) {
			return true;
		}
		return false;
	}

	public function addViewer($object, $user_id) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		$logged_in_user_id = $this->w->Auth->user()->id;
		$owner_link = $this->w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "user_id" => $logged_in_user_id, "type" => "owner"]);

		if (empty($owner_link) || $logged_in_user_id !== $owner_link->user_id) {
			return false;
		}

		$link = new RestrictedObjectUserLink($this->w);
		$link->object_class = get_class($object);
		$link->object_id = $object->id;
		$link->user_id = $user_id;
		$link->type = "viewer";

		if ($link->insert()) {
			return true;
		}
		return false;
	}

	public function removeViewer($object, $user_id) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		$logged_in_user_id = $this->w->Auth->user()->id;
		$owner_link = $this->w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "user_id" => $logged_in_user_id, "type" => "owner"]);

		if ($logged_in_user_id !== $owner_link->user_id) {
			return false;
		}

		$link = $this->w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "user_id" => $user_id, "type" => "viewer"]);
		if (!empty($link) && $link->delete()) {
			return true;
		}
		return false;
	}

	public function getOwner($object) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		$link = $this->w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "owner"]);
		if (empty($link)) {
			return null;
		}

		return $this->w->Auth->getObject("User", $link->user_id);
	}

	public function getViewers($object) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		$links = $this->w->Main->getObjects("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "viewer"]);
		if (empty($links)) {
			return null;
		}

		$viewers = [];
		foreach ($links as $link) {
			$viewer = $this->w->Auth->getUser($link->user_id);
			if (!empty($viewer)) {
				$viewers[] = $viewer;
			}
		}

		return $viewers;
	}

	public function getOwnerLink($object) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		return $this->w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "owner"]);
	}

	public function getViewerLinks($object) {
		if (!property_exists($object, "_restrictable")) {
			return false;
		}

		return $this->w->Main->getObjects("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "viewer"]);
	}
}