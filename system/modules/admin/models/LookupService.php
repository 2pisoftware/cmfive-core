<?php

class LookupService extends DbService
{

    public function getLookups()
    {
        return $this->getObjects("Lookup", ["is_deleted" => 0]);
    }

    public function getLookup($id)
    {
        return $this->getObject("Lookup", $id);
    }

    /**
     * Returns an array of lookups via their type.
     *
     * @param string $type
     * @return array<Lookup>
     */
    public function getLookupByType($type)
    {
        return $this->getObjects("Lookup", ["type" => $type, "is_deleted" => 0]);
    }

    public function getLookupByTypeAndCode($type, $code)
    {
        return $this->getObjects("Lookup", ["type" => $type, "code" => $code, "is_deleted" => 0]);
    }

    public function lookupForSelect($w, $type)
    {
        $select = [];
        $rows = $this->getObjects("Lookup", ["type" => $type, 'is_deleted' => 0]);
        if ($rows) {
            foreach ($rows ?? [] as $row) {
                $select[] = [$row->title, $row->code];
            }
        }
        return $select;
    }
}
