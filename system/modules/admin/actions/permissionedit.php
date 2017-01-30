<?php

function permissionedit_GET(Web $w) {
    $option = $w->pathMatch("group_id");

    $user = $w->Auth->getUser($option['group_id']);

    $userName = $user->is_group == 1 ? $user->login : $user->getContact()->getFullName();

    $w->Admin->navigation($w, __("Permissions")." - " . $userName);

    //fill in permission tables;
    $groupUsers = $w->Auth->getUser($option['group_id'])->isInGroups();
    $groupRoles = array();
    if ($groupUsers) {
        foreach ($groupUsers as $groupUser) {
            $grs = $groupUser->getGroup()->getRoles();

            foreach ($grs as $gr) {
                $groupRoles[] = $gr;
            }
        }
    }

    $allroles = $w->Auth->getAllRoles();

    foreach ($allroles as $role) {
        $parts = explode("_", $role);

        if (count($parts) == 1) {
            array_unshift($parts, "admin");
        }
        
        $module = array_shift($parts);

        $result[$module][] = implode("_", $parts);
    }

    $permission = array();
    foreach ($result as $module => $parts) {
        $parts = array_chunk($parts, 4);

        foreach ($parts as $level => $roles) {
            foreach ($roles as $r) {
                $roleName = $module == "admin" ? $r : implode("_", array($module, $r));

                $permission[ucwords($module)][$level][] = array($roleName, "checkbox", "check_" . $roleName, $w->Auth->getUser($option['group_id'])->hasRole($roleName));
            }
        }
    }
    $action = $w->Auth->user()->is_admin ? "/admin/permissionedit/" . $option['group_id'] : null;

    $w->ctx("permission", Html::multiColForm($permission, $action));

    $w->ctx("groupRoles", json_encode($groupRoles));
}

function permissionedit_POST(Web &$w) {
    $option = $w->pathMatch("group_id");
    //update permissions for user/group;
    $user = $w->Auth->getUser($option['group_id']);
    //add roles;
    $roles = $w->Auth->getAllRoles();
    foreach ($roles as $r) {
        if (!empty($_POST["check_" . $r])) {
            if ($_POST["check_" . $r] == 1) {
                $user->addRole($r);
            }
        }
    }
    //remove roles;
    $userRoles = $user->getRoles();

    foreach ($userRoles as $userRole) {
        if (!array_key_exists("check_" . $userRole, $_POST)) {
            $user->removeRole($userRole);
        }
    }
    $returnPath = $user->is_group == 1 ? "/admin/moreInfo/" . $option['group_id'] : "/admin/users";

    $w->msg(__("Permissions are updated!"), $returnPath);
}
