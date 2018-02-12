<?php

function view_GET(Web $w) {

	$w->setLayout('layout-f6');

	list($task_id) = $w->pathMatch('task_id');

	if (empty($task_id)) {
		$w->error('Task not found', '/task/list');
	}

	$task = $w->Task->getTask($task_id);
	if (empty($task->id)) {
		$w->error('Task not found', '/task/list');
	}

	// Register style components
	CmfiveStyleComponentRegister::registerComponent('task-css', new CmfiveStyleComponent('/system/modules/task/assets/css/style.scss'));

	// Register vue components
	// VueComponentRegister::registerComponent('html-tabs', new VueComponent('html-tabs', '/system/templates/vue-components/html/html-tabs.vue.js', '/system/templates/vue-components/html/html-tabs.vue.scss'));
	// VueComponentRegister::registerComponent('html-tab', new VueComponent('html-tab', '/system/templates/vue-components/html/html-tab.vue.js', '/system/templates/vue-components/html/html-tab.vue.scss'));
	// VueComponentRegister::registerComponent('user-card', new VueComponent('user-card', '/system/templates/vue-components/html/custom/user-card.vue.js', '/system/templates/vue-components/html/custom/user-card.vue.scss'));
	// VueComponentRegister::registerComponent('html-segment', new VueComponent('html-segment', '/system/templates/vue-components/html/custom/html-segment.vue.js', '/system/templates/vue-components/html/custom/html-segment.vue.scss'));
	// VueComponentRegister::registerComponent('html-button-bar', new VueComponent('html-button-bar', '/system/templates/vue-components/html/custom/html-button-bar.vue.js', '/system/templates/vue-components/html/custom/html-button-bar.vue.scss'));

	$w->ctx('task', $task);

}