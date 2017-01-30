<?php

function edit_GET(Web $w) {
    $p = $w->pathMatch("id");
    $report_connection = new ReportConnection($w);
    if (!empty($p["id"])) {
        $report_connection = $w->Report->getConnection($p["id"]);
    }
    
    if (!empty($report_connection->id)) {
        $report_connection->decrypt();
    }
    
    $form = array(
        "Connection" => array(
            array(
                array(__("Driver"), "select", "db_driver", $report_connection->db_driver, PDO::getAvailableDrivers()),
                array(__("Host"), "text", "db_host", $report_connection->db_host)
            ),
            array(
                array(__("Port"), "text", "db_port", $report_connection->db_port),
                array(__("Database"), "text", "db_database", $report_connection->db_database)
            ),
            array(
                array(__("Username"), "text", "s_db_user", $report_connection->s_db_user),
                array(__("Password"), "password", "s_db_password", $report_connection->s_db_password)
            )
        )
    );
    
    $w->out(Html::multiColForm($form, "/report-connections/edit/{$report_connection->id}"));
}

function edit_POST(Web $w) {
    $p = $w->pathMatch("id");
    $report_connection = !empty($p["id"]) ? $w->Report->getConnection($p["id"]) : new ReportConnection($w);
    $report_connection->fill($_POST);
    if (empty($_POST["s_db_password"])) {
        $report_connection->s_db_password = NULL;
    }
    $report_connection->insertOrUpdate();
    $w->msg(__("Connection ") . (!empty($p["id"]) ? __("updated") : __("created")), "/report-connections");
}
