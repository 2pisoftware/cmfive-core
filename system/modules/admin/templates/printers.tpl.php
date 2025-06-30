<div class="row">
    <div class="col">
        <?php echo HtmlBootstrap5::box(href: "/admin/editprinter", title: "Add a printer", button: true, class: 'btn btn-primary'); ?>
    </div>
</div>
<?php echo HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header); 
