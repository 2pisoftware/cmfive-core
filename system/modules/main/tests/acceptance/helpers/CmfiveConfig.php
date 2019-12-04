<?php

namespace Helper;

class CmfiveConfig extends \Codeception\Module
{
    private $CACHE_PATH = '/../../cache/config.cache';
    private $CONFIG_CLASS_PATH = '/../../system/classes/Config.php';

    public function __construct(\Codeception\Lib\ModuleContainer $moduleContainer, $config = null)
    {
        if (!class_exists("Config")) {
            require_once(getcwd() . $this->CONFIG_CLASS_PATH);
        }

        \Config::fromJson(getcwd() . $this->CACHE_PATH);
        parent::__construct($moduleContainer, $config);
    }

    public function getConfig($path)
    {
        return \Config::get($path);
    }
}
