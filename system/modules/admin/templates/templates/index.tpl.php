<div class="row">
    <div class="col">
        <?php echo HtmlBootstrap5::b("/admin-templates/edit", "Add Template", null, null, false, "btn btn-sm btn-primary"); ?>
    </div>
</div>
<?php echo !empty($templates_table) ? $templates_table : ""; ?>
