<?php

/**@author Derek Crannaford */

class TokensService extends DbService
{

    public function getTokenFromAuthorisationHeader($bearer)
    {
        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            return null;
        }
        return $matches[1] ?? null;
    }

    public function getJsonFromPostRequest()
    {
        // Takes raw data from the request
        $json = file_get_contents('php://input');

        // Converts it into a PHP object
        return json_decode($json, true) ?? ['error' => "json parse failed"];
    }

    // This will only work for a vanilla HS256 token!
    // Key-pair hashed tokens (Cognito etc) will neeed their own check methods

    public function getJwtSignatureCheck($jwt, $asBase64 = false)
    {
        $parts = explode(".", $jwt);

        $header = json_decode(base64_decode($parts[0] ?? ""), true);
        $alg = $header['alg'] ?? "";

        if (empty($parts[2]) || !$alg == "HS256") {
            return false;
        }

        $signature = hash('sha256', $parts[0] . "." . ($parts[1] ?? ""));
        if ($asBase64) {
            $signature = $this->getBase64URL($signature);
        }

        return ($signature == ($parts[2] ?? null));
    }

    public function getJwtPayload($jwt)
    {
        $parts = explode(".", $jwt);
        return json_decode(base64_decode($parts[1] ?? ""), true);
    }

    public function getAppFromJwtPayload($jwt)
    {
        return $this->getJwtPayload($jwt)['client_id'] ?? null;
    }

    function getBase64URL($plainText)
    {
        $base64 = base64_encode($plainText);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');
        return ($base64url);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    // Use case example, and HOW-TO for handling token event hooks
    // NOT for deployment !
    //////////////////////////////////////////////////////////////////////////////////////////////

    /*
    A general model for token (api) support:

    A logged in user-with-role can get a sha'd token JWT from tokens/grant! 
     - This (example) token is secured by getDayDateUserToken hashing
     - The token is per user & only viable by exact match of single-day-date (ie: dies at midnight!)
     - This (example) token asserts "CMFIVE" (core) validation for access to "CMFIVE_API" app actions
     - "CMFIVE_API" app actions are deemed by policy of user->allowed roles (from role files) with suffix of "_api"

    A request with auth token bearer and no user session state, triggers token handling from auth module:
     - A hook fires to have the token validated 
        - eg: tokens_auth_get_auth_token_validation spools getDayDateUserTokenCheck supporting the "CMFIVE_API" app
        - auth module accepts the token if a hook handler yields a TokensPolicy
        - The policy is internally 'stateless' & never persisted in cmfive DB
        - then auth module requests for TokensPolicy to allow the current request action
     - A hook fires from TokensPolicy seeking an application model to implement the policy roles
        - the listening app_service will materialise these in standard manner of user->roles->allowed
        - TokensPolicy GetBearersRoles assists with collation
        - Listener will interpret profile identifier per app model (eg: as user_id, group_policy, code-baked app_actions etc)
        - eg: tokens_tokens_get_roles_from_policy spools getCoreRolesByDayDateUserPolicy as implementing "CMFIVE_API" app
            - "CMFIVE_API" app filters filters subset of standard user->roles as _role_profile = user_id

    Result
      - Default/example case of "CMFIVE_API" application is implemented
        - auth module allows user-as-token-bearer to execute actions per user->roles with suffix of "_api"
      - eg:
      curl http://cmfive/tokens-api/loopback  -H 'Content-Type: application/json' -d '{"data1":"setting1","data2":"setting2"}' -H 'Authorization: Bearer MYGRANTEDAPIJWT'
    */

    // Example code, showing how hook handler builds roles
    public function getCoreRolesByDayDateUserPolicy($policy)
    {
        $built_roles = [];
        /*
        // The 'CMFIVE' policy is actually a user ID!

        //get user roles which end in _api
        $rows = AuthService::getInstance($policy->w)->getObjects("UserRole", ["user_id" => $policy->_role_profile]);
        if ($rows) {
            foreach ($rows as $row) {
                if (!in_array($row->role, $built_roles) && str_ends_with($row->role, "_api")) {
                    $built_roles[] = $row->role;
                }
            }
        }

        // get group roles which end in _api
        $groupUsers = AuthService::getInstance($policy->w)->getObjects("GroupUser", ['user_id' => $policy->_role_profile]);
        if ($groupUsers) {
            foreach ($groupUsers as $groupUser) {
                $groupRoles = $groupUser->getGroupRoles();
                foreach ($groupRoles as $groupRole) {
                    if (!in_array($groupRole, $built_roles) && str_ends_with($groupRole, "_api")) {
                        $built_roles[] = $groupRole;
                    }
                }
            }
        }
        */

        return $built_roles;
    }


    // Example code, showing how (not!) to grant a token...
    // These tokens aren't guessable = GOOD
    // But can be synthesised if you see the source code & DB_user table = BAD
    // And they die at midnight (not time length) = confusing?
    // 'better' tokens would need a peristent object store and an isolated synthesis 'seed/key'!
    public function getDayDateUserToken($w)
    {
        return "NO_TOKEN";

        /*
        $header = $this->getBase64URL(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $user = AuthService::getInstance($w)->user();
        $today = date("y-m-d");

        $payload = $this->getBase64URL(json_encode([
            "api" => "CMFIVE",
            "id" => hash('sha256', $today . $user->password),
            "limit" => $today
        ]));

        $signature = hash('sha256', $header . "." . $payload);

        return $header . "." . $payload . "." . $signature;
        */
    }


    // Example code, showing how hook handler can validate token
    public function getDayDateUserTokenCheck($jwt)
    {
        return null;

        /*
        if (!$this->getJwtSignatureCheck($jwt)) {
            return null;
        };

        // The policy is internally 'stateless' & never persisted in cmfive DB
        $internal = new TokensPolicy($this->w);
        $internal->_validator = "CMFIVE";
        $internal->_raw_jwt = $jwt;
        $internal->_app_id = "CMFIVE_API";
        $today = date("y-m-d");

        $parts = explode(".", $jwt);
        $payload = json_decode(base64_decode($parts[1] ?? ""), true);

        if ($payload['limit'] !== date("y-m-d") || $payload['api'] !== $internal->_validator) {
            return null;
        }

        // for simplest implementation of policy, our policy_id will be user_id, hence filtered from user->roles
        $candidates = $this->getObjects("User", ['is_active' => true, 'is_external' => false, 'is_deleted' => false, 'is_group' => false]);
        foreach ($candidates as $check) {
            if ($payload['id'] == hash('sha256', $today . $check->password)) {
                $internal->_role_profile = $check->id;
                return $internal;
            }
        }

        return null;
        */
    }
}
