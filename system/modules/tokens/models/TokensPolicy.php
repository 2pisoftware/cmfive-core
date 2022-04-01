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
        echo print_r($this->_validator, true);
        echo print_r($this->_role_profile, true);
        
        
        // So, from here, let's hit another hook, for validators/service models to populate roles from profile?
        if ($this->_validator == 'CMFIVE') {
            $roles = TokensService::getCoreRolesByDayDateUserPolicy();
        }
        // A straight function would give us array of string (roles)
        // A hook response would give as an array of string arrays
        // we would need to merge & de-dupe...
        // No panic, PHP hass native functions for such.
        
        // broadcast this policy
        // listener will self identify as _validator
        // listener will interpret profile identifier
        // listern will return conventional/conformed roles list

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
