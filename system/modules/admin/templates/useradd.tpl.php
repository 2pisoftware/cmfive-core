<?php

echo HtmlBootstrap5::multiColForm(
    $form,
    $w->localUrl("/admin/useradd"),
    "POST",
    "Save",
    null,
    null,
    null,
    "_self",
    true,
    array_merge(User::$_validation, ['password' => ['required'], 'password2' => ['required']])
);
