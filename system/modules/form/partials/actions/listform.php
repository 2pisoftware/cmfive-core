<?php

namespace System\Modules\Form;

function listform(\Web $w, $params)
{
    // $w->enqueueScript(['name' => 'vue-js', 'uri' => '/system/templates/js/vue.js', 'weight' => 200]);
    \CmfiveStyleComponentRegister::registerComponent('list-form', new \CmfiveStyleComponent('/system/templates/base/dist/FormListForm.css'));

    $w->ctx('redirect_url', $params['redirect_url']);
    $w->ctx('form', $params['form']);
    $w->ctx('object', $params['object']);
    $w->ctx('display_only', !empty($params['display_only']) ? !!$params['display_only'] : false);

    $paginated = !empty($params['paginated']) ?? false;
    $w->ctx('paginated', $paginated);

    $currentpage = $w->sessionOrRequest('currentpage', 1);

    if ($paginated) {
        $w->ctx('currentpage', $params['currentpage']);
        $w->ctx('numpages', $params['numpages']);
        $w->ctx('pagesize', $params['pagesize']);
        $w->ctx('totalresults', $params['totalresults']);
    }
}
