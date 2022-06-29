<?php

class LookupService extends DbService
{

    public function getLookupsWhere(array $where = []): array
    {
        if (!array_key_exists("is_deleted", $where)) {
            $where['is_deleted'] = 0;
        }
        return $this->getObjects("Lookup", $where);
    }

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

    public function getLookupTypes(): array
    {
        $types = $this->w->db->get('lookup')->select()->select('DISTINCT type')->fetchAll();

        return array_map(function ($t) {
            return [$t['type'], $t['type']];
        }, $types);
    }
}
