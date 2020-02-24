<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo !empty($title) ? $title : ''; ?></title>
        <meta charset=utf-8>
        <meta http-equiv=X-UA-Compatible content="IE=edge">
        <meta name=viewport content="width=device-width,initial-scale=1">
        <?php
        $w->enqueueStyle(["name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010]);
        // $w->enqueueStyle(["name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005]);
        $w->enqueueStyle(["name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000]);
        $w->enqueueStyle(["name" => "print.css", "uri" => "/system/templates/css/print.css", "weight" => 995]);
        $w->enqueueStyle(["name" => "datePicker.css", "uri" => "/system/templates/css/datePicker.css", "weight" => 980]);
        $w->enqueueStyle(["name" => "jquery-ui-1.8.13.custom.css", "uri" => "/system/templates/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css", "weight" => 970]);
        
        $w->enqueueScript(['name' => 'vue.js', 'uri' => '/system/templates/js/vue.js', 'weight' => 2000]);

        $w->enqueueScript(["name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1010]);
        $w->enqueueScript(["name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 1000]);
        $w->enqueueScript(["name" => "jquery-ui-1.10.4.custom.min.js", "uri" => "/system/templates/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js", "weight" => 960]);
        $w->enqueueScript(["name" => "jquery-ui-timepicker-addon.js", "uri" => "/system/templates/js/jquery-ui-timepicker-addon.js", "weight" => 950]);

        $w->outputStyles();
        $w->outputScripts();
        $w->loadVueComponents();
        ?>
        <script>
            var $ = $ || jQuery;
        </script>
    </head>
    <body>
        <?php echo $body; ?>
    </body>
</html>