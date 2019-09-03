<?php namespace System\Modules\Report;

use CmfiveStyleComponent;
use CmfiveStyleComponentRegister;

function showReport(\Web $w, $params = [])
{
    // Params should have omit, output formats?
    // Partial intended to be displayed in modals

    if (empty($params['module']) || empty($params['category'])) {
        return;
    }

    CmfiveStyleComponentRegister::registerComponent('styles', new CmfiveStyleComponent('/system/templates/scss/redesign.scss', ['/system/templates/scss/']));

    $report = $w->Report->getReportByModuleAndCategory($params['module'], $params['category']);
    $form = $report->getReportCriteria(true);

    // Determine if it's a multicolform
    $section_key = array_keys($form);
    if (!empty($section_key[0])) {
        $section_key = $section_key[0];
    } else {
        $section_key = 0;
    }

    $is_multicol_form = !empty($form[$section_key][0]) && is_array($form[$section_key][0]);
    $w->ctx('is_multicol_form', $is_multicol_form);

    if (true || !empty($params['include_export_options']) && $params['include_export_options'] === true) {
        $format_form = [
            [(new \Html\Form\InputField\Radio(['id' => 'pdf', "name" => "format", "label" => "PDF", "checked" => true]))],
            [(new \Html\Form\InputField\Radio(['id' => 'csv', "name" => "format", "label" => "csv"]))]
        ];
        $form["Select format"] = ($is_multicol_form ? [$format_form] : $format_form);
    }

    $w->ctx("form", $form);
}
