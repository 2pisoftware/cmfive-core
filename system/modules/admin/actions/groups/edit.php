<?php

// This edit action should be called in a modal dialog

function edit_GET(Web $w) {
    $p = $w->pathMatch("id");
    $user = (!empty($p['id']) ? AuthService::getInstance($w)->getUser($p['id']) : new User($w));
    
    $template[($user->id ? "Edit" : "Create") . ' Group'] = array(array(array("Group Title","text", "title", $user->login)));
    $w->out(Html::multiColForm($template,"/admin-groups/edit/".$user->id));
    $w->setLayout(null);
}

function edit_POST(Web $w) {
    $p = $w->pathMatch("id");

    $group = !empty($p['id']) ? AuthService::getInstance($w)->getUser($p['group_id']) : new User($w);
    $group->login = Request::string('title');
    $group->is_group = 1;
    $group->insertOrUpdate();

    $w->msg("Group " . (!empty($p['id']) ? "updated" : "created"), "/admin-groups/show/{$group->id}");
}