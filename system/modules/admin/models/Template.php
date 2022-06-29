<?php
class Template extends DbObject
{
    public $title;
    public $description;

    public $category; // eg. Invoice, Quote, Form Letter, Contract, etc.
    public $module; // which module to use this for, eg. crm

    public $is_active;

    public $template_title; // this can be used to automatically generated email subject lines
    public $template_body; // this contains the template body which contains replacement markers
    public $test_title_json; // this can contain test data in JSON format for testing the template
    public $test_body_json; // this can contain test data in JSON format for testing the template

    // standard object stuff
    public $is_deleted;
    public $dt_created;
    public $dt_modified;
    public $creator_id;
    public $modifier_id;

    public function renderTitle($data)
    {
        if (is_array($data)) {
            return TemplateService::getInstance($this->w)->render($this->template_title, $data);
        } else {
            return null;
        }
    }
    public function renderBody($data)
    {
        if (is_array($data)) {
            return TemplateService::getInstance($this->w)->render($this->template_body, $data);
        } else {
            return null;
        }
    }

    public function testTitle()
    {
        return $this->renderTitle(json_decode(defaultVal($this->test_title_json, "[]"), true));
    }

    public function testBody()
    {
        return $this->renderBody(json_decode(defaultVal($this->test_body_json, "[]"), true));
    }
}
