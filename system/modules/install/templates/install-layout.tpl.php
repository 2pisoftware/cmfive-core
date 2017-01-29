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
        $w->enqueueStyle(array("name" => "font-awesome.min.css", "uri" => "/system/modules/install/assets/css/font-awesome.min.css", "weight" => 1004));
        $w->enqueueStyle(array("name" => "install.css", "uri" => "/system/modules/install/assets/css/install.css", "weight" => 1003));
        //$w->enqueueStyle(array("name" => "timezonepicker.css", "uri" => "/system/modules/install/assets/css/timezonepicker.css", "weight" => 1003));
            
            
        $w->enqueueScript(array("name" => "jquery.js", "uri" => "/system/docs/api/js/jquery-1.11.0.min.js", "weight" => 1004));
        $w->enqueueScript(array("name" => "maphilight.js", "uri" => "/system/modules/install/assets/js/jquery.maphilight.min.js", "weight" => 1003));
        $w->enqueueScript(array("name" => "timezonepicker.js", "uri" => "/system/modules/install/assets/js/jquery.timezone-picker.js", "weight" => 1002));
        $w->enqueueScript(array("name" => "install.js", "uri" => "/system/modules/install/assets/js/install.js", "weight" => 1001));
            
		$w->outputStyles();
		$w->outputScripts();
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
                    <div class='install_breadcrumbs'>
                        <ul>
<?php
                            if(isset($steps) && is_array($steps))
                            {
                                foreach($steps as $i => $title)
                                {
                                    echo "                            <li" . ($step >= intval($i) ? " class='complete'" : "") . ">\n" .
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
                    <form method="post" action="<?= $form_action; ?>" id='install_form'>
                        <input type='hidden' value='<?= $step ?>' name='step'/>
                        <?php if(!empty($body)) echo $body; ?>

<script type="text/javascript">
<!--

jQuery(document).ready(function($){
    var errors = '<?= addslashes(preg_replace("/[\n\r]+/", "", $w->Install->formatErrors($stepName))) ?>';
    if(errors.length)
    {
        CmFiveAjax.showError(errors);
    }
                       
    var warnings = '<?= addslashes(preg_replace("/[\n\r]+/", "", $w->Install->formatErrors($stepName, "warnings"))) ?>';
    if(warnings.length)
    {
        CmFiveAjax.showWarning(warnings);
    }
});

//-->
</script>
                    </form>
                </div>
            </div>
        </div>
	</body>
</html>