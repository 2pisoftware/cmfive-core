<?php

class LookupService extends DbService {
    
    public function getLookups() {
        return $this->getObjects("Lookup", array("is_deleted" => 0));
    }
    
    public function getLookup($id) {
        return $this->getObject("Lookup", $id);
    }
    
    public function getLookupByType($type) {
        return $this->getObjects("Lookup", array("type" => $type, "is_deleted" => 0));
    }
    
    public function getLookupByTypeAndCode($type, $code) {
        return $this->getObjects("Lookup", array("type" => $type, "code" => $code, "is_deleted" => 0));
    }
    
}