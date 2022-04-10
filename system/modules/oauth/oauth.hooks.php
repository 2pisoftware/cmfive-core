<?php

function oauth_oauth_request_app_id_flow(Web $w, $requested)
{
    CognitoFlowService::getInstance($w)->attemptCognitoFlow($requested);
}


function oauth_oauth_request_code_submit_flow(Web $w, $requested)
{
    return CognitoFlowService::getInstance($w)->attemptCognitoAccess($requested);
}


// function oauth_auth_get_auth_token_validation(Web $w, $jwt)
// {
//     return TokensService::getInstance($w)->getCoreTokenCheck($jwt);
// }


// function oauth_tokens_get_roles_from_policy(Web $w, $policy)
// {
//     if ($policy->_validator == 'CMFIVE') {
//         return TokensService::getInstance($w)->getCoreRolesByDayDateUserPolicy($policy);
//     }
// }
