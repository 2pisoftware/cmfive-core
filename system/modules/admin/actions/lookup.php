<?php

use Html\Form\Select;

function lookup_ALL(Web &$w)
{
    $w->setLayout('layout-bootstrap-5');

    AdminService::getInstance($w)->navigation($w, "Lookup");

    $types = LookupService::getInstance($w)->getLookupTypes();

    $selectedtype = Request::string('type');
    $w->ctx("selectedtype", $selectedtype);

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

    $table = array(array("Type", "Code", "Title", "Actions", "sort_key" => null));

    if (!empty($lookup)) {
        foreach ($lookup as $look) {
            $line = [];

            $line[] = $look->type;
            $line[] = $look->code;
            $line[] = $look->title;
            $line[] = HtmlBootstrap5::buttonGroup(
                HtmlBootstrap5::box($w->localUrl("/admin/editlookup/" . $look->id . "/" . urlencode(Request::string('type', ''))), " Edit ", true, false, null, null, 'isbox', null, 'btn btn-sm btn-secondary') .
                    HtmlBootstrap5::b($w->webroot() . "/admin/deletelookup/" . $look->id . "/" . urlencode(Request::string('type', '')), " Delete ", "Are you sure you wish to DELETE this Lookup item?", "deletebutton", false, "btn-sm btn-danger")
            );
            $line['sort_key'] = strtoupper($look->type) . strtoupper($look->code) . strtoupper($look->title);

            $table[] = $line;
        }

        // Order by sort key (group name in uppercase)
        array_multisort(
            array_column($table, "sort_key"),
            SORT_ASC,
            $table
        );
        // Remove sort column
        for ($i = 0, $length = count($table); $i < $length; ++$i) {
            unset($table[$i]["sort_key"]);
        }
    } else {
        $table[] = ["No Lookup items to list", null, null, null, null];
    }

    // display list of items, if any
    $w->ctx("listitem", HtmlBootstrap5::table($table, null, "tablesorter", true));


    // tab: new lookup item
    $types = LookupService::getInstance($w)->getLookupTypes();

    $w->ctx('newitem', HtmlBootstrap5::multiColForm([
        'Create a New Entry' => [
            [
                (new Select([
                    'id|name' => 'type',
                    'selected_option' => null,
                    'label' => 'Type',
                    'options' => $types,
                ])),
            ],
            [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'ntype',
                    'label' => 'or Add New Type',
                ]))
            ],
            [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'code',
                    'label' => 'Code',
                ]))
            ],
            [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'title',
                    'label' => 'Title',
                ]))
            ],
        ],
    ], $w->localUrl("/admin/newlookup/"), "POST", "Save"));
}
