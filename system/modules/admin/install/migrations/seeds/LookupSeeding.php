<?php

class LookupSeeding extends CmfiveSeedMigration
{
    public $name = "Lookup Seed";
    public $description = "Adds basic lookup table values (Mr/Mrs/Miss and Yes/No)";

    public function seed()
    {
        $values = [
            ['title', 'Mr', 'mr'],
            ['title', 'Mrs', 'mrs'],
            ['title', 'Ms', 'ms'],
            ['YesNo', 'Yes', 1],
            ['YesNo', 'No', 0]
        ];

        foreach ($values as $value) {
            $lookup = new Lookup($this->w);
            $lookup->type = $value[0];
            $lookup->code = $value[2];
            $lookup->title = $value[1];
            $lookup->insert();
        }
    }
}
