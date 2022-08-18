<?php

namespace System\Modules\Task;

function listchecklists(\Web $w, $params) {

    $object = $params['object'];
    $redirect = $params['redirect'];

    $checklists = [];
    $checklist['items'] = [];

    echo $object;

    // if ($object['id']) {echo '1';}
    // {
    //     echo '1';
    //     $checklists = getTaskChecklists($object);
    //     echo '2';
    //     if (!empty($checklist)) 
    //     {
    //         foreach($checklists as $checklist => $checklist_id)
    //         {
    //             $checklist['items'] = getTaskChecklistData($checklist_id);      
    //         }
    //     }
    // }

    $w->ctx("checklists", $checklists);
    $w->ctx("redirect", $redirect);
    $w->ctx("task", $object);
}
