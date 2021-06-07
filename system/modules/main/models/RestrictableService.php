<?php

class RestrictableService extends DbService
{

    public function setOwner($object, $user_id)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        $link = MainService::getInstance($this->w)->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "owner"]);
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

    public function addViewer($object, $user_id)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        $logged_in_user_id = AuthService::getInstance($this->w)->user()->id;
        $owner_link = MainService::getInstance($this->w)->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "user_id" => $logged_in_user_id, "type" => "owner"]);

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

    public function removeViewer($object, $user_id)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        $logged_in_user_id = AuthService::getInstance($this->w)->user()->id;
        $owner_link = MainService::getInstance($this->w)->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "user_id" => $logged_in_user_id, "type" => "owner"]);

        if ($logged_in_user_id !== $owner_link->user_id) {
            return false;
        }

        $link = MainService::getInstance($this->w)->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "user_id" => $user_id, "type" => "viewer"]);
        if (!empty($link) && $link->delete()) {
            return true;
        }
        return false;
    }

    public function getOwner($object)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        $link = MainService::getInstance($this->w)->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "owner"]);
        if (empty($link)) {
            return null;
        }

        return AuthService::getInstance($this->w)->getObject("User", $link->user_id);
    }

    public function getViewers($object)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        $links = MainService::getInstance($this->w)->getObjects("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "viewer"]);
        if (empty($links)) {
            return null;
        }

        $viewers = [];
        foreach ($links as $link) {
            $viewer = AuthService::getInstance($this->w)->getUser($link->user_id);
            if (!empty($viewer)) {
                $viewers[] = $viewer;
            }
        }

        return $viewers;
    }

    public function getOwnerLink($object)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        return MainService::getInstance($this->w)->getObject("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "owner"]);
    }

    public function getViewerLinks($object)
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        return MainService::getInstance($this->w)->getObjects("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object), "type" => "viewer"]);
    }

    /**
     * Will ckeck if a DbObject is restricted.
     *
     * @param DbObject $object
     * @return boolean
     */
    public function isRestricted(DbObject $object): bool
    {
        if (!property_exists($object, "_restrictable")) {
            return false;
        }

        return $this->_db->get("restricted_object_user_link")->where("object_id", $object->id, "object_class")->where("object_class", $object->getDbTableName())->count() > 0;
    }

    /**
     * Removes restrictions from a DbObject.
     *
     * @param DbObject $object
     * @return void
     */
    public function unrestrict(DbObject $object): void
    {
        if (!property_exists($object, "_restrictable")) {
            return;
        }

        $links = $this->getObjects("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => $object->getDbTableName()]);
        foreach ($links as $link) {
            $link->delete();
        }
    }
}
