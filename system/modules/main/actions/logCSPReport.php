<?php

function logCSPReport_POST(Web $w)
{
    $w->Log->setLogger('CSP')->error(
        'CSP Violation: ' .
        json_decode(file_get_contents('php://input'), true)['csp-report']['blocked-uri']
    );
}
