<?php

function test_ALL(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p["id"])) {
        $w->error(__("No connection ID specified"), "/report-connections");
    }
    
    $connection = $w->Report->getConnection($p["id"]);
    if (empty($connection->id)) {
        $w->error(__("Connection could not be found"), "/report-connections");
    }
    
    // Decrypt is called in getDb(), which reencrypts it
//    $connection->decrypt();
//    var_dumP($connection);
    try {
        $dbo = $connection->getDb();
        echo __("Connected to DB")."<br/>".__("Fetching databases to test connection")."...<br/>";
        
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
            echo __("No results found");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    
}
