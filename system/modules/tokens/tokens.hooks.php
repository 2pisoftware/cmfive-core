<?php

function tokens_auth_get_auth_token_validation(Web $w, $jwt)
{
    return TokensService::getInstance($w)->getCoreTokenCheck($jwt);
}


function tokens_tokens_get_roles_from_policy(Web $w, $policy)
{
    //FIXME we're never making it into here
    echo print_r("Tokens hook hit", true);
    LogService::getInstance($w)->Log->info("hook hit:");
    if ($policy->_validator == 'CMFIVE') {
        return TokensService::getInstance($w)->getCoreRolesByDayDateUserPolicy($policy);
    }
}
