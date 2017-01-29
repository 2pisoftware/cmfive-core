<?php
    
function general_VAR(Web &$w) {
    return array(
        ValidationService::add('application_name'),
        ValidationService::add('company_name'),
        ValidationService::add('company_url')->type('url'),
        ValidationService::add('company_support_email')->type('email')->required(),
        ValidationService::add('rest_api_key')->defaultValue($w->Install->getRandomString(32))->ignore()
    );
}

/** only handle variable validation and transfer from POST to SESSION **/
function general_POST(Web &$w) {
    //$step = $w->Install->getInstallStep('general');
    
    // error checking and saving to session
    $w->Install->saveInstall('general');
 
    // success message
    return "Configured Company Information";
}
 