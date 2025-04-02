<?php
echo HtmlBootstrap5::box("/main/addwidget/{$module}", "Add Widget", true);

if (!empty($widgets)):
?>
    <div class="widget_container">
        <ul class="small-block-grid-1 medium-block-grid-3">
            <?php for ($i = 0; $i < count($widgets); $i++): ?>
                <li class="widget">
                    <div class="widget_buttons">
                        <?php echo HtmlBootstrap5::box("/main/configwidget/{$module}/{$widgets[$i]->id}", __("Config"), false, false, null, null, "isbox", null, "widget_config"); ?>
                        <?php echo HtmlBootstrap5::a("/main/removewidget/{$module}/{$widgets[$i]->id}", __("Remove"), __("Remove Widget"), "widget_remove"); ?>
                    </div>
                    <?php // echo $w->partial($widgets[$i]->widget_name, null, $widgets[$i]->source_module); ?>
                    <?php if (!empty($widgets[$i]->widget_class)) {
	$widgets[$i]->widget_class->display();
}
?>
                </li>
            <?php endfor;?>
        </ul>
    </div>
    <script type="text/javascript">
        $('.widget').hover(
                function() {
                    $(this).find(".widget_buttons:first").stop(true, true).css({opacity: 0}).animate({opacity: 1}, 250);
                },
                function() {
                    $(this).find(".widget_buttons:first").stop(true, true).css({opacity: 1}).animate({opacity: 0}, 250);
                }
        );

        $("#")
    </script>
<?php endif;?>
