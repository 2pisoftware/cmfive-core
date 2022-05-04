<?php

Config::set('main', [
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'https://github.com/2pisoftware',
    "dependencies" => [
        // "monolog/monolog" => "1.22.*@dev",
        "scssphp/scssphp" => "1.9.0"
    ],
    'hooks' => [
        'core_dbobject',
        'admin'
    ],
    'available_languages' => [
        'en_AU' => 'English',
        'de_DE' => 'Deutsch',
        'fr_FR' => 'Français',
    ],
    'datepicker_first_day' => 0, /* Set the first day of the week for datepickers, integer between 0 and  6, where 0 is sunday, 1 is monday, etc.*/
    'vue_components' => [
        'html-tabs' => [
            '/system/templates/vue-components/html/html-tabs.vue.js',
            '/system/templates/vue-components/html/html-tabs.vue.scss'
        ],
        'html-tab' => [
            '/system/templates/vue-components/html/html-tab.vue.js',
            '/system/templates/vue-components/html/html-tab.vue.scss'
        ],
        'html-table' => [
            '/system/templates/vue-components/html/html-table.vue.js',
            '/system/templates/vue-components/html/html-table.vue.scss'
        ],
        'html-pagination' => [
            '/system/templates/vue-components/html/html-pagination.vue.js'
        ],
        'user-card' => [
            '/system/templates/vue-components/html/custom/user-card.vue.js',
            '/system/templates/vue-components/html/custom/user-card.vue.scss'
        ],
        'html-segment' => [
            '/system/templates/vue-components/html/custom/html-segment.vue.js',
            '/system/templates/vue-components/html/custom/html-segment.vue.scss'
        ],
        'html-button-bar' => [
            '/system/templates/vue-components/html/custom/html-button-bar.vue.js',
            '/system/templates/vue-components/html/custom/html-button-bar.vue.scss'
        ],
        'modal' => [
            '/system/templates/vue-components/modal.vue.js'
        ],
        'ajax-modal' => [
            '/system/templates/vue-components/ajax-modal.vue.js'
        ],
        'form-row' => [
            '/system/templates/vue-components/form/form-row.vue.js'
        ],
        'list-filter' => [
            '/system/templates/vue-components/filter/list-filter.vue.js',
            '/system/templates/vue-components/filter/list-filter.vue.scss'
        ],
        'select-filter' => [
            '/system/templates/vue-components/filter/select-filter.vue.js'
        ],
        'loading-indicator' => [
            '/system/templates/vue-components/loading-indicator.vue.js',
            '/system/templates/vue-components/loading-indicator.vue.css'
        ],
    ],
]);
