<?php
echo Html::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string, "Change Insight Parameters");
echo Html::b("/insights-export/csv/" . $insight_class_name . "?" . $request_string, "Export to CSV", null);
echo Html::box("/insights-export/bindpdf/" . $insight_class_name . "?" . $request_string, "Export to PDF", true);

try {
    foreach ($run_data as $data) {
        echo '<h4>' . $data->title . '</h4>';
        echo "<div style='overflow: auto;'>";
        echo Html::table($data->data, null, "tablesorter", $data->header);
        echo "</div>";
    }
} catch (Error $e) {
    echo "Error caught: " . $e->getMessage();
    LogService::getInstance($w)->setLogger("INSIGHTS")->error("Error occurred. Cannot run insight $p" . $e->getMessage());
}
