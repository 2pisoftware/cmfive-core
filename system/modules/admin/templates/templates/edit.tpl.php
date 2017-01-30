<?php echo Html::b("/admin-templates",__("Back to Templates List"),false); ?>
<br/><br/>
<div class="tabs">

	<div class="tab-head">
		<a href="#details"><?php _e('Details'); ?></a>
		<a href="#template"><?php _e('Template'); ?></a>
		<a href="#test"><?php _e('Test Data'); ?></a>
        <?php echo Html::box("/admin-templates/rendertemplate/". (!empty($id) ? $id : ""), __("Test Output"), false); ?>
		<a href="#manual"><?php _e('Manual'); ?></a>
	</div>
	<div class="tab-body clearfix">
            <div id="details"><p>
                    <?php echo !empty($editdetailsform) ? $editdetailsform : '';?>
            </div>
            <div id="template" style="display: none;"><p>
                    <?php echo !empty($templateform) ? $templateform : '';?>
            </div>
            <div id="test" style="display: none;"><p>
                    <?php echo !empty($testdataform) ? $testdataform : '';?>
            </div>
            <div id="manual" style="display: none;"><p>
                            <?php _e('this is the template manual'); ?>
            </div>
	</div>
</div>

