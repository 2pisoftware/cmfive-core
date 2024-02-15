<?php
$theme_setting = AuthService::getInstance($w)->getSettingByKey('bs5-theme');
?>
<!DOCTYPE html>
<html class="theme theme--<?php echo !empty($theme_setting->id) ? $theme_setting->setting_value : 'light'; ?>">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon" />
    <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' - ' . $title : ''; ?></title>
    <?php
    CmfiveStyleComponentRegister::registerComponent('app', new CmfiveStyleComponent("/system/templates/base/dist/app.css"));
    CmfiveScriptComponentRegister::registerComponent('app', new CmfiveScriptComponent("/system/templates/base/dist/app.js"));

    $w->outputStyles();
    ?>
    <!-- backwards compat -->
    <script src="/system/templates/base/node_modules/vue/dist/vue.min.js"></script>
    <style>
        #cmfive-body {
            display: flex;
            background-color: #34486A !important;
        }

        #login-content {
            min-width: 500px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fefefe;
            border: 1px solid #122648;
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0px 5px 10px rgba(10, 10, 10, 0.5);
        }

        @media (max-width: 500px) {
            #login-content {
                min-width: 90%;
            }
        }
    </style>
</head>

<body id="cmfive-body" class='d-flex justify-content-center align-items-center container-fluid'>
    <div id="login-content" class='panel col-3'>
        <div class='row'>
            <?php
            if (!empty($error)) {
                echo HtmlBootstrap5::alertBox($error, "alert-warning");
            } elseif (!empty($msg)) {
                echo HtmlBootstrap5::alertBox($msg, 'alert-success');
            }
            ?>
        </div>
        <div class='row'>
            <?php if (Config::get("main.application_logo")) : ?>
                <center><img src=<?php echo Config::get("main.application_logo"); ?> class='img-fluid' alt="<?php echo Config::get("main.application_name"); ?>" /></center>
            <?php endif; ?>
            <?php if (Config::get('auth.show_application_name', true)) : ?>
                <h1 style="text-align: center;"><?php echo $w->moduleConf('main', 'application_name'); ?></h1>
            <?php endif; ?>
        </div>
        <div class='row'>
            <?php echo !empty($body) ? $body : ''; ?>
        </div>
    </div>

    <?php $w->outputScripts(); ?>
</body>

</html>