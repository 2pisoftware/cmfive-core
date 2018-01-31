<?php

function list_GET(Web $w) {

	$w->setLayout('layout-f6');
	
	// Task list action
	History::add('Task List');

	// Register vue components
	VueComponentRegister::registerComponent('list-filter', new VueComponent('list-filter', '/system/templates/vue-components/filter/list-filter.vue.js', '/system/templates/vue-components/filter/list-filter.vue.scss'));
	VueComponentRegister::registerComponent('select-filter', new VueComponent('select-filter', '/system/templates/vue-components/filter/select-filter.vue.js')); //, '/system/modules/form/assets/js/metadata-select.vue.css'));
	VueComponentRegister::registerComponent('html-table', new VueComponent('html-table', '/system/templates/vue-components/html/html-table.vue.js', '/system/templates/vue-components/html/html-table.vue.scss'));

	$w->ctx('task_types', []);

}