<?php

/**
 * NOTE: Lookup functions used in this AdminService class are now DEPRICATED
 *       Use the LookupService class instead
 */

class AdminService extends DbService {

    // function to sort lookup items by type
    static function sortByType($a, $b) {
        if ($a->type == $b->type) {
            return 0;
        }
        return ($a->type > $b->type) ? +1 : -1;
    }

 

    function getLookupTypes() {
        $lookup = $this->getObjects("Lookup", array("is_deleted" => 0));
        $types = array();
        if ($lookup) {
            foreach ($lookup as $l) {
                $types[$l->type] = array($l->type, $l->type);
            }
        }
        return $types;
    }

    function getLookupItemsbyType($type) {
        $lookup = $this->getObjects("Lookup", array("type" => $type, "is_deleted" => 0), true);
        $items = array();
        if ($lookup) {
            foreach ($lookup as $l) {
                $items[$l->id] = array($l->title, $l->id);
            }
        }

        return $items;
    }

    function getLookupItembyId($id) {
//        return $this->getObject("Lookup", $id);
        $lookup = $this->getObjects("Lookup", array("id" => $id));
        $item = null;
        foreach ($lookup as $l) {
            $item = array($l->code, $l->title);
        }
        return $item;
    }

    function getLookupbyTypeCode($type, $code) {
        return $this->getObject("Lookup", array("type" => $type, "code" => $code, "is_deleted" => 0));
    }

    function getLookupbyId($id) {
        return $this->getObject("Lookup", array("id" => $id));
    }

    function getAllLookup($where = array()) {
        $where["is_deleted"] = 0;

        $lookups = $this->getObjects("Lookup", $where);
        if ($lookups) {
            usort($lookups, array("AdminService", "sortbyType"));
        }
        return $lookups;
    }

    public function navigation(Web $w, $title = null, $prenav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }
		
        $nav = $prenav ? $prenav : array();
        if ($w->Auth->loggedIn()) {
            $w->menuLink("admin/users", __("List Users"), $nav);
            $w->menuLink("admin/groups", __("List Groups"), $nav);
            $w->menuLink("admin/lookup", __("Lookup"), $nav);
            $w->menuLink("admin-templates", __("Templates"), $nav);
            $w->menuLink("admin/phpinfo", __("PHP Info"), $nav);
            $w->menuLink("admin/printers", __("Printers"), $nav);
            $w->menuLink("admin/printqueue", __("Print Queue"), $nav);
            $w->menuLink("admin/databasebackup", __("Backup Database"), $nav);
            $w->menuLink("admin/composer", __("Install/Update Composer"), $nav, null, "_blank");
            $w->menuLink("admin/email", __("Email"), $nav);
            $w->menuLink("admin-migration", __("Migrations"), $nav);
			$w->menuLink("admin-file", __("File transfer"), $nav);
        }
		
        $w->ctx("navigation", $nav);
        return $nav;
    }

}
