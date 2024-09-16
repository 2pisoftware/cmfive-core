<?php

function index_ALL(Web &$w)
{
    $w->setLayout('layout-bootstrap-5');
    AdminService::getInstance($w)->navigation($w, "Lookups");
    History::add("List Lookups");

    $types = LookupService::getInstance($w)->getLookupTypes();

    $typelist = Html::select("type", $types, Request::string('type'));
    $w->ctx("typelist", $typelist);

    // tab: Lookup List
    $where = [];
    if (Request::string('reset') == null) {
        if (Request::string('type') != "") {
            $where['type'] = Request::string('type');
        }
    } else {
        // Reset called, unset vars
        if (Request::string("type") !== null) {
            unset($_REQUEST["type"]);
        }
    }

    $lookup = LookupService::getInstance($w)->getLookupsWhere($where);

    $line[] = ["Type", "Code", "Title", "Actions"];

    if ($lookup) {
        foreach ($lookup as $look) {
            $line[] = [
                $look->type,
                $look->code,
                $look->title,
                HtmlBootstrap5::box(
                    href: $w->localUrl("/admin-lookups/edit/" . $look->id . "/" . urlencode(Request::string('type', ''))),
                    title: "Edit",
                    class: 'btn btn-sm btn-primary') .
                HtmlBootstrap5::b(
                    href: $w->localUrl("/admin-lookups/delete/" . $look->id . "/" . urlencode(Request::string('type', ''))),
                    title: "Delete",
                    confirm: "Are you sure you wish to DELETE this Lookup item?",
                    class: 'btn btn-sm btn-danger')
            ];
        }
    } else {
        $line[] = ["No Lookup items to list", null, null, null];
    }

    // display list of items, if any
    $w->ctx("listitem", Html::table($line, null, "tablesorter", true));

    // Countries tab
    $countries = AdminService::getInstance($w)->getCountries();
    uasort($countries, fn ($a, $b) => $a->name <=> $b->name);
    $country_rows = array_map(fn (Country $c) => [
        $c->name, 
        $c->alpha_2_code . ' / ' . $c->alpha_3_code, 
        $c->demonym,
        HtmlBootstrap5::box(href: "/admin-lookups/edit_country/" . $c->id, title: "Edit", class: 'btn btn-sm btn-primary') .
        HtmlBootstrap5::b(href: "/admin-lookups/delete_country/" . $c->id, title: "Delete", confirm: "Are you sure you wish to delete this Country?", class: 'btn btn-sm btn-danger')
    ], $countries);

    $w->ctx("country_rows", Html::table($country_rows, null, "tablesorter", ["Name", "Code", "Demonym", "Actions"]));
}
