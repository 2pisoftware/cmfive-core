<?php
    
function timezone_VAR() {
    return array(
        ValidationService::add('timezone'),
        ValidationService::add('gmt')->type('float'),
        ValidationService::add('country')
    );
}

function timezone_POST(Web $w) {
    //$step = getInstallStep('timezone');
    
    // error checking and saving to session
    $w->Install->saveInstall('timezone');
    
    // display errors, warnings, tests, success?
    return "Configured Local Timezone";
}
