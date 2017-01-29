<?php
/*
 *  Display Edit and Testdata forms for Templates
 */

function edit_GET(Web $w) {
	$w->Admin->navigation($w,"Templates");
	$p = $w->pathMatch("id");
	
	$t = $w->Template->getTemplate($p['id']);
	$t = $t ? $t : new Template($w);
	
	$newForm = array();
	$newForm["Template Details"] = array(
		array(
                    array("Title", "text", "title",$t->title),
                    array("Active", "checkbox", "is_active",$t->is_active)),
		array(
                    array("Module", "text", "module",$t->module),
                    array("Category", "text", "category",$t->category)));
	$newForm['Description'] = array(
		array(
                    array("", "textarea", "description",$t->description)),
	);

	$w->ctx("editdetailsform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/'.$t->id)));
	
	$newForm = array();
	$newForm["Template Title"] = array(
			array(array("", "textarea", "template_title",$t->template_title,100,1, false))
	);
	$newForm["Template Body"] = array(
			array(array("", "textarea", "template_body",$t->template_body,60,100, "codemirror"))
	);

	$w->ctx("templateform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/'.$t->id)));
	
	$newForm = array();
	$newForm["Title Data"] = array(
			array(array("", "textarea", "test_title_json",$t->test_title_json,100,5, false))
	);
	$newForm["Body Data"] = array(
			array(array("", "textarea", "test_body_json",$t->test_body_json,100,20, false))
	);
	
	$w->ctx("testdataform", Html::multiColForm($newForm, $w->localUrl('/admin-templates/edit/'.$t->id)));
	$w->ctx("id", $p['id']);
//        try {
//            $w->ctx("testtitle",$t->testTitle());
//            $w->ctx("testbody",$t->testBody());
//        } catch (Exception $e) {
//            $w->ctx("testbody", "Error: Couldn't not render Twig template.<br/>Error Message: " . $e->getMessage());
//        }
		
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	$t = $p["id"] ? $w->Template->getTemplate($p['id']) : new Template($w);
        $t->fill($_POST);
        
        // Set is active if saving is originating from the first page
        if (isset($_POST["title"]) && isset($_POST["module"]) && isset($_POST["category"])) {
            $t->is_active = intval($w->request("is_active"));
        }
	
	$t->insertOrUpdate();
	$w->msg("Template saved", "/admin-templates/edit/".$t->id);	
}