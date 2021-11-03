<?php
$theme_setting = AuthService::getInstance($w)->getSettingByKey('bs5-theme');
?>
<!DOCTYPE html>
<html class="theme theme--<?php echo !empty($theme_setting->id) ? $theme_setting->setting_value : 'dark'; ?>">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon"/>
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' - ' . $title : ''; ?></title>
        <?php
        CmfiveStyleComponentRegister::registerComponent('app', new CmfiveStyleComponent("/system/templates/base/dist/app.css"));
        CmfiveScriptComponentRegister::registerComponent('app', new CmfiveScriptComponent("/system/templates/base/dist/app.js"));

        $w->outputStyles();
        ?>
        <!-- backwards compat -->
        <script src="/system/templates/base/node_modules/vue/dist/vue.min.js"></script>
        <style>
            #login-content {
                min-width: 500px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            @media (max-width: 500px) {
                #login-content {
                    min-width: 90%;
                }
            }
        </style>
    </head>
    <body id="cmfive-body">
        <div id="app">
            <div id="login-content">
                <?php
                if (!empty($error)) {
                    echo HtmlBootstrap5::alertBox($error, "alert-warning");
                } elseif (!empty($msg)) {
                    echo HtmlBootstrap5::alertBox($msg, 'alert-success');
                }
                ?>
                <?php if (!empty($title)) : ?>
                    <h1><?php echo $title; ?></h1>
                <?php endif; ?>
                <?php echo !empty($body) ? $body : ''; ?>
            </div>
        </div>
        <?php $w->outputScripts(); ?>
    </body>
</html>