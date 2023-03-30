<?php
/*
 *  Display Edit and Testdata forms for Templates
 */

function edit_GET(Web $w)
{
	AdminService::getInstance($w)->navigation($w, "Templates");
	$p = $w->pathMatch("id");

	$t = TemplateService::getInstance($w)->getTemplate($p['id']);
	$t = $t ? $t : new Template($w);

	$newForm = [];
	$newForm["Template Details"] = [
		[
			["Title", "text", "title", $t->title],
			["Active", "checkbox", "is_active", $t->is_active]
		],
		[
			["Module", "text", "module", $t->module],
			["Category", "text", "category", $t->category]
		]
	];
	$newForm['Description'] = [
		[
			["", "textarea", "description", $t->description]
		],
	];

	$w->ctx("editdetailsform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/' . $t->id)));

	$newForm = [];
	$newForm["Template Title"] = [
		[["", "textarea", "template_title", $t->template_title, 100, 1, false]]
	];
	$newForm["Template Body"] = [
		[["", "textarea", "template_body", $t->template_body, 60, 100, "codemirror"]]
	];

	$w->ctx("templateform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/' . $t->id)));

	$newForm = [];
	$newForm["Title Data"] = [
		[["", "textarea", "test_title_json", $t->test_title_json, 100, 5, false]]
	];
	$newForm["Body Data"] = [
		[["", "textarea", "test_body_json", $t->test_body_json, 100, 20, false]]
	];

	$w->ctx("testdataform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/' . $t->id)));
	$w->ctx("id", $p['id']);
}

function edit_POST(Web $w)
{
	$p = $w->pathMatch("id");
	$t = $p["id"] ? TemplateService::getInstance($w)->getTemplate($p['id']) : new Template($w);
	$t->fill($_POST);

	// Set is active if saving is originating from the first page
	if (isset($_POST["title"]) && isset($_POST["module"]) && isset($_POST["category"])) {
		$t->is_active = intval(Request::int("is_active"));
	}

	$t->insertOrUpdate();
	$w->msg("Template saved", "/admin-templates/edit/" . $t->id);
}
