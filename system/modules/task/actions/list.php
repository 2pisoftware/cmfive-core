<?php

/**
 * List tasks
 */
function list_GET(Web $w) {
    $w->setLayout('layout-f6');

    // Task list action
    History::add('Task List');

    // Register needed scripts
    CmfiveScriptComponentRegister::registerComponent('vue-search-select', new CmfiveScriptComponent('/system/templates/vue-components/form/elements/vue-search-select/vue-search-select.min.js'));
    CmfiveScriptComponentRegister::registerComponent('pagination', new CmfiveScriptComponent('/system/templates/vue-components/html/twopipagination.js'));
    CmfiveScriptComponentRegister::registerComponent('natsort', new CmfiveScriptComponent('/system/templates/js/natsort.min.js'));
    CmfiveScriptComponentRegister::registerComponent('filtersort', new CmfiveScriptComponent('/system/templates/vue-components/html/filterSort.js'));
    CmfiveStyleComponentRegister::registerComponent('task_style', new CmfiveStyleComponent('/system/modules/task/assets/css/style.scss', ['/system/templates/scss/']));

    // Filter props
    $w->ctx("assignees", $w->Task->getAssignees());
    $w->ctx("creators", $w->Task->getCreators());
    $w->ctx("task_groups", $w->Task->getTaskGroups());
    $w->ctx("task_types", $w->Task->getTaskTypesList());
    $w->ctx("priority_list", $w->Task->getPriorityList());
    $w->ctx("status_list", $w->Task->getStatusList());
}