<?php

if (!empty($form)) {
    echo $w->partial("show_form_wizard", ["form" => $form, "object_class" => "class", "object_id" => "0"]);
}
