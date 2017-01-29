<?php

function timelog_core_template_menu(Web $w) {
    if ($w->Timelog->shouldShowTimer()) {
        return $w->partial('timelogwidget', null, 'timelog');
    }
}