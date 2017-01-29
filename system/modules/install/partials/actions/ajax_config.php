<?php
    
function ajax_config_ALL(Web $w, $params)
{
    //echo "<pre>SESSION:\n " . print_r($_SESSION['install']['saved'], true) . "</pre>";

    $tpl = new WebTemplate();
    $tpl->set_vars($_SESSION['install']['saved']);
    $templateFile = "<?php\n";
    $templateFile .= $tpl->fetch('system/modules/install/assests/config.tpl.php');
    //file_put_contents('config.php', $templateFile);
    
    // Render data in config
    ConfigService::initConfigFile();
    ConfigService::saveConfigData($_SESSION['install']['saved']);
    ConfigService::writeConfigToProject();
}