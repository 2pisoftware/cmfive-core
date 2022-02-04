<?php
function lookup_ALL(Web &$w)
{
    AdminService::getInstance($w)->navigation($w, "Lookup");

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
                Html::box($w->localUrl("/admin/editlookup/" . $look->id . "/" . urlencode(Request::string('type', ''))), " Edit ", true) .
                    "&nbsp;&nbsp;&nbsp;" .
                    Html::b($w->webroot() . "/admin/deletelookup/" . $look->id . "/" . urlencode(Request::string('type', '')), " Delete ", "Are you sure you wish to DELETE this Lookup item?")
            ];
        }
    } else {
        $line[] = ["No Lookup items to list", null, null, null];
    }

    // display list of items, if any
    $w->ctx("listitem", Html::table($line, null, "tablesorter", true));


    // tab: new lookup item
    $types = LookupService::getInstance($w)->getLookupTypes();

    $f = Html::form([
        ["Create a New Entry", "section"],
        ["Type", "select", "type", null, $types],
        ["or Add New Type", "text", "ntype"],
        ["Code", "text", "code"],
        ["Title", "text", "title"],
    ], $w->localUrl("/admin/newlookup/"), "POST", " Save");

    $w->ctx("newitem", $f);
}
