<?php

use Html\Form\Select;

function editlookup_GET(Web &$w)
{
    $p = $w->pathMatch("id", "type");

    $lookup = LookupService::getInstance($w)->getLookup($p['id']);

    if ($lookup) {
        $types = LookupService::getInstance($w)->getLookupTypes();

        $w->out(HtmlBootstrap5::multiColForm([
            'Edit an Existing Entry' => [
                [
                    (new Select([
                        'id|name' => 'type',
                        'selected_option' => $lookup->type,
                        'label' => 'Type',
                        'options' => $types,
                    ])),
                ],
                [
                    (new \Html\Form\InputField\Text([
                        'id|name' => 'code',
                        'label' => 'Code',
                        'value' => $lookup->code,
                    ]))
                ],
                [
                    (new \Html\Form\InputField\Text([
                        'id|name' => 'title',
                        'label' => 'Title',
                        'value' => $lookup->title,
                    ]))
                ],
            ],
        ], $w->localUrl("/admin/editlookup/" . $lookup->id . "/" . $p['type']), "POST", " Update "));
/*
        $f = HtmlBootstrap5::multiColForm(array(
            array("Edit an Existing Entry", "section"),
            array("Type", "select", "type", $lookup->type, $types),
            array("Key", "text", "code", $lookup->code),
            array("Value", "text", "title", $lookup->title),
        ), $w->localUrl("/admin/editlookup/" . $lookup->id . "/" . $p['type']), "POST", " Update ");

        //$w->setLayout(null);
        $w->out($f);
*/
    } else {
        $w->msg("No such Lookup Item?", "/admin/lookup/");
    }
}

function editlookup_POST(Web &$w)
{
    $p = $w->pathMatch("id", "type");

    $err = "";
    if ($_REQUEST['type'] == "")
        $err = "Please add select a TYPE<br>";
    if ($_REQUEST['code'] == "")
        $err .= "Please enter a KEY<br>";
    if ($_REQUEST['title'] == "")
        $err .= "Please enter a VALUE<br>";

    if ($err != "") {
        $w->error($err, "/admin/lookup/?type=" . $p['type']);
    } else {
        $lookup = LookupService::getInstance($w)->getLookup($p['id']);

        if ($lookup) {
            $lookup->fill($_REQUEST);
            $lookup->update();
            $msg = "Lookup Item edited";
        } else {
            $msg = "Could not find item?";
        }
        $w->msg($msg, "/admin/lookup/?type=" . $p['type']);
    }
}
