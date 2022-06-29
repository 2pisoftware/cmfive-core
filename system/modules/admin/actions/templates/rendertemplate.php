<?php

function rendertemplate_ALL(Web $w) {
    $p = $w->pathMatch("id");
	
    $t = TemplateService::getInstance($w)->getTemplate($p['id']);
    $t = $t ? $t : new Template($w);
    
    $w->setLayout(null);

    $testTitle = $t->testTitle();
    if (!empty($testTitle)) {
	    $w->out($testTitle);
	    $w->out("<hr/>");
	}
    $w->out($t->testBody());
}
