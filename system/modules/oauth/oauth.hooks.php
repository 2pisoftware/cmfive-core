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

// function tokens_tokens_request_app_id_flow(Web $w, $requested)
// {
//     /*
//     Some basic stuff about the code_verifier, code_challenge and the code_challenge_method
// code_verifier — The code verifier should be a high-entropy cryptographic random string with a minimum of 43 characters and a maximum of 128 characters. Should only use A-Z, a-z, 0–9, “-”(hyphen), “.” (period), “_”(underscore), “~”(tilde) characters.
// code_challenge — The code challenge is created by SHA256 hashing the code_verifier and base64 URL encoding the resulting hash Base64UrlEncode(SHA256Hash(code_verifier)). In the case that there is no such possibility ever to do the above transformation it is okay to use the code_verifier itself as the code_challenge.
// code_challenge_method — This is an optional parameter. The available values are “S256” when using a transformed code_challenge as mentioned above. Or “plain” when the code_challenge is the same as the code_verifier. Since this is an optional parameter, if not sent in the request the Authorisation Server will assign “plain” as the default value.
// */

//     if ($requested['app_id'] == '1kssj2bp4ospjfna33if60s8k7') {
//         $w->redirect(
//             "https://2pi-devpoint-arbitration.auth.ap-southeast-2.amazoncognito.com/login?client_id="
//         . $requested['app_id']
//         . "&response_type=code&scope=aws.cognito.signin.user.admin"
//         . "&redirect_uri=https://apatchofnettles.github.io/" //https://sswp.cosinecrm.com.au"
//         // . "&redirect_uri=".$requested['callback']
//     );
//     }

//     return false;
// }