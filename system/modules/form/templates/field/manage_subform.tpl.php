<?php

if (!empty($error_message)) {
    echo "<div data-alert class=\"alert-box warning radius\">$error_message</div>";
    return;
}

if ($is_singleton) {
    echo $w->partial("show_form", [
        "form" => $subform,
        "redirect_url" => "/form-field/manage_subform/$form_value->id",
        "object" => $form_value,
        "display_only" => $display_only,
    ], "form");
} else {
    echo $w->partial("listform", [
        "form" => $subform,
        "redirect_url" => '/form-field/manage_subform/' . $form_value->id,
        'object' => $form_value,
        'display_only' => $display_only,
    ], "form");
}
