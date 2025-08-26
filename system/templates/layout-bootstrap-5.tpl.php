<?php
/** @var Web $w */
$theme_setting = AuthService::getInstance($w)->getSettingByKey('bs5-theme');
?>
<!DOCTYPE html>
<html
    class="theme theme--<?php echo !empty($theme_setting->id) ? $theme_setting->setting_value : 'dark'; ?>"
    data-bs-theme="<?php echo !empty($theme_setting->id) ? ($theme_setting->setting_value === "dark" ? "dark" : "light") : "dark" ?>"
>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="/system/templates/img/cosine-icon-colour.png" type="image/x-icon" />
    <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' &#x2022; ' . $title : ''; ?></title>
    <script>var exports = {};</script>
    <?php
    CmfiveStyleComponentRegister::registerComponent('app', new CmfiveStyleComponent(Config::get('system.style_override', "/system/templates/base/dist/app.css")));
    CmfiveScriptComponentRegister::registerComponent('app', new CmfiveScriptComponent("/system/templates/base/dist/app.js", ['type' => 'module']));

    $w->outputStyles();
    ?>
    <!-- backwards compat -->
    <!-- <script src="/system/templates/base/node_modules/vue3/dist/vue.global.prod.js"></script> -->
    <script>
        // @todo: move this into a build file
        let modal_history = [];
    </script>
