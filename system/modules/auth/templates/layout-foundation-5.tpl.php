<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon"/>
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' - ' . $title : ''; ?></title>

        <?php
            $w->enqueueStyle(["name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000]);
            $w->enqueueStyle(["name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 990]);
            $w->enqueueStyle(["name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 980]);

            $w->enqueueScript(["name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1000]);
            $w->enqueueScript(["name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 990]);
            $w->enqueueScript(["name" => "foundation.min.js", "uri" => "/system/templates/js/foundation-5.5.0/js/foundation/foundation.js", "weight" => 980]);
            $w->enqueueScript(["name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 500]);

            $w->enqueueStyle(["name" => "auth.css", "uri" => "/system/modules/auth/assets/css/auth.css", "weight" => 1000]);

            $w->outputStyles();
            $w->outputScripts();
        ?>
        <?php echo (!empty($htmlheader) ? $htmlheader : ''); ?>
    </head>
    <body>
        <div class="row">
            <div class="large-6 small-10 columns small-centered">
                <?php if (Config::get('system.test_mode', false) === true) : ?>
                    <div class="row">
                        <div data-alert class="alert-box alert-info">
                            <?php echo Config::get('system.test_mode_message'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row small-6 small-centered">
                    <?php if (Config::get("main.application_logo")) : ?>
                        <center><img src="<?php echo Config::get("main.application_logo");?>" alt="<?php echo Config::get("main.application_name");?>"/></center>
                    <?php endif;?>
                    <?php if (Config::get('auth.show_application_name', true)) : ?>
                        <h1 style="text-align: center;"><?php echo $w->moduleConf('main', 'application_name'); ?></h1>
                    <?php endif; ?>
                </div>

                <?php if (!empty($error) || !empty($msg)) : ?>
                    <div data-alert class="alert-box <?php echo !empty($error) ? 'alert-warning' : 'alert-info'; ?>">
                        <?php echo !empty($error) ? $error : $msg; ?>
                        <a href="#" class="close">&times;</a>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php echo !empty($body) ? $body : ''; ?>
                </div>
            </div>
        </div>
        <script>
            try {
                jQuery(document).foundation();
            } catch (e) {
                console.log(e);
            }
        </script>
    </body>
</html>
