<?php echo HtmlBootstrap5::b("/admin-templates", "Back to Templates List", null, null, false, "btn btn-sm btn-primary"); ?>
<br /><br />
<div class='tabs'>
    <div class='tab-head'>
        <a class='active' href="#tab-1">Details</a>
        <a href="#tab-2">Template</a>
        <a href="#tab-3">Test Data</a>
        <?php echo HtmlBootstrap5::a("/admin-templates/rendertemplate/" . (!empty($id) ? $id : ""), "Test Output", null, null, null, '_blank'); ?>
        <a href="#tab-5">Manual</a>
    </div>
    <div class='tab-body'>
        <div id='tab-1'>
            <?php echo !empty($editdetailsform) ? $editdetailsform : ''; ?>
        </div>
        <div id='tab-2'>
            <?php echo !empty($templateform) ? $templateform : ''; ?>
        </div>
        <div id='tab-3'>
            <?php echo !empty($testdataform) ? $testdataform : ''; ?>
        </div>
        <div id='tab-5'>
            <p>
                This is the template manual
            </p>
        </div>
    </div>
</div>