</head>
<body id="cmfive-body">
    <div id="app">
        <?php if (Config::get('system.test_mode') === true) : ?>
            <div class="alert alert-primary d-flex justify-content-center align-items-center text-center mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>&nbsp;
                <div>
                    <?php echo Config::get('system.test_mode_message'); ?>
                </div>
            </div>
        <?php endif; ?>
        <div id="offscreen-menu" class="d-flex flex-column pb-0">
            <div class="d-flex flex-row justify-content-between offscreen-header pb-3">
                <div class="d-flex flex-row align-items-center">
                    <h5 class="fw-bold text-2pi mt-1 mb-1 nav-link px-3 pr-2"><?php echo Config::get('main.application_name'); ?></h5>
                </div>
                <a class="nav-link pt-0 pb-1" data-toggle-menu="close"><i class="bi bi-x" style="font-size: 24px;"></i></a>
            </div>
            <ul class="nav py-2 justify-content-around border-top border-bottom">
                <li class="nav-item"><a class="nav-link nav-icon" href="/<?php echo AuthService::getInstance($w)->user()->redirect_url; ?>"><i class="bi bi-house-fill"></i></a></li>
                <?php if (Config::get('favorite.active', true) === true && AuthService::getInstance($w)->user()->allowed('/favorite')) : ?>
                    <li class="nav-item"><a class="nav-link nav-icon" data-modal-target="/favorite"><i class="bi bi-star-fill"></i></a></li>
                <?php endif;
                if (Config::get('system.help_enabled', true) && AuthService::getInstance($w)->user()->allowed('/help')) : ?>
                    <li class="nav-item"><a class="nav-link nav-icon" data-modal-target="/help/view/<?php echo $w->_module . ($w->_submodule ? "-" . $w->_submodule : "") . "/" . $w->_action; ?>"><i class="bi bi-question-circle-fill"></i></a></li>
                <?php endif;
                if (Config::get('system.search_enabled', true) && AuthService::getInstance($w)->user()->allowed('/search')) : ?>
                    <li class="nav-item"><a class="nav-link nav-icon" data-modal-target='/search?isbox=1'><i class="bi bi-search"></i></a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link nav-icon" href="#" data-toggle-theme><i class="bi bi-palette-fill"></i></a></li>
                <li class="nav-item"><a class="nav-link nav-icon" href="#" data-toggle-nav-settings><i class="bi bi-gear-fill"></i></a></li>
            </ul>
            <div class="accordion" id="accordion_menu">
                <?php $injectedModules = $w->callHook('core_template', 'topmenu');
                $base_modules = $w->modules();
                $base_modules_ref = $w->modules();
                array_push($base_modules, ...array_merge(...array_values($injectedModules)));

                foreach ($base_modules as $module) :
                    $module_service = ucfirst($module) . "Service";
                    if (!in_array($module, $base_modules_ref) && (new \ReflectionClass($module_service))->implementsInterface("InjectableModuleInterface")) {
                        Config::enableSandbox();
                        Config::promoteSandbox();
                        Config::set($module, $module_service::serviceConfig());
                    }
                    
                    // Check if config is set to display on topmenu
                    if (Config::get("{$module}.topmenu") && Config::get("{$module}.active")) :
                        // Check for navigation
                        $array = [];
                        $menu_link = method_exists($module_service, "menuLink") ? $module_service::getInstance($w)->menuLink() : $w->menuLink($module, is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"), $array, null, null, "nav-link");
                        if ($menu_link !== false) :
                            if (method_exists($module . "Service", "navList") || method_exists($module . "Service", "navigation")) : ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="accordion_menu_<?php echo $module; ?>_heading">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion_menu_<?php echo $module; ?>" aria-expanded="true" aria-controls="accordion_menu_<?php echo $module; ?>">
                                            <?php echo is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"); ?>
                                        </button>
                                    </h2>
                                    <?php // Try and get a badge count for the menu item
                                    $module_navigation = method_exists_overloaded($module_service::getInstance($w), "navList") ? $module_service::getInstance($w)->navList($w) : $module_service::getInstance($w)->navigation($w);

                                    // Invoke hook to inject extra navigation
                                    $hook_navigation_items = $w->callHook($module, "extra_navigation_items", $module_navigation);
                                    if (!empty($hook_navigation_items)) {
                                        foreach ($hook_navigation_items as $hook_navigation_item) {
                                            $module_navigation = array_merge($module_navigation, $hook_navigation_item);
                                        }
                                    }
                                    ?>
                                    <div id="accordion_menu_<?php echo $module; ?>" class="accordion-collapse collapse" aria-labelledby="accordion_menu_<?php echo $module; ?>_heading" data-bs-parent="#accordion_menu">
                                        <ul class="nav flex-column">
                                            <?php foreach ($module_navigation as $module_nav) :
                                                if (is_string($module_nav)) : ?>
                                                    <li class="nav-item"><?php echo $module_nav; ?></li>
                                                <?php else : ?>
                                                    <li class="nav-item"><a <?php echo $module_nav->type == MenuLinkType::Modal ? 'data-modal-target' : 'href'; ?>="<?php echo $module_nav->url; ?>"><?php echo $module_nav->title; ?></a></li>
                                                <?php endif;
                                            endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="accordion_menu_<?php echo $module; ?>_heading">
                                        <?php echo $menu_link; ?>
                                    </h2>
                                </div>
                            <?php endif; ?>
                        <?php endif;
                    endif;
                    Config::disableSandbox();
                endforeach; ?>
            </div>
            <div class="mt-auto pt-4">
                <div class="px-2 pb-1">
                    <div class="d-flex bd-highlight">
                        <div class="flex-fill bd-highlight">
                            <a role="button" class="btn btn-outline-primary w-100" href="/auth/profile">Profile</a>
                        </div>
                        <div class="ms-2 flex-fill bd-highlight">
                            <a role="button" class="btn btn-outline-secondary w-100" href="/auth/logout">Logout</a>
                        </div>
                    </div>
                    <hr class="mt-2 mb-1" />
                    <div class="col text-center">
                        <p class="fw-lighter m-0 p-0 text-secondary">&#169; <a class="text-secondary" href="<?php echo Config::get('main.company_url'); ?>" target="_blank"><?php echo Config::get('main.company_name'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="content">
            <div class="container-fluid p-0 py-2 p-lg-2" id="navbar">
                <?php $active_modules = array_filter($w->modules(), fn ($m) => !empty(Config::get("{$m}.topmenu")) && Config::get("{$m}.active") === true); ?>
                <nav class="<?php echo count($active_modules) <= 9 ? "container-xl" : "container-fluid"; ?> navbar navbar-expand navbar-light bg-light p-0 p-lg-2">
                    <div class="container-fluid justify-content-start">
                        <ul class="navbar-nav me-md-4">
                            <li class="nav-item"><a class="nav-link nav-icon" data-toggle-menu="open"><i class="bi bi-list"></i></a></li>
                            <li class="nav-item"><a class="nav-link nav-icon" href="/<?php echo AuthService::getInstance($w)->user()->redirect_url; ?>"><i class="bi bi-house-fill"></i></a></li>
                            <?php if (Config::get('favorite.active', true) === true && AuthService::getInstance($w)->user()->allowed('/favorite')) : ?>
                                <li class="nav-item"><a class="nav-link nav-icon" data-modal-target="/favorite"><i class="bi bi-star-fill"></i></a></li>
                            <?php endif;
                            if (Config::get('system.help_enabled', true) && AuthService::getInstance($w)->user()->allowed('/help')) : ?>
                                <li class="nav-item"><a class="nav-link nav-icon" data-modal-target="/help/view/<?php echo $w->_module . ($w->_submodule ? "-" . $w->_submodule : "") . "/" . $w->_action; ?>"><i class="bi bi-question-circle-fill"></i></a></li>
                            <?php endif;
                            if (Config::get('system.search_enabled', true) && AuthService::getInstance($w)->user()->allowed('/search')) : ?>
                                <li class="nav-item"><a class="nav-link nav-icon" data-modal-target='/search?isbox=1'><i class="bi bi-search"></i></a></li>
                            <?php endif; ?>
                        </ul>
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 d-none d-lg-flex">
                            <?php
                            // This feature allows modules to inject top level menu items
                            $injectedModules = array_merge(...array_values($w->callHook('core_template', 'topmenu')));
                            $base_modules = $w->modules();
                            array_push($base_modules, ...$injectedModules);

                            $printActiveFlag = function ($module, $module_service) use ($w) {
                                if (method_exists($module_service, "isInInjectedTopLevelModule")) {
                                    return $module_service::isInInjectedTopLevelModule($w) ? 'active' : '';
                                }
                                
                                $ignoreActiveList = Config::get("{$module}.ignore_topmenu_active");
                                if (!empty($ignoreActiveList) && in_array($w->_module . '-' . $w->_submodule, $ignoreActiveList)) {
                                    return '';
                                }

                                return $w->_module == $module ? 'active' : '';
                            };

                            foreach ($base_modules as $module) :
                                $module_service = ucfirst($module) . "Service";

                                // We do this by requiring the injected module to have a service class as well as a serviceConfig method
                                // this serviceConfig method needs to return an array consistent with an actual module config
                                if (!in_array($module, $w->modules()) && (new \ReflectionClass($module_service))->implementsInterface("InjectableModuleInterface")) {
                                    Config::enableSandbox();
                                    Config::promoteSandbox();
                                    Config::set($module, $module_service::serviceConfig());
                                }

                                // Check if config is set to display on topmenu
                                if ((Config::get("{$module}.topmenu") && Config::get("{$module}.active"))) :
                                    // Check for navigation

                                    $array = [];
                                    $menu_link = method_exists($module_service, "menuLink") ? $module_service::getInstance($w)->menuLink() : $w->menuLink($module, is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"), $array, null, null, "nav-link");
                                    if ($menu_link !== false) :
                                        if (method_exists($module . "Service", "navList") || method_exists($module . "Service", "navigation")) : ?>
                                            <li class="nav-item dropdown <?php echo $printActiveFlag($module, $module_service); ?>" id="topnav_<?php echo $module; ?>">
                                                <a class="nav-link dropdown-toggle caret-off" href="#" id="topnav_<?php echo $module; ?>_dropdown_link" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <?php echo is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"); ?>
                                                </a>
                                                <?php // Try and get a badge count for the menu item
                                                $module_navigation = method_exists_overloaded($module_service::getInstance($w), "navList") ? $module_service::getInstance($w)->navList($w) : $module_service::getInstance($w)->navigation($w);

                                                // Invoke hook to inject extra navigation
                                                $hook_navigation_items = $w->callHook($module, "extra_navigation_items", $module_navigation);
                                                if (!empty($hook_navigation_items)) {
                                                    foreach ($hook_navigation_items as $hook_navigation_item) {
                                                        if (is_array($hook_navigation_item)) {
                                                            $module_navigation = array_merge($module_navigation, $hook_navigation_item);
                                                        } else {
                                                            $module_navigation[] = $hook_navigation_item;
                                                        }
                                                    }
                                                } ?>
                                                <div class="dropdown-menu" aria-labelledby="topnav_<?php echo $module; ?>_dropdown_link">
                                                    <?php foreach ($module_navigation as $module_nav) :
                                                        if (is_string($module_nav)) {
                                                            echo $module_nav;
                                                            continue;
                                                        } ?>
                                                        <a <?php echo $module_nav->type == MenuLinkType::Modal ? 'data-modal-target' : 'href'; ?>="<?php echo $module_nav->url; ?>"><?php echo $module_nav->title; ?></a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </li>
                                        <?php else : ?>
                                            <li class="nav-item <?php echo $printActiveFlag($module, $module_service); ?>"><?php echo $menu_link; ?></li>
                                        <?php endif; ?>
                                    <?php endif;
                                    Config::disableSandbox();
                                endif;
                            endforeach; ?>
                        </ul>

                        <ul class="navbar-nav mb-2 mb-lg-0">
                        <?php
                        $inject = $w->callHook('core_template', 'menu');
                        if (!empty($inject)) {
                            foreach ($inject as $i) {
                                echo "<li>{$i}</li>";
                            }
                        }
                        ?>
                        </ul>
                    </div>
                </nav>
                <nav aria-label="breadcrumb" class="<?php echo count($active_modules) <= 9 ? "container-xl" : "container-fluid"; ?>" id="breadcrumbs">
                    <ol class="breadcrumb pt-1">
                        <?php
                        $breadcrumbs = History::get();
                        
                        if (empty($breadcrumbs)) : ?>
                            <li class="breadcrumb-item active align-middle" aria-current="page">Your history will appear here</li>
                        <?php endif;
                        $isFirst = true && $breadcrumbs !== null && ($_SERVER['REQUEST_URI'] === key($breadcrumbs));
                        foreach ($breadcrumbs ?? [] as $path => $value) :
                            if (!AuthService::getInstance($w)->allowed($path)) {
                                continue;
                            }
                            if ($isFirst) : ?>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php echo html_entity_decode($value['name']); ?>"><?php echo html_entity_decode($value['name']); ?></span>
                                </li>
                            <?php else : ?>
                                <li class="breadcrumb-item">
                                    <a href='<?php echo $path; ?>' data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php echo html_entity_decode($value['name']); ?>"><?php echo html_entity_decode($value['name']); ?></a>
                                </li>
                            <?php endif;
                            $isFirst = false;
                        endforeach; ?>
                    </ol>
                </nav>
            </div>
            <div id="menu-overlay" data-toggle-menu="close"></div>
        </div>
        <div class="<?php echo $w->ctx("layout-size") == "large" ? "container-fluid px-4" : "container-xl"; ?>" id="body-content">
            <?php
            if (!empty($error)) {
                echo HtmlBootstrap5::alertBox($error, "alert-warning");
            }
            if (!empty($msg)) {
                echo HtmlBootstrap5::alertBox($msg, 'alert-success');
            }
            ?>
            <?php if (!empty($title)) : ?>
                <h1><?php echo $title; ?></h1>
            <?php endif; ?>
            <?php echo !empty($body) ? $body : ''; ?>
        </div>
    </div>
    <div class="modal" id="cmfive-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content"></div>
        </div>
    </div>
    <div id="cmfive-overlay">
        <div class="inner">
            Please wait
            <div class="spinner">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
    </div>
    <div class="cmfive-toast-message"></div>
    <?php $w->outputScripts(); ?>
</body>

</html>