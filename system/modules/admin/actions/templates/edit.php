<?php
/*
 *  Display Edit and Testdata forms for Templates
 */

function edit_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');

    AdminService::getInstance($w)->navigation($w, "Templates");
    $p = $w->pathMatch("id");

    $t = TemplateService::getInstance($w)->getTemplate($p['id']);
    $t = $t ? $t : new Template($w);

    $newForm = [];
    $newForm["Template Details"] = [
        [
            (new \Html\Form\InputField\Text([
                "id|name" => "title",
                "label" => "Title",
                "required" => true,
                "value" => StringSanitiser::sanitise($t->title)
            ])), // ["Title", "text", "title", $t->title],
            (new \Html\Form\InputField\Checkbox([
                "id|name" => "is_active",
                'label' => 'Active',
                "class" => "",
                "checked" => $t->is_active ? $t->is_active : null
            ])) // ->setAttribute("is_active", $t->is_active) //["Active", "checkbox", "is_active", $t->is_active]
        ],
        [
            (new \Html\Form\InputField\Text([
                "id|name" => "module",
                "label" => "Module",
                "required" => true,
                "value" => StringSanitiser::sanitise($t->module)
            ])), //["Module", "text", "module", $t->module],
            (new \Html\Form\InputField\Text([
                "id|name" => "category",
                "label" => "Category",
                "required" => true,
                "value" => StringSanitiser::sanitise($t->category)
            ])) //["Category", "text", "category", $t->category]
        ]
    ];
    $newForm['Description'] = [
        [
            (new \Html\Cmfive\QuillEditor([
                "id|name" => "description",
                "value" => $t->description,
                "label" => "Description",
            ]))->setOptions(["placeholder" => "Please provide a brief description of the template"])
        ],
    ];

    $w->ctx("editdetailsform", HtmlBootstrap5::multiColForm($newForm, $w->localUrl('/admin-templates/edit/' . $t->id)));

    $newForm = [];
    $newForm["Template Title"] = [
        [
            (new \Html\Form\InputField\Text([
                "id|name" => "template_title",
                "value" => StringSanitiser::sanitise($t->template_title),
                "maxlength" => 100
            ]))
        ]
    ];
    $newForm["Template Body"] = [
        [
            (new \Html\Cmfive\CodeMirrorEditor([
                "id|name" => "template_body",
                "value" => $t->template_body,
            ]))
        ]
    ];

    $w->ctx("templateform", HtmlBootstrap5::multiColForm($newForm, $w->localUrl('/admin-templates/edit/' . $t->id)));

    $newForm = [];
    $newForm["Title Data"] = [
        [
            (new \Html\Form\InputField\Text([
                "id|name" => "test_title_json",
                "value" => StringSanitiser::sanitise($t->test_title_json),
                "maxlength" => 500
            ])) //["", "textarea", "test_title_json", $t->test_title_json, 100, 5, false]]
        ]
    ];
    $newForm["Body Data"] = [
        [
            (new \Html\Form\Textarea([
                "id|name" => "test_body_json",
                "value" => StringSanitiser::sanitise($t->test_body_json),
                "maxlength" => 2000
            ])) // ["", "textarea", "test_body_json", $t->test_body_json, 100, 20, false]]
        ]
    ];

    $w->ctx("testdataform", HtmlBootstrap5::multiColForm($newForm, $w->localUrl('/admin-templates/edit/' . $t->id)));
    $w->ctx("id", $p['id']);
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $t = $p["id"] ? TemplateService::getInstance($w)->getTemplate($p['id']) : new Template($w);
    $t->fill($_POST);
    if (array_key_exists('template_body', $_POST)) {
        $t->template_body = json_decode($_POST['template_body']);
    }

    // Set is active if saving is originating from the first page
    if (isset($_POST["title"]) && isset($_POST["module"]) && isset($_POST["category"])) {
        $t->is_active = !empty($_REQUEST['is_active']) ? $_REQUEST['is_active'] : 0;
        $t->is_active = !empty($_REQUEST['is_active']) ? $_REQUEST['is_active'] : 0;
    }

    $t->insertOrUpdate();
    $w->msg("Template saved", "/admin-templates/edit/" . $t->id . "#tab-1");
}
