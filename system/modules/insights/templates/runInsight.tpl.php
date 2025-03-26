<?php
echo HtmlBootstrap5::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string,"Change Insight Parameters", null, null, false, "btn-sm btn-primary");
echo HtmlBootstrap5::b("/insights-export/csv/" . $insight_class_name . "?" . $request_string, "Export to CSV", null, null, false, "btn-sm btn-primary");
echo HtmlBootstrap5::box("/insights-export/bindpdf/" . $insight_class_name . "?" . $request_string, "Export to PDF", true, false, null, null, "isbox", null, "btn-sm btn-primary");

try {
    foreach ($run_data as $data) {
        echo '<h4>' . $data->title . '</h4>';
        echo "<div style='overflow: auto;'>";
        echo HtmlBootstrap5::table($data->data, null, "tablesorter", $data->header);
        echo "</div>";
    }
} catch (Error $e) {
    echo "Error caught: " . $e->getMessage();
    LogService::getInstance($w)->setLogger("INSIGHTS")->error("Error occurred. Cannot run insight $p" . $e->getMessage());
}
