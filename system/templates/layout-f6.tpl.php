<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon"/>
        <title><?php echo ucfirst($w->currentModule()); ?><?php echo!empty($title) ? ' - ' . $title : ''; ?></title>

        <?php
         CmfiveScriptComponentRegister::registerComponent('polyfill', new CmfiveScriptComponent('https://cdn.polyfill.io/v2/polyfill.min.js?features=es6'));
        CmfiveScriptComponentRegister::registerComponent('moment', new CmfiveScriptComponent('/system/templates/js/moment.min.js'));
        CmfiveScriptComponentRegister::registerComponent('axios', new CmfiveScriptComponent('/system/templates/js/axios.min.js'));     

		CmfiveStyleComponentRegister::registerComponent('foundation-5', new CmfiveStyleComponent(
			"/system/templates/js/foundation-5.5.0/css/foundation.min.css"
		));

        // Old styles
        CmfiveStyleComponentRegister::registerComponent('style', new CmfiveStyleComponent("/system/templates/css/style.css"));

        // New styles
        CmfiveStyleComponentRegister::registerComponent('hind_vadodara', new CmfiveStyleComponent("https://fonts.googleapis.com/css?family=Hind+Vadodara:300,400,700", [], true));
        CmfiveStyleComponentRegister::registerComponent('app', new CmfiveStyleComponent("/system/templates/scss/app.scss", ['/system/templates/scss/']));
        
        CmfiveScriptComponentRegister::registerComponent('modernizr', new CmfiveScriptComponent('/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js', ['weight' => 1010]));
        CmfiveScriptComponentRegister::registerComponent('jquery', new CmfiveScriptComponent('/system/templates/js/foundation-5.5.0/js/vendor/jquery.js', ['weight' => 1009]));
        CmfiveScriptComponentRegister::registerComponent('foundation', new CmfiveScriptComponent('/system/templates/js/foundation-5.5.0/js/foundation.min.js', ['weight' => 1008]));
        CmfiveScriptComponentRegister::registerComponent('jquery-tablesorter', new CmfiveScriptComponent('/system/templates/js/tablesorter/jquery.tablesorter.js', ['weight' => 990]));
        
        CmfiveScriptComponentRegister::registerComponent('vue', new CmfiveScriptComponent('/system/templates/js/vue.js', ['weight' => 1010]));
        CmfiveScriptComponentRegister::registerComponent('main', new CmfiveScriptComponent("/system/templates/js/main.js"));
        $fontawesome_js = new CmfiveScriptComponent("/system/templates/js/fontawesome-all.min.js");
        $fontawesome_js->defer = null;
        CmfiveScriptComponentRegister::registerComponent('fa-shim', new CmfiveScriptComponent("/system/templates/js/fa-v4-shims.min.js"));
        CmfiveScriptComponentRegister::registerComponent('fontawesome5', $fontawesome_js);

        CmfiveScriptComponentRegister::registerComponent('slideout', new CmfiveScriptComponent("/system/templates/js/slideout-1.0.1/dist/slideout.min.js"));
        CmfiveScriptComponentRegister::registerComponent('toast', new CmfiveScriptComponent("/system/templates/js/Toast.js"));
        CmfiveStyleComponentRegister::registerComponent('toast', new CmfiveStyleComponent("/system/templates/css/Toast.scss", ['/system/templates/scss/']));

        
        // Print registered vue component links
        /** @var Web $w */
        $w->loadVueComponents();

        $w->outputStyles();
        $w->outputScripts();

        ?>
        <script type="text/javascript">
            var $ = $ || jQuery;
            $(document).ready(function() {
                $("table.tablesorter").tablesorter({dateFormat: "uk", widthFixed: true, widgets: ['zebra']});
                $(".tab-head").children("a").each(function() {
                    if (this.href.indexOf("#") != -1) {
                        $(this).bind("click", {alink: this}, function(event) {
                            changeTab(event.data.alink.hash);
                            return false;
                        });
                    }
                });

                // Change tab if hash exists
                var hash = window.location.hash.split("#")[1];
                if (hash && hash.length > 0) {
                    changeTab(hash);
                } else {
                    $(".tab-head > a:first").trigger("click");
                }
                
                // Set up CodeMirror instances if any
                bindCodeMirror();
                
                // Adjust the breadcrumbs div if it's content is longer than the viewport
                var breadcrumbs = $('.cmfive_breadcrumbs');
                if (breadcrumbs.length) {
                    if (breadcrumbs[0].scrollWidth > $(window).width()) {
                        breadcrumbs.css('height', (breadcrumbs.height() + 20) + "px");
                    }
                }

				// Admin clear cache button function
				$('#admin_clear_cache').bind('click', function(e) {
					$('#admin_clear_cache').css('color', '#CD0000');
					$.get($(this).attr('href'), function() {
						setTimeout(function() {
							$('#admin_clear_cache').css('color', '#4B6995');
							$('#admin_clear_cache .clear_cache_icon').removeClass('fi-refresh').addClass('fi-check');
							setTimeout(function() {
								$('#admin_clear_cache').css('color', '#FFF');
								$('#admin_clear_cache .clear_cache_icon').removeClass('fi-check').addClass('fi-refresh');
							}, 500);
						}, 500);
					});
					e.preventDefault();
					return false;
				});
                // Search function shortcut listener
                $(document).on('keydown', function ( e ) {
                    if ((e.ctrlKey || e.metaKey) && e.which === 70) {
                        $('#cmfive_search_button').click();
                        return false;
                    }
                });
				if(jQuery('.enable_drop_attachments').length !== 0) {
					globalFileUpload.init();
				}
				
				// Look for cmfive__count-* classes and count the instances of *
				$("span[class^='cmfive__count-'], span[class*=' cmfive__count-']").each(function(index, element) {
					var classList = this.className.split(/\s+/);
					for(var i in classList) {
						if (classList[i].indexOf('cmfive__count-') > -1) {
							$(this).text($('.' + classList[i].substring(classList[i].indexOf('-') + 1)).length);
						}
					}
				});
            });

            // Try and prevent multiple form submissions
            $("input[type=submit]").click(function() {
                $(this).hide();
            });
			
            $(document).bind('cbox_complete', function() {
                $("input[type=submit]").click(function() {
                    $(this).hide();
                });
            });
			
			// Focus first form element when the modal opens
			$(document).ready(function() {
				$('.body form:first :input:visible:enabled:first').not('.no-focus').focus();
				
				$(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
					$('form:visible:first :input:visible:enabled:first', $(this)).not('.no-focus').focus();
				});
			});
        </script>
    </head>
    <body>
        <!-- Side (slideout) menu -->
        <nav id='cmfive-side-menu' class='cmfive-nav menu slideout-menu slideout-menu-left'>
            <div>
                <ul class="accordion side-nav" data-accordion>
                    <?php foreach ($w->modules() as $module) :
                        // Check if config is set to display on topmenu
                        if (Config::get("{$module}.topmenu") && Config::get("{$module}.active")) :
                            // Check for navigation
                            $service_module = ucfirst($module);
                            $menu_link = method_exists($w->$service_module, "menuLink") ? $w->$service_module->menuLink() : $w->menuLink($module, is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"));
                            if ($menu_link !== false) :
                                if (method_exists($module . "Service", "navigation")) : ?>
                                    <li class="accordion-navigation <?php echo $w->_module == $module ? 'active' : ''; ?>" id="topnav_<?php echo $module; ?>">
                                        <a href='#side_menu_panel-<?php echo $module; ?>'><?php echo is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"); ?><?php // echo $menu_link; ?></a>
                                    <?php // Try and get a badge count for the menu item
                                        $module_navigation = $w->service($module)->navigation($w);
                                        
                                        // Invoke hook to inject extra navigation
                                        $hook_navigation_items = $w->callHook($module, "extra_navigation_items", $module_navigation);
                                        if (!empty($hook_navigation_items)) {
                                            foreach($hook_navigation_items as $hook_navigation_item) {
                                                if (is_array($hook_navigation_item)) {
                                                    $module_navigation = array_merge($module_navigation, $hook_navigation_item);
                                                } else {
                                                    $module_navigation[] = $hook_navigation_item;
                                                }
                                            }
                                        }

                                        if (!empty($module_navigation)) : ?>
                                            <div id='side_menu_panel-<?php echo $module; ?>' class="content">
                                                <?php echo Html::ul($module_navigation, null, "side-nav"); ?>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php else: ?>
                                    <li class='accordion-navigation <?php echo $w->_module == $module ? 'active' : ''; ?>'><?php echo $menu_link; ?></li>
                                <?php endif; ?>
                            <?php endif;
                        endif;
                    endforeach; ?>
                </ul>
                
                <!-- Footer -->
                <div class="row-fluid align-center footer clearfix">
                    <div class="columns small-12">
                        <div class='text-center'>
                            Copyright &#169; <?php echo date('Y'); ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $w->moduleConf('main', 'company_url'); ?>"><?php echo $w->moduleConf('main', 'company_name'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main id='cmfive-main-content' class='slideout-panel slideout-panel-left'>
            <div class="loading_overlay" <?php echo $w->request('show_overlay') == null ? 'style="display:none;"' : ''; ?>>
                <div class="circle"></div>
    			<img class="center_image" width="100px" height="100px" src="/system/templates/img/cmfive_V_logo.png" />
                <h4 class="subheader">Please wait</h4>
            </div>
    		<div class="global_file_drop_area" style="display:none;" id="global_file_drop_area">
    			<div class="global_file_drop_overlay_init">
    				<h4 class="subheader">Drop files here...</h4>
    			</div>
    		</div>
    		<div class="global_file_drop_overlay" id="global_file_drop_overlay" style="display:none;">
    			<div class="global_file_drop_overlay_loading">
    				<div class="circle"></div>
    				<img class="center_image" width="100px" height="100px" src="/system/templates/img/cmfive_V_logo.png" />
    				<h4 class="subheader">Uploading (0%)</h4>
    			</div>
    		</div>
    		
    		<?php if (Config::get('system.test_mode') === true) : ?>
    			<div class="row-fluid">
    				<div class="small-12">
    					<div data-alert class="alert-box warning" style="margin-bottom: 0px; padding: 5px 0px;">
    						<h4 style="font-weight: lighter; text-align: center; color: white; padding: 5px 0px 0px 0px;"><?php echo Config::get('system.test_mode_message')?></h4>
    					</div>
    				</div>
    			</div>
    		<?php endif; ?>
            
            <nav class="top-bar cmfive-nav" data-topbar role="navigation">
                <section class="top-bar-section">

                    <!-- Right Nav Section -->
                    <ul class="right">
                        <?php
                            $inject = $w->callHook('core_template', 'menu');
                            if (!empty($inject)) :
                                foreach($inject as $i) : ?>
                                    <li><?php echo $i; ?></li>
                                <?php endforeach;
                            endif;
                        ?>
                        <li><span><?php echo Config::get('main.company_name'); ?></span>    </li>
                    </ul>

                    <!-- Left Nav Section -->
                    <ul class="left">
                        <li>
                            <a href='#' class='side-menu-toggle-button'><span class='fas fa-bars fa-2x'></span></a>
                        </li>
                        
                        <li>
                            <a href='<?php echo $w->Main->getUserRedirectURL(); ?>'>
                                <?php if (!empty(Config::get('main.application_logo'))) : ?>
                                    <img class='home_logo' src='<?php echo Config::get('main.application_logo'); ?>' />
                                <?php else: ?>
                                    <span class='fi-home show-for-medium-up'></span><span class='show-for-small'>Home</span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="has-dropdown">
                            <a href="#">
                                <img class='comment_avatar' src='https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim(@$w->Auth->user()->getContact()->email))); ?>?d=identicon' />
                            </a>
                            <?php
                            echo Html::ul(
                                array(
                                    $w->menuBox("auth/profile/box", $w->Auth->user()->getShortName()),
                                    $w->menuLink("auth/logout", "Logout")
                                ), null, "dropdown");
                            ?>    
                        </li>
                    </ul>
                </section>
            </nav>

            <nav class="cmfive-phone-nav" data-topbar role="navigation">
                <div class='left'><a href='#' class='side-menu-toggle-button'><span class='fas fa-bars fa-2x'></span></a></div>
                <div class='text-right cmfive__company-name'><span><?php echo Config::get('main.company_name'); ?></span></div>
            </nav>

            <!-- Breadcrumbs -->
            <div class="row-fluid breadcrumb-container">
                <?php echo Html::breadcrumbs([], $w); ?>
                <span class='icon-container'>
                    <?php if ($w->Auth->allowed('help/view')) {
                            echo Html::box(WEBROOT . "/help/view/" . $w->_module . ($w->_submodule ? "-" . $w->_submodule : "") . "/" . $w->_action, "<span class='fas fa-info show-for-medium-up'></span><span class='show-for-small-only'>Help</span>", false, true, 750, 500, "isbox", null, null, null, 'cmfive-help-modal');
                        }
                        echo Html::box("/search", "<span class='fas fa-search show-for-medium-up'></span><span class='show-for-small-only'>Search</span>", false, false, null, null, null, "cmfive_search_button"); ?>
                </span>
            </div>
            
            <!-- Action content -->
            <div class="row-fluid body cmfive__no-margin-phone">
                <div class='small-12 columns'>
                    <!-- Title and messages -->
                    <?php if (empty($hideTitle) && !empty ($title)):?>
                        <header><?php echo $title; ?></header>
                    <?php endif; ?>
                    <?php if (!empty($error)) : ?>
                        <div class='callout alert' data-closable>
                            <?php echo $error; ?>
                            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($msg)) : ?>
                       <div class="callout primary" data-closable>
                            <?php echo $msg; ?>
                            <button class="close-button" aria-label="Dismiss message" type="button" data-close>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php echo !empty($body) ? $body : ''; ?>
                </div>
            </div>

            <!-- Slider overlay -->
            <div id='slider_overlay'></div>
        </main>

        <!-- Modals -->
        <div id="cmfive-modal" class="reveal-modal xlarge" data-reveal></div>
        <div id="cmfive-help-modal" class="reveal-modal xlarge" data-reveal></div>
        
        <script>
            var slideout = new Slideout({
                'panel': document.getElementById('cmfive-main-content'),
                'menu': document.getElementById('cmfive-side-menu'),
                'padding': 256,
                'tolerance': 140
            });

            slideout.on('beforeopen', function() {
                $("#slider_overlay").fadeIn(100);
            }).on('beforeclose', function() {
                $("#slider_overlay").fadeOut(100);
            });

            document.querySelectorAll('.side-menu-toggle-button').forEach(function(toggle_button) {
                toggle_button.addEventListener('click', function() {
                    slideout.open();
                    // $("#slider_overlay").fadeIn(100);
                });
            });

            $("#slider_overlay").click(function(){
                slideout.close();
                // $("#slider_overlay").fadeOut(100);
            })

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
