<?php

function index_ALL(Web $w)
{
    $connections = ReportService::getInstance($w)->getConnections();

    $table_header = ["Driver", "Host", "Database", "Port", "Username", "Actions"];
    $table_body = [];
    if (!empty($connections)) {
        foreach ($connections as $conn) {
            $conn->decrypt();
            $table_body[] = [
                $conn->db_driver,
                $conn->db_host,
                $conn->db_database,
                $conn->db_port,
                $conn->s_db_user,
                HtmlBootstrap5::buttonGroup(
                    Html::box("/report-connections/test/{$conn->id}", "Test Connection", true) .
                        Html::box("/report-connections/edit/{$conn->id}", "Edit", true) .
                        Html::b("/report-connections/delete/{$conn->id}", "Delete", "Are you sure you want to remove this connection?", null, false, "btn-danger")
                )
            ];
        }
    }

    $w->ctx("connections_table", HtmlBootstrap5::table($table_body, null, "tablesorter", $table_header));
}
