<?php

function logCSPReport_POST(Web $w)
{
    $report = json_decode(file_get_contents('php://input'), true)['csp-report'];
    LogService::getInstance($w)->setLogger('CSP')->error(
        "CSP Violation: {$report['blocked-uri']} {$report['script-sample']}"
    );
}
