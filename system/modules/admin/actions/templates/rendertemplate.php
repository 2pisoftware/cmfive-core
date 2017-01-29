<?php

function rendertemplate_ALL(Web $w) {
    $p = $w->pathMatch("id");
	
    $t = $w->Template->getTemplate($p['id']);
    $t = $t ? $t : new Template($w);
    
    $w->setLayout(null);
    $w->out($t->testTitle());
    $w->out("<hr/>");
    $w->out($t->testBody());
}
