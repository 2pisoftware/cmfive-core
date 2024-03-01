<?php

function purgeunusedroles_GET(Web $w)
{
    $unused_roles = AuthService::getInstance($w)->getUnusedRoles();
    if (!empty($unused_roles)) {
        foreach ($unused_roles as $role) {
            $role = AuthService::getInstance($w)->getRole($role['id']);
            if (!empty($role->id)) {
                $role->delete();
            }
        }
    }

    $w->msg("Unused roles removed", "/admin-maintenance");
}