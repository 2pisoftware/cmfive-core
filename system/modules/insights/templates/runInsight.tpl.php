<div>
    <?php
        echo HtmlBootstrap5::b(href: '/insights/viewInsight/' . $insight_class_name . '?' . $request_string, title: 'Change Insight Parameters', class: 'btn-sm btn-primary');
        echo HtmlBootstrap5::b(href: '/insights-export/csv/' . $insight_class_name . '?' . $request_string, title: 'Export to CSV', class: 'btn-sm btn-secondary');
        echo HtmlBootstrap5::box(href: '/insights-export/bindpdf/' . $insight_class_name . '?' . $request_string, title: 'Export to PDF', button: true, class: 'btn-sm btn-secondary');
    ?>
</div>
<style>
    .tablesorter tbody tr td {
        padding: 5px 20px !important;
    }
</style>
<?php

foreach ($run_data as $data) : ?>
    <h4 class="mt-4"><?php echo $data->title; ?></h4>
    <div style='overflow: auto;'>
        <?php echo Html::table($data->data, null, "tablesorter", $data->header); ?>
    </div>
<?php endforeach;
