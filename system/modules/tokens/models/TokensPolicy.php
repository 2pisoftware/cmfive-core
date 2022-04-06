<?php

/**@author Derek Crannaford */

class TokensPolicy extends DbService
{
    public $_validator;
    public $_role_profile;


    public function getBearersRoles()
    {
        // Consider remaining stateless here...
        // we have persisted no concept of the token beyond:
        // - it was asserted valid
        // - the VALIDATOR identified itself (for stubbed purposes, this is CMFIVE)
        // - the VALIDATOR asserted a ROLE PROFILE (for stubbed purposes, this will be a CMFIVE USER)
        //echo print_r($this->_validator, true);
        //echo print_r($this->_role_profile, true);

        // So, from here, let's hit another hook, for validators/service models to populate roles from profile?
        
        $hook_results = $this->w->callHook("tokens", "get_roles_from_policy", $this);

        if (empty($hook_results)) {
            $this->Log->error("No Roles for policy:" . $this->_role_profile);
        }

        $roles = [];
        foreach ($hook_results as $module => $_roles) {
            $roles = array_merge($roles, $_roles);
        }
        $roles = array_unique($roles);

        // echo "\n" ;
        // echo print_r("Roles: ", true);
        // echo print_r($roles, true);

        return $roles;
        
        // broadcast this policy
        // listener will self identify as _validator
        // listener will interpret profile identifier
        // listern will return conventional/conformed roles list
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
    public function tokensAllowed($path)
    {
        $roles = $this->getBearersRoles() ?? [];

        foreach ($roles as $rn) {
            $rolefunc = "role_" . $rn . "_allowed";
            if (function_exists($rolefunc)) {
                if ($rolefunc($this->w, $path)) {
                    return true;
                }
            } else {
                $this->w->Log->error("Role '" . $rn . "' does not exist!");
            }
        }

        return false;
    }
}
