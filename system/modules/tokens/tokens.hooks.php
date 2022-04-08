<?php

function tokens_auth_get_auth_token_validation(Web $w, $jwt)
{
    return TokensService::getInstance($w)->getDayDateUserTokenCheck($jwt);
}


function tokens_tokens_get_roles_from_policy(Web $w, $policy)
{
    if ($policy->_validator == "CMFIVE" && $policy->_app_id == "CMFIVE_API") {
        return TokensService::getInstance($w)->getCoreRolesByDayDateUserPolicy($policy);
    }
}
