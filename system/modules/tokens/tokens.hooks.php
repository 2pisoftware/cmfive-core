<?php


    /*
    A request with auth token bearer and no user session state, triggers token handling from auth module:
     - A hook fires to have the token validated 
        - your APP MODULE should claim *** [module]_auth_get_auth_token_validation ***
        - auth module accepts the token if a hook handler yields a TokensPolicy
        - The policy is internally 'stateless' & never persisted in cmfive DB
        - then auth module requests for TokensPolicy to allow the current request action
     - A hook fires from TokensPolicy seeking an application model to implement the policy roles
        - your APP MODULE should claim *** [module]__tokens_get_roles_from_policy  ***
        - the listening APP will materialise these in standard manner of user->roles->allowed
        - TokensPolicy GetBearersRoles assists with collation
        - APP can interpret profile identifier freely (eg: as user_id, group_policy, code-baked app_actions etc)
    */
    
/*

// Your AUTH MODEL supporting API APP as token provider should catch this hook:

function [MySmartAuthModule]_auth_get_auth_token_validation(Web $w, $jwt)
{
    return [MySmartAuthModule]::getInstance($w)->getResultOfAppliedTokenCheck($jwt);
}


// Your API APP as role provider should catch this hook:

function [MyUsefulAPIModule]_tokens_get_roles_from_policy(Web $w, $policy)
{
    if ($policy->_validator == "MyAuthWasResponsible" && $policy->_app_id == "MyAppShouldHandleThis") {
        return [MyUsefulAPIModule]::getInstance($w)->getAppropriateRolesByTokenResolvedToUserPolicy($policy);
    }
}

*/
