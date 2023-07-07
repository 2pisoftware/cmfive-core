<?php

namespace Tests\Support\Helper;
use Tests\Support\AcceptanceTester;

class CmfiveConfig extends \Codeception\Module
{
    private $CACHE_PATH = '/../../cache/config.cache';
    private $CONFIG_CLASS_PATH = '/../../system/classes/Config.php';

    public function _before($test)
    {
        $base = $this->getModule('Tests\Support\Helper\CmfiveSite')->getInstallPath();
        $this->CACHE_PATH = $base . '/cache/config.cache';
        $this->CONFIG_CLASS_PATH = $base . '/system/classes/Config.php';

        if (!class_exists("Config")) {
            // like: /mnt/2pi/cmfive-boilerplate/system/classes/Config.php
            require_once($this->CONFIG_CLASS_PATH);
        }

        echo 'Preparing test snapshot of cm5 config:' . "\n";
        echo ($base) . "\n";
        echo ($this->CACHE_PATH) . "\n";
        echo ($this->CONFIG_CLASS_PATH) . "\n";
        \Config::fromJson(file_get_contents($this->CACHE_PATH));
    }
    public function getCmfiveConfig($path)
    {
        return \Config::get($path);
    }

    public function setCmfiveConfig($key, $value)
    {
        return \Config::set($key, $value);
    }
}
