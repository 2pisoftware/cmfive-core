<?php

interface InjectableModuleInterface
{
    /**
     * This function should return a fake Config array that will be used to inject the module into the top menu.
     * The injected_by key should be the name of the module that is injecting this module and is required for
     * ensuring that the autoloader can find the right folder for autoloaded classes
     *
     * E.g.
     * ```php
     * return [
     *  'path' => 'modules',
     *  'active' => true,
     *  'topmenu' => 'Injected Module',
     *  'injected_by' => 'my_parent_module',
     * ];
     * ```
     * @return array
     */
    public static function serviceConfig(): array;

    /**
     * This function should inspect the parsed Web path and determine if the current request is in the injected top level module.
     * This is primarily used to ensure that the correct top level menu item is highlighted correctly.
     *
     * E.g.
     * ```php
     * return $w->_module == "parent_module" && in_array($w->_submodule, ['injected_submodule1', 'injected_submodule2']);
     * ```
     * @param Web $w
     * @return bool
     */
    public static function isInInjectedTopLevelModule(Web $w): bool;
}
