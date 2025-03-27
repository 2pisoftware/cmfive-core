<!DOCTYPE html>
<html class="theme theme--light">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="<?php echo Config::get('main.favicon_path', '/system/templates/img/favicon.ico'); ?>" type="image/x-icon" />
    <title><?php echo Config::get('main.application_name', ''); ?><?php echo !empty($title) ? ' &#x2022; ' . $title : ''; ?></title>
    <?php
    CmfiveStyleComponentRegister::registerComponent('app', new CmfiveStyleComponent("/system/templates/base/dist/app.css"));
    CmfiveScriptComponentRegister::registerComponent('app', new CmfiveScriptComponent("/system/templates/base/dist/app.js", ['type' => 'module']));
    $w->outputStyles();
    ?>
    <style>
        #cmfive-body {
            background-color: #34486A !important;
        }

        #login-content {
            min-width: 500px;
            background-color: #fefefe;
            border: 1px solid #122648;
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0px 5px 10px rgba(10, 10, 10, 0.5);
        }

        /* 575.98px is BS5's sm breakpoint */
        @media (max-width: 575.98px) {
            #login-content {
                width: 83.33333%;
                padding: 20px 30px;
                min-width: 90%;
            }
        }
    </style>
</head>

<body id="cmfive-body" class='d-flex justify-content-center align-items-center container '>
    <div id="login-content" class='panel position-absolute top-50 start-50 translate-middle'>
        <div class='row mx-1'>
            <?php
            if (!empty($error)) {
                echo HtmlBootstrap5::alertBox($error, "alert-warning");
            } elseif (!empty($msg)) {
                echo HtmlBootstrap5::alertBox($msg, 'alert-success');
            }
            ?>
        </div>
        <div class='row '>
            <?php if (Config::get("main.application_logo")) : ?>
                <center><img src="<?php echo Config::get("main.application_logo"); ?>" class='img-fluid' alt="<?php echo Config::get("main.application_name"); ?>" /></center>
            <?php endif; ?>
        </div>
        <div class="row mt-3">
            <?php if (Config::get('auth.show_application_name', true)) : ?>
                <h2 style="text-align: center;"><?php echo $w->moduleConf('main', 'application_name'); ?></h2>
            <?php endif; ?>
        </div>
        <div class='row'>
            <?php echo !empty($body) ? $body : ''; ?>
        </div>
    </div>

    <?php $w->outputScripts(); ?>
</body>

</html>