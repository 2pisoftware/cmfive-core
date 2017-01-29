<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install Cmfive</title>

<?php
    $w->enqueueStyle(array("name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010));
    $w->enqueueStyle(array("name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005));
    $w->enqueueStyle(array("name" => "install.css", "uri" => "/system/modules/install/assets/css/install.css", "weight" => 1004));
    //$w->enqueueStyle(array("name" => "timezonepicker.css", "uri" => "/system/modules/install/assets/css/timezonepicker.css", "weight" => 1003));
    
    
    $w->enqueueScript(array("name" => "jquery.js", "uri" => "/system/docs/api/js/jquery-1.11.0.min.js", "weight" => 1004));
   // $w->enqueueScript(array("name" => "install.js", "uri" => "/system/modules/install/assets/js/install.js", "weight" => 1001));
    
    $w->outputStyles();
    $w->outputScripts();
    
    $error = 'A config file already exists.';
    ?>
</head>
<body>
<div class="row body">
    <div class="row-fluid">
        <div class="row-fluid small-12">
            <h4 class="header"><img class="hide-for-small-down" src="/system/templates/img/cmfive-logo.png" alt="Cmfive" /> Installing cmfive v<?php echo CMFIVE_VERSION; ?></h4>
            </div>
            <div class="row-fluid">
                <div data-alert class="alert-box alert" style='display:<?= isset($error) ? "block" : "none"?>'>
                    <?= isset($error) ? $error : '' ?>
                </div>
                <div data-alert class="alert-box warning" style='display:<?= isset($warning) ? "block" : "none"?>'>
                    <?= isset($warning) ? $warning : '' ?>
                </div>
                <div data-alert class="alert-box success" style='display:<?= isset($info) ? "block" : "none"?>'>
                    <?= isset($info) ? $info : '' ?>
                </div>
            </div>
            <div class='install_breadcrumbs'>
                <ul>
                <?php
                    if(isset($steps) && is_array($steps))
                    {
                        foreach($steps as $i => $title)
                        {
                            echo "                            <li class='complete'>\n" .
                                 "                                <a href='" . DS .
                                 "install" . DS .  $i . DS . $title . "'>" .
                                 $i . ". " . ucwords(str_replace('-', ' ', $title)) .
                                 "</a>\n" .
                                 "                            </li>\n";
                        }
                    }
                ?>
                </ul>
            </div>
        </div>
        <div class="row-fluid" style="overflow: hidden;">
            <p>To overwrite the current configuration with a new installation <i>(eg: swap databases)</i>... please delete this file</p>
            <pre>/config.php</pre>
            <?php /*echo $w->fetchTemplate('Auth/login');*/ ?>
        </div>
    </div>
</div>
</body>
</html>