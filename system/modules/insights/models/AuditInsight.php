
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
            
            ["Users", "select", "users", null, AuthService::getInstance($w)->getUsers()]
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
