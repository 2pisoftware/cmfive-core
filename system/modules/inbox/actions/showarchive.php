<?php

function showarchive_ALL(Web $w) {
    $w->Inbox->navigation($w, "Archive");

    $p = $w->pathMatch('num');
    $num = $p['num'] ? $p['num'] : 1;
    
    $new_arch = $w->Inbox->getMessages($num - 1, 40, $w->Auth->user()->id, 1, 1);
    $arch = $w->Inbox->getMessages($num - 1, 40, $w->Auth->user()->id, 0, 1);
    $arch_count = $w->Inbox->getArchCount($w->Auth->user()->id);

    $table_header = array("<input style='margin: 0px;' type='checkbox' id='allChk' onclick='selectAll()' />", "Subject", "Date", "Sender");
    $table_data = array();
    if (!empty($new_arch)) {
        foreach ($new_arch as $q) {
            $table_data[] = array(
                "<input style='margin: 0px;' type='checkbox' id='" . $q->id . "' value='" . $q->id . "' class='classChk'/>",
                Html::a(WEBROOT . "/inbox/view/new/" . $q->id, "<b>" . $q->subject . "</b>"),
                "<b>" . $q->getDate("dt_created", "d/m/Y H:i") . "</b>",
                "<b>" . ($q->sender_id ? $q->getSender()->getFullName() : "") . "</b>"
            );
        }
    }

    if (!empty($arch)) {
        foreach ($arch as $q) {
            $table_data[] = array(
                "<input style='margin: 0px;' type='checkbox' id='" . $q->id . "' value='" . $q->id . "' class='classChk'/>",
                Html::a(WEBROOT . "/inbox/view/read/" . $q->id, $q->subject),
                "<b>" . $q->getDate("dt_created", "d/m/Y H:i") . "</b>",
                "<b>" . ($q->sender_id ? $q->getSender()->getFullName() : "") . "</b>"
            );
        }
    }
    $w->ctx("arch_table", Html::table($table_data, null, "tablesorter", $table_header));
    $w->ctx('pgnum', $num);
    $w->ctx("readtotal", $arch_count);
//    $w->ctx("new_arch", $new_arch);
}
