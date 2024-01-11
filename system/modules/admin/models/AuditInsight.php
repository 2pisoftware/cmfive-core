<?php

class AuditInsight extends InsightBaseClass
{
    public $name = "Audit Insight";
    public $description = "Shows audit information";

    //Displays Filters to select user
    public function getFilters(Web $w, $parameters = []): array
    {
        $moduleSelectOptions = AuditService::getInstance($w)->getLoggedModules();
        $actionSelectOptions = AuditService::getInstance($w)->getLoggedActions();

        return [
            "Options" => [
                [
                    (new \Html\Form\InputField\Date([
                        'id|name' => 'dt_from',
                        'value' => array_key_exists('dt_from', $parameters) ? $parameters['dt_from'] : null,
                        'label' => 'Date From',
                        'required' => true,
                    ])),
                    (new \Html\Form\InputField\Date([
                        'id|name' => 'dt_to',
                        'value' => array_key_exists('dt_to', $parameters) ? $parameters['dt_to'] : null,
                        'label' => 'Date To',
                        'required' => true,
                    ])),
                ],
                [
                    ["Users (optional)", "select", "user_id", array_key_exists('user_id', $parameters) ? $parameters['user_id'] : null, AuthService::getInstance($w)->getUsers()],
                ],
                [
                    ["Module (optional)", "select", "module", array_key_exists('module', $parameters) ? $parameters['module'] : null, $moduleSelectOptions],
                    ["Action (optional)", "select", "action", array_key_exists('action', $parameters) ? $parameters['action'] : null, $actionSelectOptions],
                ]
            ]
        ];
    }

    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array
    {
        //below service is referred to as $where in subsequent notes in this block for purpose of examples
        $data = AuditService::getInstance($w)->getAudits(($parameters['dt_from']), ($parameters['dt_to']), ($parameters['user_id']), ($parameters['module']), ($parameters['action']));

        if (!$data) {
             $results[] = new InsightReportInterface('Audit Report', ['Results'], [['No data returned for selections']]);
        } else {
            // convert $data from list of objects to array of values
            $convertedData = [];
            while ($datarow = array_pop($data)) {
            // foreach ($data as $datarow) {
                $row = [];
                $row['Date'] = formatDateTime($datarow['dt_created']);
                $creator = AuthService::getInstance($w)->getUser($datarow['creator_id']);
                $row['User'] = empty($creator) ? '' : $creator->getFullName();
                unset($creator);
                $row['Module'] = $datarow['module'];
                $row['URL'] = $datarow['path'];
                $row['Class'] = $datarow['db_class'];
                $row['Action'] = $datarow['db_action'];
                $row['DB Id'] = $datarow['db_id'];
                $convertedData[] = $row;
            }
            $results[] = new InsightReportInterface('Audit Report', ['Date', 'User', 'Module', 'URL', 'Class', 'Action', 'DB Id'], $convertedData);
        }
        return $results;
    }
}
