<?php

if (!empty($comments)) {
    foreach($comments as $c) {
        echo $w->partial("displaycomment", array("object" => $c, "redirect" => $redirect), "admin");
    }
}
