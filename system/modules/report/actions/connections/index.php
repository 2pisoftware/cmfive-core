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
                StringSanitiser::sanitise($conn->db_driver),
                StringSanitiser::sanitise($conn->db_host),
                StringSanitiser::sanitise($conn->db_database),
                StringSanitiser::sanitise($conn->db_port),
                StringSanitiser::sanitise($conn->s_db_user),
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::box(href: "/report-connections/test/{$conn->id}", title: "Test Connection", button: true, class: 'btn btn-sm btn-info') .
                        HtmlBootstrap5::box(href: "/report-connections/edit/{$conn->id}", title: "Edit", button: true, class: 'btn btn-sm btn-secondary') .
                        HtmlBootstrap5::b(href: "/report-connections/delete/{$conn->id}", title: "Delete", confirm: "Are you sure you want to remove this connection?", class: 'btn btn-sm btn-danger')
                )
            ];
        }
    }

    $w->ctx("connections_table", HtmlBootstrap5::table($table_body, null, "tablesorter", $table_header));
}
