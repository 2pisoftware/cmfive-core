<?php

function logCSPReport_POST(Web $w)
{
    $w->setLogger('CSP')->error('aaaaah' . $_POST['csp-report']);
}
