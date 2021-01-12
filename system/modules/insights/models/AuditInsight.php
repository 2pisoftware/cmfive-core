
<?php

class AuditInsight extends InsightBaseClass
{
    public $name = "Audit Insight";
    public $description = "Shows audit information";

    //Displays Filters to select user
    public function getFilters(Web $w): array

    {
        $moduleSelectOptions = $w->db->query("select distinct module as value, module as title from audit order by module asc")->fetchAll();
        $actionSelectOptions = $w->db->query("select distinct concat(module,'/',action) as title, action as value from audit order by title")->fetchAll();
        //var_dump($actionSelectOptions);
        //die;

        return [
            "Options" => [
                [
                    [
                        "Date From (required)", "date", "dt_from", null //$this->_db->sql(a.dt_created >= '{{dt_from}} 00:00:00')
                    ],
                    [
                        "Date To (required)", "date", "dt_to", null //$this->_db->sql(and a.dt_created <= '{{dt_to}} 23:59:59')
                    ],
                ],
                [
                    ["Users (optional)", "select", "user_id", null, AuthService::getInstance($w)->getUsers()],
                ],
                [
                    ["Module (optional)", "select", "module", null, $moduleSelectOptions],
                    ["Action (optional)", "select", "action", null, $actionSelectOptions],
                ]
            ]

        ];
    }
    //     [[dt_from||date||Date From]]

    // [[dt_to||date||Date To]]

    // [[user_id||select||User||select u.id as value, concat(c.firstname,' ',c.lastname) as title from user u, contact c where u.contact_id = c.id order by title]]

    // [[module||select||Module||select distinct module as value, module as title from audit order by module asc]]

    // [[action||select||Action||select distinct action as value, concat(module,'/',action) as title from audit order by title]]


    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array

    {
        $results = [];
        $results[] = new InsightReportInterface('Audit Report', ['Date', 'User', 'Module', 'URL', 'Class', 'Action', 'DB Id'], [[]]);
        return $results;
    }
}
