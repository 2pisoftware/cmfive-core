<?php

/**@author Derek Crannaford */

class TokensPolicy extends DbObject
{
    public $_validator;
    public $_role_profile;
    public $_raw_jwt;
    public $_app_id;

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

    public function getBearersRoles(): array
    {
        // Consider remaining stateless here...
        // we have persisted no concept of the token beyond:
        // - it was asserted valid
        // - the VALIDATOR identified itself (for stubbed purposes, this is CMFIVE)
        // - the VALIDATOR confirmed the implementation of an APP
        // - the VALIDATOR asserted a ROLE PROFILE (for simplest purposes, this could be a CMFIVE USER)

        // So, from here, let's hit another hook, for validators/app_service models to populate roles from profile:

        // broadcast this policy
        // listener will self identify as _validator and _app_id
        // listener will interpret profile identifier (eg as user_id, group_policy, code-baked app_actions etc)
        $hook_results = $this->w->callHook("tokens", "get_roles_from_policy", $this);

        if (empty($hook_results)) {
            LogService::getInstance($this->w)->error("No Roles for policy:" . $this->_role_profile);
        }

        // listener will return conventional/conformed roles list
        // which we clean up in case of merged results typical of hooks:
        $roles = [];
        foreach ($hook_results as $_roles) {
            $roles = array_merge($roles, $_roles ?? []);
        }
        $roles = array_unique($roles);

        return $roles;
    }

    /**
     * Check whether a bearer is allowed to navigate to a certain url
     * in the system.
     *
     * This will execute all the functions associated to the bearer's roles
     * until one function returns true.
     *
     * @param Web $w
     * @param string $path
     * @return true if one role function returned true
     */
    public function tokensAllowed($path): bool
    {
        $roles = $this->getBearersRoles() ?? [];

        foreach ($roles as $rn) {
            $rolefunc = "role_" . $rn . "_allowed";
            $policyfunc = "token_policy_" . $rn . "_allowed";
            if (!function_exists($rolefunc) && !function_exists($policyfunc)) {
                LogService::getInstance($this->w)->error("Role or policy'" . $rn . "' does not exist!");
                continue;
            }
            // These will be visible as user role selectors
            // Ticked user can navigate to, if logged in!
            if (function_exists($rolefunc) && $rolefunc($this->w, $path)) {
                return true;
            }
            // These have no visible roles
            // Only a token policy can grant page access
            if (function_exists($policyfunc) && $policyfunc($this->w, $path)) {
                return true;
            }
        }

        return false;
    }
}
