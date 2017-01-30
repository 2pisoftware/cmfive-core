<?php

function index_ALL(Web $w) {
   
    $connections = $w->Report->getConnections();
    
    $table_header = array(__("Driver"), __("Host"), __("Database"), __("Port"), __("Username"), __("Actions"));
    $table_body = array();
    if (!empty($connections)) {
        foreach($connections as $conn) {
            $conn->decrypt();
            $table_body[] = array(
                $conn->db_driver, $conn->db_host, $conn->db_database, $conn->db_port, $conn->s_db_user,
                Html::box("/report-connections/test/{$conn->id}", __("Test Connection"), true) .
                Html::box("/report-connections/edit/{$conn->id}", __("Edit"), true) .
                Html::b("/report-connections/delete/{$conn->id}", __("Delete"), __("Are you sure you want to remove this connection?"))
            );
        }
    }
    
    $w->ctx("connections_table", Html::table($table_body, null, "tablesorter", $table_header));
}
