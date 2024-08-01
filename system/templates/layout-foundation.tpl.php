<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/system/templates/img/favicon.ico" type="image/x-icon" />
    <title><?php echo ucfirst($w->currentModule()); ?><?php echo !empty($title) ? ' - ' . $title : ''; ?></title>

    <?php
    $w->enqueueStyle(["name" => "normalize.css", "uri" => "/system/templates/js/foundation-5.5.0/css/normalize.css", "weight" => 1010]);
    $w->enqueueStyle(["name" => "foundation.css", "uri" => "/system/templates/js/foundation-5.5.0/css/foundation.css", "weight" => 1005]);
    $w->enqueueStyle(["name" => "style.css", "uri" => "/system/templates/css/style.css", "weight" => 1000]);
    $w->enqueueStyle(["name" => "print.css", "uri" => "/system/templates/css/print.css", "weight" => 995]);
    $w->enqueueStyle(["name" => "tablesorter.css", "uri" => "/system/templates/css/tablesorter.css", "weight" => 990]);
    $w->enqueueStyle(["name" => "datePicker.css", "uri" => "/system/templates/css/datePicker.css", "weight" => 980]);
    $w->enqueueStyle(["name" => "jquery-ui-1.8.13.custom.css", "uri" => "/system/templates/js/jquery-ui-new/css/custom-theme/jquery-ui-1.8.13.custom.css", "weight" => 970]);
    $w->enqueueStyle(["name" => "liveValidation.css", "uri" => "/system/templates/css/liveValidation.css", "weight" => 960]);
    $w->enqueueStyle(["name" => "jquery.asmselect.css", "uri" => "/system/templates/css/jquery.asmselect.css", "weight" => 940]);
    $w->enqueueStyle(["name" => "foundation-icons.css", "uri" => "/system/templates/font/foundation-icons/foundation-icons.css", "weight" => 930]);
    $w->enqueueStyle(["name" => "codemirror.css", "uri" => "/system/templates/js/codemirror-4.4/lib/codemirror.css", "weight" => 900]);

    $w->enqueueScript(['name' => 'vue.js', 'uri' => '/system/templates/js/vue.js', 'weight' => 2000]);

    $w->enqueueScript(["name" => "modernizr.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/modernizr.js", "weight" => 1010]);
    $w->enqueueScript(["name" => "jquery.js", "uri" => "/system/templates/js/foundation-5.5.0/js/vendor/jquery.js", "weight" => 1000]);
    $w->enqueueScript(["name" => "jquery.tablesorter.js", "uri" => "/system/templates/js/tablesorter/jquery.tablesorter.js", "weight" => 990]);
    $w->enqueueScript(["name" => "jquery.tablesorter.pager.js", "uri" => "/system/templates/js/tablesorter/addons/pager/jquery.tablesorter.pager.js", "weight" => 980]);
    $w->enqueueScript(["name" => "jquery-ui-1.10.4.custom.min.js", "uri" => "/system/templates/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js", "weight" => 960]);

    $w->enqueueScript(["name" => "jquery-ui-timepicker-addon.js", "uri" => "/system/templates/js/jquery-ui-timepicker-addon.js", "weight" => 950]);
    $w->enqueueScript(["name" => "main.js", "uri" => "/system/templates/js/main.js", "weight" => 995]);
    $w->enqueueScript(["name" => "jquery.asmselect.js", "uri" => "/system/templates/js/jquery.asmselect.js", "weight" => 920]);
    $w->enqueueScript(["name" => "ckeditor.js", "uri" => "/system/templates/js/ckeditor/ckeditor.js", "weight" => 900]);
    $w->enqueueScript(["name" => "Chart.js", "uri" => "/system/templates/js/chart-js/dist/Chart.min.js", "weight" => 890]);
    $w->enqueueScript(["name" => "moment.js", "uri" => "/system/templates/js/moment.min.js", "weight" => 880]);

    // Code mirror
    $w->enqueueScript(["name" => "codemirror.js", "uri" => "/system/templates/js/codemirror-4.4/codemirror-compressed.js", "weight" => 880]);

    CmfiveScriptComponentRegister::registerComponent('AxiosJS', new CmfiveScriptComponent('/system/templates/js/axios.min.js'));
    CmfiveScriptComponentRegister::registerComponent('ToastJS', new CmfiveScriptComponent("/system/templates/js/Toast.js"));
    CmfiveStyleComponentRegister::registerComponent('ToastSCSS', new CmfiveStyleComponent("/system/templates/css/Toast.scss", ['/system/templates/scss/']));
    $w->loadVueComponents();

    $w->outputStyles();
    $w->outputScripts();
    ?>
    <script type="text/javascript">
        var $ = $ || jQuery;
        $(document).ready(function() {
            $("table.tablesorter").tablesorter({
                dateFormat: "uk",
                widthFixed: true,
                widgets: ['zebra']
            });
            $(".tab-head").children("a").each(function() {
                if (this.href.indexOf("#") != -1) {
                    $(this).bind("click", {
                        alink: this
                    }, function(event) {
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
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.which === 70) {
                    $('#cmfive_search_button').click();
                    return false;
                }
            });
            if (jQuery('.enable_drop_attachments').length !== 0) {
                globalFileUpload.init();
            }

            // Look for cmfive__count-* classes and count the instances of *
            $("span[class^='cmfive__count-'], span[class*=' cmfive__count-']").each(function(index, element) {
                var classList = this.className.split(/\s+/);
                for (var i in classList) {
                    if (classList[i].indexOf('cmfive__count-') > -1) {
                        $(this).text($('.' + classList[i].substring(classList[i].indexOf('-') + 1)).length);
                    }
                }
            });
        });


        // function modalClickListener() {
        //     if (this.hasAttribute('data-modal-confirm')) {
        //         if (confirm(this.getAttribute('data-modal-confirm'))) {
        //             openModal(this.getAttribute('data-modal-target'));
        //         }
        //     } else {
        //         openModal(this.getAttribute('data-modal-target'))
        //     }
        // }

        // function openModal(url) {
        //     $('#cmfive_modal').foundation('reveal', 'open', url);
        //     bindNewModalLinks();
        // }

        // Try and prevent multiple form submissions
        $("input[type=submit]").click(function() {
            $(this).hide();
        });

        $(document).bind('cbox_complete', function() {
            $("input[type=submit]").click(function() {
                $(this).hide();
            });
        });

        // function bindNewModalLinks() {
        //     document.querySelectorAll('[data-modal-target]')?.forEach((m) => {
        //         m.removeEventListener('click', modalClickListener);
        //         m.addEventListener('click', modalClickListener);
        //     });
        // }
        // Focus first form element when the modal opens
        $(document).ready(function() {
            // bindNewModalLinks();

            $('.body form:first :input:visible:enabled:first').not('.no-focus').focus();

            $(document).on('opened.fndtn.reveal', '[data-reveal]', function() {
                $('form:visible:first :input:visible:enabled:first', $(this)).not('.no-focus').focus();
            });
        });
    </script>
</head>

<body>
    <?php /** @var Web */ ?>
    <div class="loading_overlay" <?php echo Request::bool('show_overlay') == null ? 'style="display:none;"' : ''; ?>>
        <div class="circle"></div>
        <img class="center_image" width="100px" height="100px" src="/system/templates/img/cmfive_V_logo.png" />
        <h4 class="subheader">Please wait</h4>
    </div>
    <div class="global_file_drop_area" id="global_file_drop_area">
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
                    <h4 style="font-weight: lighter; text-align: center; color: white; padding: 5px 0px 0px 0px;"><?php echo Config::get('system.test_mode_message') ?></h4>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row-fluid">
        <nav class="top-bar" data-topbar>
            <!-- To make it that you need to click to activate dropdown use  data-options="is_hover: false" -->
            <ul class="title-area">
                <li class="name">
                </li>
                <li class="toggle-topbar"><a href="">Menu</a></li>
            </ul>

            <section class="top-bar-section">
                <!-- Right Nav Section -->
                <ul class="right">
                    <!-- Module template injection -->
                    <?php
                    $inject = $w->callHook('core_template', 'menu');
                    if (!empty($inject)) :
                        foreach ($inject as $i) : ?>
                            <li><?php echo $i; ?></li>
                    <?php
                        endforeach;
                    endif;
                    ?>

                    <!-- Search bar -->
                    <?php if (Config::get('system.search_enabled', true) && AuthService::getInstance($w)->user()->allowed('/search')) : ?>
                        <li><?php echo Html::box("/search", "<span class='fi-magnifying-glass show-for-medium-up'></span><span class='show-for-small'>Search</span>", false, false, null, null, null, "cmfive_search_button"); ?></li>
                    <?php endif; ?>

                    <?php if (AuthService::getInstance($w)->user()) : ?>
                        <!-- Clear cache button -->
                        <?php if (AuthService::getInstance($w)->user()->is_admin) : ?>
                            <li>
                                <a id="admin_clear_cache" href="/admin/ajaxClearCache" onclick="return false;" title="Clear configuration cache">
                                    <span class="clear_cache_icon fi-refresh show-for-medium-up"></span>
                                    <span class="show-for-small">Clear cache</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <!-- User Profile drop down -->
                        <li class="has-dropdown">
                            <a href="#">
                                <span class="fi-torso show-for-medium-up"></span>
                                <span class="show-for-small">Account</span>
                            </a>
                            <?php
                            echo Html::ul(
                                [
                                    $w->menuLink("auth/profile", AuthService::getInstance($w)->user()->getShortName()),
                                    $w->menuLink("auth/logout", "Logout")
                                ],
                                null,
                                "dropdown"
                            );
                            ?>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Left Nav Section -->
                <ul class="left">
                    <?php if (AuthService::getInstance($w)->loggedIn()) : ?>
                        <li><?php echo $w->menuLink(MainService::getInstance($w)->getUserRedirectURL(), "<span class='fi-home show-for-medium-up'></span><span class='show-for-small'>Home</span>"); ?></li>
                        <li class="divider"></li>
                        <?php foreach ($w->modules() as $module) {
                            // Check if config is set to display on topmenu
                            if (Config::get("{$module}.topmenu") && Config::get("{$module}.active")) :
                                // Check for navigation
                                $service_module = ucfirst($module) . "Service";
                                $menu_link = method_exists($service_module, "menuLink") ? $service_module::getInstance($w)->menuLink() : $w->menuLink($module, is_bool(Config::get("{$module}.topmenu")) ? ucfirst($module) : Config::get("{$module}.topmenu"));
                                if ($menu_link !== false) :
                                    if (method_exists($module . "Service", "navigation")) : ?>
                                        <li class="has-dropdown <?php echo $w->_module == $module ? 'active' : ''; ?>" id="topnav_<?php echo $module; ?>">
                                            <?php // Try and get a badge count for the menu item
                                            echo $menu_link;
                                            $module_navigation = $service_module::getInstance($w)->navigation($w);

                                            // Invoke hook to inject extra navigation
                                            $hook_navigation_items = $w->callHook($module, "extra_navigation_items", $module_navigation);
                                            if (!empty($hook_navigation_items)) {
                                                foreach ($hook_navigation_items as $hook_navigation_item) {
                                                    if (is_array($hook_navigation_item)) {
                                                        $module_navigation = array_merge($module_navigation, $hook_navigation_item);
                                                    } else {
                                                        $module_navigation[] = $hook_navigation_item;
                                                    }
                                                }
                                            }
                                            echo Html::ul($module_navigation, null, "dropdown"); ?>
                                        </li>
                                    <?php else : ?>
                                        <li <?php echo $w->_module == $module ? 'class="active"' : ''; ?>><?php echo $menu_link; ?></li>
                                    <?php endif; ?>
                                    <li class="divider"></li>
                            <?php endif;
                            endif;
                        }

                        if (Config::get('system.help_enabled', true) && AuthService::getInstance($w)->allowed('/help/view')) : ?>
                            <li><?php echo Html::box(WEBROOT . "/help/view/" . $w->_module . ($w->_submodule ? "-" . $w->_submodule : "") . "/" . $w->_action, "<span class='fi-q show-for-medium-up'>?</span><span class='show-for-small'>Help</span>", false, true, 750, 500, "isbox", null, null, null, 'cmfive-help-modal'); ?> </li>
                    <?php endif;
                    endif; ?>
                </ul> <!-- End left nav section -->
            </section>
        </nav>
    </div>

    <!-- Breadcrumbs -->
    <div class="row-fluid">
        <?php echo Html::breadcrumbs([], $w); ?>
    </div>

    <div class="row-fluid body">
        <?php // Body section w/ message and body from template
        ?>
        <div class="row-fluid">
            <?php if (empty($hideTitle) && !empty($title)) : ?>
                <div class="row-fluid small-12">
                    <h3 class="header"><?php echo $title; ?></h3>
                </div>
            <?php endif; ?>
            <?php
            if (!empty($error)) {
                echo Html::alertBox($error, "warning");
            }
            if (!empty($msg)) {
                echo Html::alertBox($msg);
            }
            ?>
            <div class="row-fluid" style="overflow: hidden;">
                <?php echo !empty($body) ? $body : ''; ?>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div id="footer">
            Copyright &#169; <?php echo date('Y'); ?>&nbsp;&nbsp;&nbsp;<a href="<?php echo $w->moduleConf('main', 'company_url'); ?>"><?php echo $w->moduleConf('main', 'company_name'); ?></a>
        </div>
    </div>

    <div id="cmfive-modal" class="reveal-modal xlarge" data-reveal></div>
    <div id="cmfive-help-modal" class="reveal-modal xlarge" data-reveal></div>
    <script type="text/javascript" src="/system/templates/js/foundation-5.5.0/js/foundation.min.js"></script>
    <script type="text/javascript" src="/system/templates/js/foundation-5.5.0/js/foundation/foundation.clearing.js"></script>
    <script>
        $(document).foundation({
            reveal: {
                animation_speed: <?php echo defaultVal(Config::get('core_template.foundation.reveal.animation_speed'), 150); ?>,
                animation: '<?php echo defaultVal(Config::get('core_template.foundation.reveal.animation'), 'fade'); ?>',
                close_on_background_click: <?php echo defaultVal(Config::get('core_template.foundation.reveal.close_on_background_click'), 'true'); // Must be string value in PHP 
                                            ?>
            },
            accordion: {
                multi_expand: <?php echo defaultVal(Config::get('core_template.foundation.accordion.multi_expand'), 'true'); ?>,
            }
        });

        var modal_history = [];
        var modal_history_pop = false;

        function boundModalListener() {
            if (this.hasAttribute('data-modal-confirm')) {
                if (confirm(this.getAttribute('data-modal-confirm'))) {
                    openModal(this.getAttribute('data-modal-target'));
                }
            } else {
                openModal(this.getAttribute('data-modal-target'))
            }
        }

        $(document).ready(function() {
            document.querySelectorAll('[data-modal-target]').forEach(function(m) {
                m.removeEventListener('click', boundModalListener);
                m.addEventListener('click', boundModalListener);
            })
        })

        // $(document).ready(function() {
        //     document.querySelectorAll('[data-modal-target]').forEach(function(m) {
        //         m.addEventListener('click', function() {
        //             if (m.hasAttribute('data-modal-confirm')) {
        //                 if (confirm(m.getAttribute('data-modal-confirm'))) {
        //                     openModal(m.getAttribute('data-modal-target'));
        //                 }
        //             } else {
        //                 openModal(m.getAttribute('data-modal-target'))
        //             }
        //         });
        //     })
        // })

        // Automatically append the close 'x' to reveal modals
        $(document).on('opened', '[data-reveal]', function() {
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

        function openModal(url) {
            changeModalWindow($('#cmfive-modal'), url);
            $('#cmfive-modal').foundation('reveal', 'open');
        }
    </script>
</body>

</html>