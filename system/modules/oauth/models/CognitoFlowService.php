<?php

/**@author Derek Crannaford */

class CognitoFlowService extends DbService
{

    /*
    Endgame:
    The JSON returned in the resulting response has the following keys:    
        access_token – A valid user pool access token.
        refresh_token – A valid user pool refresh token. This can be used to retrieve new tokens by sending it through a POST request to https://AUTH_DOMAIN/oauth2/token, specifying the refresh_token and client_id parameters, and setting the grant_type parameter to “refresh_token“.
        expires_in – The length of time (in seconds) that the provided ID and/or access token(s) are valid for.
        token_type – Set to “Bearer“.
        */

    /*
    An application makes an HTTP GET request to https://AUTH_DOMAIN/oauth2/authorize, where AUTH_DOMAIN represents the user pool’s configured domain. This request includes the following query parameters:
        response_type – Set to “code” for this grant type.
        client_id – The ID for the desired user pool app client.
        redirect_uri – The URL that a user is directed to after successful authentication.
        state (optional but recommended) – A random value that’s used to prevent cross-site request forgery (CSRF) attacks.
        scope (optional) – A space-separated list of scopes to request for the generated tokens. Note that:
        An ID token is only generated if the openid scope is requested.
        The phone, email, and profile scopes can only be requested if openid is also requested.
        A vended access token can only be used to make user pool API calls if aws.cognito.signin.user.admin is requested.
        identity_provider (optional) – Indicates the provider that the end user should authenticate with.
        idp_identifier (optional) – Same as identity_provider, but doesn’t expose the provider’s real name.
        code_challenge (optional, is required if code_challenge_method is specified) – The hashed, base64 URL-encoded representation of a random code that’s generated client side. It serves as a Proof Key for Code Exchange (PKCE), which prevents attackers from being able to use intercepted authorization codes.
        code_challenge_method (optional, is required if code_challenge is specified) – The hash algorithm that’s used to generate the code_challenge. Amazon Cognito currently only supports setting this parameter to “S256“. This indicates that the code_challenge parameter was generated using SHA-256.
        */
    public function attemptCognitoFlow($requested)
    {
        $app = OauthFlowService::getInstance($this->w)->getOauthAppByProvider("cognito", $requested['app_id'] ?? null);
        if (empty($app)) {
            return false;
        }

        // https://AUTH_DOMAIN/oauth2/authorize,
        // --> https://AUTH_DOMAIN/login 

        $flow = new OauthFlow($this->w);
        $flow->app_id = $requested['app_id'];
        $flow->update();
        $this->w->redirect(
            "https://"
                . ($app['domain'] ?? "")
                . "/oauth2/authorize?client_id="
                . $requested['app_id']
                . "&response_type=code"
                . "&redirect_uri=https://apatchofnettles.github.io/"
                . "&state=" . $flow->state
                . "&code_challenge=" . $flow->pkce_challenge
                . "&code_challenge_method=" . $flow->pkce_method
                . ((empty($app['scope'])) ? "" : "&scope=" . $app['scope'])
        );

        return false;
    }

    /*
    POST request to https://AUTH_DOMAIN/oauth2/token with the following application/x-www-form-urlencoded parameters:
        grant_type – Set to “authorization_code” for this grant.
        code – The authorization code that’s vended to the user.
        client_id – Same as from the request in step 1.
        redirect_uri – Same as from the request in step 1.
        code_verifier (optional, is required if a code_challenge was specified in the original request) – The base64 URL-encoded representation of the unhashed, random string that was used to generate the PKCE code_challenge in the original request.
        
        If the client app that was used requires a secret, the Authorization header for this request is set as:
            “Basic BASE64(CLIENT_ID:CLIENT_SECRET)“, 
            where BASE64(CLIENT_ID:CLIENT_SECRET) is the base64 representation of the app client ID and app client secret, concatenated with a colon.
        */
    public function attemptCognitoAccess($requested)
    {
        if (empty($requested['code']) || empty($requested['state'])) {
            return null;
        }

        $known = OauthFlowService::getInstance($this->w)->getObject("OauthFlow", ['state' => $requested['state']]);
        if (empty($known)) {
            return null;
        }
        
        $app = OauthFlowService::getInstance($this->w)->getOauthAppByProvider("cognito", $known->app_id);
        if (empty($app)) {
            return null;
        }

        $cognito = new OauthCognitoClient($this->w);
        $appAuth = (empty($app['client_secret']))
            ? null
            : TokensService::getInstance($this->w)->getBase64URL($known->app_id . ":" . $app['client_secret']);

        $cognito->getTokenIssuer(
            "https://"
                . ($app['domain'] ?? "")
                . "/oauth2/token",
            $appAuth
        );
        $issued = $cognito->getIssuedToken([
            'client_id' => $known->app_id,
            'client_secret' => ((empty($app['client_secret'])) ? "" :  $app['client_secret']),
            'grant_type' => "authorization_code",
            'redirect_uri' => "https://apatchofnettles.github.io/",
            'code' => $requested['code'],
            'code_verifier' => $known->pkce_verifier,

        ]);

        return json_decode($issued, true);
    }
}
