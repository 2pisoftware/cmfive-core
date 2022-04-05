<?php

/**@author Derek Crannaford */

class TokensService extends DbService
{
    /*
    */

    public function getTokenFromAuthorisationHeader($bearer)
    {
        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            return null;
        }
        return $matches[1];
    }

    public function getCoreTokenCheck($jwt)
    {
        // We could look for a fingerprint here and handle JWT by case
        // OR can defer proper cases to external modules supporting the hook
        // For now we just assess the dinky-Day-Date-User model
        return $this->getDayDateUserTokenCheck($jwt);
    }


    public function getCoreRolesByDayDateUserPolicy($policy)
    {
        // policy is actually a user ID
        if ($policy->_validator == "CMFIVE") {
            $this->_roles = [];
            
            //get user roles which end in _api
            $rows = AuthService::getInstance($policy->w)->getObjects("UserRole", ["user_id" => $policy->_role_profile]);
            if ($rows) {
                foreach ($rows as $row) {
                    if (!in_array($row->role, $this->_roles) && str_ends_with($row->role, "_api")) {
                        $this->_roles[] = $row->role;
                    }
                }
            }

            // get group roles which end in _api
            $groupUsers = AuthService::getInstance($policy->w)->getObjects("GroupUser", ['user_id' => $policy->_role_profile]);
            if ($groupUsers) {
                foreach ($groupUsers as $groupUser) {
                    $groupRoles = $groupUser->getGroupRoles();
                    foreach ($groupRoles as $groupRole) {
                        if (!in_array($groupRole, $this->_roles) && str_ends_with($groupRole, "_api")) {
                            $this->_roles[] = $groupRole;
                        }
                    }
                }
            }
        }
        return $this->_roles;
    }

    // DEV STUBS ONLY FOR REPRESENTATIVE MODEL //
    public function getDayDateUserToken($w)
    {
        // make a 'fake' JWT looking thing
        // not great, because we have password hidden in there!
        // nice would be build proper 3rd block as sig-verifier

        $user = AuthService::getInstance($w)->user();
        $key = base64_encode(base64_encode($user->password) . ".CMFIVE." . base64_encode(date("y-m-d")));
        $bumps = strlen($key)/5;
        $key = substr($key, 0, $bumps) . "." . substr($key, $bumps, $bumps*2) . "." . substr($key, $bumps*3);
        return $key;
    }

    public function getDayDateUserTokenCheck($jwt)
    {
        // as above, we could improve jwt conformance here:

        $internal = new TokensPolicy($this->w);
        $internal->_validator = "CMFIVE";

        // because I don't know any better, let's win a user_id here:
        $parts = explode(".", base64_decode(str_replace(".", "", $jwt)));
        $parts[0] = base64_decode($parts[0] ?? null);
        $parts[1] = str_replace($internal->_validator, "VALID", $parts[1] ?? null);
        $parts[2] = base64_decode($parts[2] ?? null);

        if ($parts[2] !== date("y-m-d") || $parts[1] !== "VALID") {
            return null;
        }
        $user = $this->getObject("User", ['password' => $parts[0]]);

        if (empty($user)) {
            return null;
        }
        $internal->_role_profile = $user->id;

        return $internal;
    }
    // DEV STUBS ONLY FOR REPRESENTATIVE MODEL //
}
