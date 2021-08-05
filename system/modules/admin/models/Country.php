<?php

class Country extends DbObject
{
    const _ALPHA_2_CODE = '2';
    const _ALPHA_3_CODE = '3';

    public $name;
    public $alpha_2_code;
    public $alpha_3_code;
    public $capital;
    public $region;
    public $subregion;
    public $demonym;

    public function getSelectOptionTitle()
    {
        return $this->name;
    }

    public function getSelectOptionValue()
    {
        return $this->alpha_2_code;
    }
}
