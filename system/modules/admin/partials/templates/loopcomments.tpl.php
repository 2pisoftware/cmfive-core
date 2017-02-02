<?php

if (!empty($comments)) {
    foreach($comments as $c) {
        echo $w->partial("displaycomment", array("object" => $c, "redirect" => $redirect, "internal_only" => !empty($internal_only) ? $internal_only : false, "external_only" => !empty($external_only) ? $external_only : false), "admin");
    }
}
