
<?php

class AuditInsight extends InsightBaseClass
{
    public $name = "Audit Insight";
    public $description = "Shows audit information";

    //Displays Filters to select user
    public function getFilters(Web $w): array

    {
       $moduleSelectOptions = $this->_db->sql("select distinct module as value, module as title from audit order by module asc")->fetch_all();
       $actionSelectOptions = $this->_db->sql("select distinct action as value, concat(module,'/',action) as title from audit order by title")->fetch_all();

       return [
            ["dt_from", "date", "Date From (required)", date ("d/m/Y") //$this->_db->sql(a.dt_created >= '{{dt_from}} 00:00:00')
            ];
            ["dt_to", "date", "Date To (required)", date ("d/m/Y") //$this->_db->sql(and a.dt_created <= '{{dt_to}} 23:59:59')
            ];
            ["Users", "select", "users (optional)", null, AuthService::getInstance($w)->getUsers()];
            ["module", "select", "Module (optional)", $moduleSelectOptions];
            ["action", "select", "Action (optional)", $actionSelectOptions];
       ]
    }
//     [[dt_from||date||Date From]]

// [[dt_to||date||Date To]]

// [[user_id||select||User||select u.id as value, concat(c.firstname,' ',c.lastname) as title from user u, contact c where u.contact_id = c.id order by title]]

// [[module||select||Module||select distinct module as value, module as title from audit order by module asc]]

// [[action||select||Action||select distinct action as value, concat(module,'/',action) as title from audit order by title]]


    //Displays insights for selected member
    public function run(Web $w, $parameters = []): array

    {
       
    }
}
