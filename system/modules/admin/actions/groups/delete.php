<?php

function delete_ALL(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
        $w->error("Group not found", "/admin-groups");
    }
    
    $group = $w->Auth->getUser($p['id']);
    if (empty($group->id)) {
        $w->error("Group not found", "/admin-groups");
    }
    
    $group->delete();

    $roles = $group->getRoles();
    if (!empty($roles)) {
        foreach ($roles as $role) {
            $group->removeRole($role);
        }
    }
    $members = $w->Auth->getGroupMembers($option['group_id']);

    if ($members) {
        foreach ($members as $member) {
            $member->delete();
        }
    }
    $w->msg("Group deleted", "/admin-groups");
}
