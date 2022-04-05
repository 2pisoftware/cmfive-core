<?php

function tokens_auth_get_auth_token_validation(Web $w, $jwt)
{
    return TokensService::getInstance($w)->getCoreTokenCheck($jwt);
}


function tokens_tokens_get_roles_from_policy(Web $w, $policy)
{
    if ($policy->_validator == 'CMFIVE') {
        return TokensService::getInstance($w)->getCoreRolesByDayDateUserPolicy($policy);
    }
}
