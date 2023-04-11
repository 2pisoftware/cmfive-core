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

    $report = ReportService::getInstance($w)->getReportByModuleAndCategory($params['module'], $params['category']);
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
            [
                new \Html\Form\InputField\Radio(['id' => 'pdf', "name" => "format", "label" => "PDF", "value" => "pdf", "checked" => true, "class" => ""])
            ],
            [
                new \Html\Form\InputField\Radio(['id' => 'csv', "name" => "format", "label" => "CSV", "value" => "csv", "class" => ""])
            ]
        ];
        $form["Select format"] = $format_form; // ($is_multicol_form ? [$format_form] : $format_form);
    }

    $w->ctx('report', $report);
    $w->ctx("form", $form);
}
