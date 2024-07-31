<?php

use Html\Form\Select;
use Html\Form\InputField\Text;

function edit_GET(Web &$w)
{
    $p = $w->pathMatch("id", "type");

    $lookup = LookupService::getInstance($w)->getLookup($p['id']);
    $types = LookupService::getInstance($w)->getLookupTypes();

    $form = [
        (!empty($lookup->id) ? 'Edit' : 'Create') . ' Lookup Item' => [
            [
                (new Select([
                    'id|name' => 'type',
                    'selected_option' => $lookup->type ?? '',
                    'label' => 'Type',
                    'options' => $types,
                ]))
            ],
            [
                (new Text([
                    'id|name' => 'code',
                    'label' => 'Code',
                    'value' => $lookup->code ?? '',
                    'required' => true,
                ]))
            ],
            [
                (new Text([
                    'id|name' => 'title',
                    'label' => 'Title',
                    'value' => $lookup->title ?? '',
                    'required' => true,
                ]))
            ],
        ],
    ];

    if (empty($lookup->id)) {
        array_push($form[(!empty($lookup->id) ? 'Edit' : 'Create') . ' Lookup Item'][0], (new \Html\Form\InputField\Text([
            'id|name' => 'ntype',
            'label' => 'or Add New Type',
        ])));
    }

    $w->out(HtmlBootstrap5::multiColForm($form, $w->localUrl("/admin-lookups/edit/" . (!empty($lookup->id) ? $lookup->id . '/' : '') . $p['type']), "POST", empty($lookup->id) ? "Create" : "Update"));
}

function edit_POST(Web &$w)
{
    list($id, $type) = $w->pathMatch("id", "type");

    if (empty($id) && LookupService::getInstance($w)->getLookupByTypeAndCodeV2($_REQUEST['type'], $_REQUEST['code'])) {
        $w->error("Type and Code combination already exists", "/admin-lookups/index?type=" . $type);
    }

    $lookup = !empty($id) ? LookupService::getInstance($w)->getLookup($id) : new Lookup($w);
    $lookup->fill($_POST);

    if (empty($lookup->id) && empty($_POST['type'])) {
        $lookup->type = $_POST['ntype'];
    }

    $lookup->insertOrUpdate();
    $w->msg('Lookup Item ' . (!empty($id) ? 'updated' : 'created'), "/admin-lookups/index?type=" . $type);
}
