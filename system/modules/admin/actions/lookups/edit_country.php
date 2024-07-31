<?php

function edit_country_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    
    list($id) = $w->pathMatch("id");

    $country = !empty($id) ? AdminService::getInstance($w)->getCountry($id) : new Country($w);

    $form = [
        (empty($id) ? 'Create' : 'Edit') . " country" => [
            [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'name',
                    'label' => 'Name',
                    'value' => htmlentities($country->name ?? ''),
                    'required' => true,
                ]))
            ], [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'alpha_2_code',
                    'label' => 'Alpha 2 Code',
                    'value' => $country->alpha_2_code ?? '',
                    'required' => true,
                ])),
                (new \Html\Form\InputField\Text([
                    'id|name' => 'alpha_3_code',
                    'label' => 'Alpha 3 Code',
                    'value' => $country->alpha_3_code ?? '',
                    'required' => true,
                ]))
            ], [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'capital',
                    'label' => 'Capital',
                    'value' => $country->capital ?? '',
                ])), 
                (new \Html\Form\InputField\Text([
                    'id|name' => 'region',
                    'label' => 'Region',
                    'value' => $country->region ?? '',
                ]))
            ], [
                (new \Html\Form\InputField\Text([
                    'id|name' => 'subregion',
                    'label' => 'Subregion',
                    'value' => $country->subregion ?? '',
                ])),
                (new \Html\Form\InputField\Text([
                    'id|name' => 'demonym',
                    'label' => 'Demonym',
                    'value' => htmlentities($country->demonym ?? ''),
                ]))
        ]
    ]];

    $w->out(HtmlBootstrap5::multiColForm($form, '/admin-lookups/edit_country/' . (!empty($id) ? $id : ''), 'POST', 'Save'));
}

function edit_country_POST(Web $w)
{
    list($id) = $w->pathMatch("id");

    $country = !empty($id) ? AdminService::getInstance($w)->getCountry($id) : new Country($w);
    $country->fill($_POST);
    $country->insertOrUpdate();

    $w->msg('Country ' . (!empty($country->id) ? 'updated' : 'created'), "/admin-lookups/index#countries");
}
