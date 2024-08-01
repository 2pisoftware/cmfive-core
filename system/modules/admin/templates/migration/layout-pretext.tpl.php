<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon"/>
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo!empty($title) ? ' - ' . $title : ''; ?></title>

        <?php
        $w->enqueueStyle(array("name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010));
        $w->enqueueStyle(array("name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005));
        $w->enqueueStyle(array("name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000));
		$w->enqueueStyle(array("name" => "print.css", "uri" => "/system/templates/css/print.css", "weight" => 995));
        $w->enqueueStyle(array("name" => "tablesorter.css", "uri" => "/system/templates/css/tablesorter.css", "weight" => 990));
        $w->enqueueStyle(array("name" => "datePicker.css", "uri" => "/system/templates/css/datePicker.css", "weight" => 980));
        $w->enqueueStyle(array("name" => "jquery-ui-1.8.13.custom.css", "uri" => "/system/templates/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css", "weight" => 970));
        $w->enqueueStyle(array("name" => "liveValidation.css", "uri" => "/system/templates/css/liveValidation.css", "weight" => 960));
        $w->enqueueStyle(array("name" => "colorbox.css", "uri" => "/system/templates/js/colorbox/colorbox/colorbox.css", "weight" => 950));
        $w->enqueueStyle(array("name" => "jquery.asmselect.css", "uri" => "/system/templates/css/jquery.asmselect.css", "weight" => 940));
        $w->enqueueStyle(array("name" => "foundation-icons.css", "uri" => "/system/templates/font/foundation-icons/foundation-icons.css", "weight" => 930));
        $w->enqueueStyle(array("name" => "codemirror.css", "uri" => "/system/templates/js/codemirror-4.4/lib/codemirror.css", "weight" => 900));
        
        $w->enqueueScript(['name' => 'vue.js', 'uri' => '/system/templates/js/vue.js', 'weight' => 2000]);

        $w->enqueueScript(array("name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1010));
        $w->enqueueScript(array("name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 1000));
        $w->enqueueScript(array("name" => "jquery.tablesorter.js", "uri" => "/system/templates/js/tablesorter/jquery.tablesorter.js", "weight" => 990));
        $w->enqueueScript(array("name" => "jquery.tablesorter.pager.js", "uri" => "/system/templates/js/tablesorter/addons/pager/jquery.tablesorter.pager.js", "weight" => 980));
        $w->enqueueScript(array("name" => "jquery.colorbox-min.js", "uri" => "/system/templates/js/colorbox/colorbox/jquery.colorbox-min.js", "weight" => 970));
        $w->enqueueScript(array("name" => "jquery-ui-1.10.4.custom.min.js", "uri" => "/system/templates/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js", "weight" => 960));

        $w->enqueueScript(array("name" => "jquery-ui-timepicker-addon.js", "uri" => "/system/templates/js/jquery-ui-timepicker-addon.js", "weight" => 950));
        $w->enqueueScript(array("name" => "livevalidation.js", "uri" => "/system/templates/js/livevalidation.js", "weight" => 940));
        $w->enqueueScript(array("name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 995));
        $w->enqueueScript(array("name" => "jquery.asmselect.js", "uri" => "/system/templates/js/jquery.asmselect.js", "weight" => 920));
        $w->enqueueScript(array("name" => "ckeditor.js", "uri" => "/system/templates/js/ckeditor/ckeditor.js", "weight" => 900));
        $w->enqueueScript(array("name" => "Chart.js", "uri" => "/system/templates/js/chart-js/dist/Chart.min.js", "weight" => 890));
        
        $w->enqueueScript(array("name" => "moment.js", "uri" => "/system/templates/js/moment.min.js", "weight" => 880));
        
        // Code mirror
        $w->enqueueScript(array("name" => "codemirror.js", "uri" => "/system/templates/js/codemirror-4.4/codemirror-compressed.js", "weight" => 880));
        
        $w->loadVueComponents();

        $w->outputStyles();
        $w->outputScripts();
        ?>
        
    </head>
    <body>
    <?php /** @var Web */ ?>
        
		<?php if (Config::get('system.test_mode') === true) : ?>
			<div class="row-fluid">
				<div class="small-12">
					<div data-alert class="alert-box warning" style="margin-bottom: 0px; padding: 5px 0px;">
						<h4 style="font-weight: lighter; text-align: center; color: white; padding: 5px 0px 0px 0px;"><?php echo Config::get('system.test_mode_message')?></h4>
					</div>
				</div>
			</div>
		<?php endif; ?>
        
        <div class="row-fluid body">
            <?php // Body section w/ message and body from template ?>
            <div class="row-fluid <?php // if(!empty($boxes)) echo "medium-10 small-12 "; ?>">
                <?php if (empty($hideTitle) && !empty ($title)):?>
                <div class="row-fluid small-12">
                    <h3 class="header"><?php echo $title; ?></h3>
                </div>
                <?php endif;?>
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

                <div class="row-fluid" style="overflow: hidden;">
                    <?php echo !empty($body) ? $body : ''; ?>
                </div>
            </div>


            <div id="cmfive-modal" class="reveal-modal xlarge" data-reveal></div>
            <div id="cmfive-help-modal" class="reveal-modal xlarge" data-reveal></div>
            <script type="text/javascript" src="/system/templates/js/foundation-5.5.0/js/foundation.min.js"></script>
            <script type="text/javascript" src="/system/templates/js/foundation-5.5.0/js/foundation/foundation.clearing.js"></script>
            <script>
            $(document).foundation({
                reveal : {
                    animation_speed: <?php echo defaultVal(Config::get('core_template.foundation.reveal.animation_speed'), 150); ?>,
                    animation: '<?php echo defaultVal(Config::get('core_template.foundation.reveal.animation'), 'fade'); ?>',
					close_on_background_click: <?php echo defaultVal(Config::get('core_template.foundation.reveal.close_on_background_click'), 'true'); // Must be string value in PHP ?>
				},
				accordion: {
					multi_expand: <?php echo defaultVal(Config::get('core_template.foundation.accordion.multi_expand'), 'true'); ?>,
				}
			});
            
            var modal_history = [];
            var modal_history_pop = false;
            
            // Automatically append the close 'x' to reveal modals
            $(document).on('opened', '[data-reveal]', function () {
                $(this).css('top', $(document).scrollTop() + 100);
                $(this).append("<a class=\"close-reveal-modal\">&#215;</a>");
                modal_history.push();
                bindModalLinks();
            });
            
            function bindModalLinks() {
                // Stop a links and follow them inside the reveal modal
                $("#cmfive-modal a:not(#modal-back)").click(function(event) {                    
                    if ($(this).hasClass("close-reveal-modal")) {
                        $("#cmfive-modal").foundation("reveal", "close");
                    } else {
						// No one is using the help system at the moment
						// Therefore no real need for a dynamic modal history
						return true;
                    }
                    return false;
                });
                
				$("#cmfive-help-modal a:not(#modal-back)").click(function(event) {                    
                    if ($(this).hasClass("close-reveal-modal")) {
                        $("#cmfive-modal").foundation("reveal", "close");
                    } else {
                        if ($(this).attr('href')[0] === "#") {
                            return true;
                        } else {
                            // Add href to history if the href wasnt the last item in the stack and that we arent the back link
                            if (modal_history.indexOf($(this).attr('href')) !== modal_history.length) {
                                modal_history.push($(this).attr('href'));
                                modal_history_pop = true;
                            }
                            changeModalWindow($(this).closest('.reveal-modal'), $(this).attr('href'));
                        }
                    }
                    return false;
                });
                
                // Bind back traversal to modal window
                $("#cmfive-modal #modal-back, #cmfive-help-modal #modal-back").click(function(event) {
                    // event.preventDefault();
                    if (modal_history.length > 0) {
                        // When you click a link, THAT link goes onto the stack.
                        // However we want the one before it.
                        // The modal_history_pop prevents us from popping twice (if back is pressed twice in a row
                        // for example)
                        if (modal_history_pop) {
                            modal_history.pop();
                            modal_history_pop = false;
                        }
                        if (modal_history.length > 0) {
                            changeModalWindow($(this).closest('.reveal-modal'), modal_history.pop());
                        }
//                        console.log(modal_history);
                    } 
                    return false;
                });
            }
            
            // Updates the modal window by content from ajax request to uri
            function changeModalWindow(object, uri) {
                $.get(uri, function(data) {
                    object.html(data + "<a class=\"close-reveal-modal\">&#215;</a>");
                    bindModalLinks();
                });
            }
        </script>
    </body>
</html>
