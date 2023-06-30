<?php
function lookup_ALL(Web &$w)
{
    $w->setLayout('layout-bootstrap-5');

    AdminService::getInstance($w)->navigation($w, "Lookup");

    $types = LookupService::getInstance($w)->getLookupTypes();

    $typelist = HtmlBootstrap5::select("type", $types, Request::string('type'));
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
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::box($w->localUrl("/admin/editlookup/" . $look->id . "/" . urlencode(Request::string('type', ''))), " Edit ", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary') .
                    HtmlBootstrap5::b($w->webroot() . "/admin/deletelookup/" . $look->id . "/" . urlencode(Request::string('type', '')), " Delete ", "Are you sure you wish to DELETE this Lookup item?", "deletebutton", false, "btn-sm btn-danger")
                )
            ];
        }
    } else {
        $line[] = ["No Lookup items to list", null, null, null];
    }

    // display list of items, if any
    $w->ctx("listitem", HtmlBootstrap5::table($line, null, "tablesorter", true));


    // tab: new lookup item
    $types = LookupService::getInstance($w)->getLookupTypes();

    $f = HtmlBootstrap5::form([
        ["Create a New Entry", "section"],
        ["Type", "select", "type", null, $types],
        ["or Add New Type", "text", "ntype"],
        ["Code", "text", "code"],
        ["Title", "text", "title"],
    ], $w->localUrl("/admin/newlookup/"), "POST", " Save");

    $w->ctx("newitem", $f);
}
