<?php

function test_ALL(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p["id"])) {
        $w->error("No connection ID specified", "/report-connections");
    }
    
    $connection = ReportService::getInstance($w)->getConnection($p["id"]);
    if (empty($connection->id)) {
        $w->error("Connection could not be found", "/report-connections");
    }
    
    // Decrypt is called in getDb(), which reencrypts it
//    $connection->decrypt();
//    var_dumP($connection);
    try {
        $dbo = $connection->getDb();
        echo "Connected to DB<br/>Fetching databases to test connection...<br/>";
        
        $results = null;
        switch ($connection->db_driver) {
            case "pgsql":
                $results = $dbo->query("SELECT datname FROM pg_database")->fetchAll();
                break;
            case "mysql":
                $results = $dbo->query("show databases")->fetchAll();
                break;
        }
        
        if (!empty($results)) {
            foreach(array_values($results) as $r) {
                echo "\t{$r[0]}<br/>";
            }
        } else {
            echo "No results found";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    
}