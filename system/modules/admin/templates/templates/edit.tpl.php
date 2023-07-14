<?php echo HtmlBootstrap5::b("/admin-templates", "Back to Templates List", null, null, false, "btn btn-sm btn-primary"); ?>
<br /><br />
<div class='tabs'>
        <div class='tab-head'>
                <a class='active' href="#tab-1">Details</a>
                <a href="#tab-2">Template</a>
                <a href="#tab-3">Test Data</a>
                <a href="#tab-4">Manual</a>
        </div>
        <div class='tab-body'>
                <div id='tab-1'>
                        <?php echo !empty($editdetailsform) ? $editdetailsform : ''; ?>
                </div>
                <div id='tab-2'>
                        <?php echo !empty($templateform) ? $templateform : ''; ?>
                </div>
                <div id='tab-3'>
                        <?php echo Html::a("/admin-templates/rendertemplate/" . (!empty($id) ? $id : ""), "Test Output", null, null, null, '_blank'); ?>
                        <?php echo !empty($testdataform) ? $testdataform : ''; ?>
                </div>
                <div id='tab-4'>
                        <p>
                                This is the template manual
                </div>
        </div>
</div>
<!--<div class="tabs">

        <div class="tab-head">
                <a href="#details">Details</a>
                <a href="#template">Template</a>
                <a href="#test">Test Data</a>
                <?php echo Html::a("/admin-templates/rendertemplate/" . (!empty($id) ? $id : ""), "Test Output", null, null, null, '_blank'); ?>
                <a href="#manual">Manual</a>
        </div>
        <div class="tab-body clearfix">
                <div id="details">
                        <p>
                                <?php echo !empty($editdetailsform) ? $editdetailsform : ''; ?>
                </div>
                <div template id="template" style="display: none;">
                        <p>
                                <?php echo !empty($templateform) ? $templateform : ''; ?>
                </div>
                <div id="test" style="display: none;">
                        <p>
                                <?php echo !empty($testdataform) ? $testdataform : ''; ?>
                </div>
                <div id="manual" style="display: none;">
                        <p>
                                this is the template manual
                </div>
        </div>
</div>
                -->