<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon"/>
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' - ' . $title : ''; ?></title>
        
        <?php
            $w->enqueueStyle(array("name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000));
            $w->enqueueStyle(array("name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 990));
            $w->enqueueStyle(array("name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 980));
            
            $w->enqueueScript(array("name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1000));
            $w->enqueueScript(array("name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 990));
            $w->enqueueScript(array("name" => "foundation.min.js", "uri" => "/system/templates/js/foundation-5.5.0/js/foundation/foundation.js", "weight" => 980));
            $w->enqueueScript(array("name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 500));
           
            $w->outputStyles();
            $w->outputScripts();
        ?>
        <?php echo (!empty($htmlheader) ? $htmlheader : ''); ?>
    </head>
    <body>
        <div class="row">
            <div class="large-6 small-10 columns small-centered">
                <div class="row small-6 small-centered">
                	<?php if (Config::get("main.application_logo")):?>
                		<center><img src="<?php echo Config::get("main.application_logo");?>" alt="<?php echo Config::get("main.application_name");?> Logo"/></center>
                	<?php endif;?>
                    <h1 style="text-align: center;"><?php echo $w->moduleConf('main','application_name'); ?></h1>
                </div>

                <?php if (!empty($error) || !empty($msg)) : ?>
                    <?php 
						$type = [];
						$nameValue='';
						if (!empty($error)) {
							$type= array("name" => "error", "class" => "warning");
							$nameValue=$error;
						} else {
							$type=array("name" => "msg", "class" => "info"); 
							$nameValue=$msg;
						}
                    ?>
                    <div data-alert class="alert-box <?php echo $type["class"]; ?>">
                        <?php echo $nameValue; ?>
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
