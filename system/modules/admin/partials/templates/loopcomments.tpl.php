<?php

if (!empty($comments)) {
    foreach ($comments as $c) {
        if (!$c->isRestricted() || ($c->isRestricted() && $c->canView(AuthService::getInstance($w)->user()))) {
            echo $w->partial("displaycomment", [
                "object" => $c,
                "redirect" => $redirect,
                "internal_only" => !empty($internal_only) ? $internal_only : false,
                "external_only" => !empty($external_only) ? $external_only : false,
                "is_outgoing" => false
            ], "admin");
        }
    }
}
