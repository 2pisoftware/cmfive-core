<?php

function delete_country_GET(Web $w)
{
    list($id) = $w->pathMatch("id");

    $country = AdminService::getInstance($w)->getCountry($id);
    $country->delete();

    $w->msg("Country deleted", "/admin-lookups/index#countries");
}
