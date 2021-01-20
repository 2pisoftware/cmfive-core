
<?php

class AuditInsight extends InsightBaseClass
{
    public $name = "Audit Insight";
    public $description = "Shows audit information";

    //Displays Filters to select user
    public function getFilters(Web $w, $parameters = []): array

    {
        $moduleSelectOptions = $w->db->query("select distinct module as value, module as title from audit order by module asc")->fetchAll();
        $actionSelectOptions = $w->db->query("select distinct concat(module,'/',action) as title, action as value from audit order by title")->fetchAll();
        //var_dump($actionSelectOptions);
        //die;

        return [
            "Options" => [
                [
                    [
                        "Date From (required)", "date", "dt_from", array_key_exists('dt_from', $parameters) ? $parameters['dt_from'] : null //$this->_db->sql(a.dt_created >= '{{dt_from}} 00:00:00')
                    ],
                    [
                        "Date To (required)", "date", "dt_to", array_key_exists('dt_to', $parameters) ? $parameters['dt_to'] : null //$this->_db->sql(and a.dt_created <= '{{dt_to}} 23:59:59')
                    ],
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
    //     [[dt_from||date||Date From]]

    // [[dt_to||date||Date To]]

    // [[user_id||select||User||select u.id as value, concat(c.firstname,' ',c.lastname) as title from user u, contact c where u.contact_id = c.id order by title]]

    // [[module||select||Module||select distinct module as value, module as title from audit order by module asc]]

    // [[action||select||Action||select distinct action as value, concat(module,'/',action) as title from audit order by title]]


    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array

    {
        //var_dump($parameters);
        //die;
        $results = [];
        $data = $w->db->query("select 
        a.dt_created as Date, 
        concat(c.firstname,' ',c.lastname) as User,  
        a.module as Module,
        a.path as Url,
        a.db_class as 'Class',
        a.db_action as 'Action',
        a.db_id as 'DB Id'
        
        from audit a
        
        left join user u on u.id = a.creator_id
        left join contact c on c.id = u.contact_id
        
        where 
        a.dt_created >= '" . InsightService::getInstance($w)->date2db($parameters['dt_from']) . " 00:00:00' 
        and a.dt_created <= '" . InsightService::getInstance($w)->date2db($parameters['dt_to']) . " 23:59:59'
        and ('" . $parameters['user_id'] . "' = '' or a.creator_id = '" . $parameters['user_id'] . "') 
        and ('" . $parameters['module'] . "' = '' or a.module = '" . $parameters['module'] . "')
        and ('" . $parameters['action'] . "' = '' or a.action = '" . $parameters['action'] . "') 
        ")->fetchAll(PDO::FETCH_ASSOC);   //sql query goes here
        //var_dump($data);
        //die;
        if (!$data) {
             $results[] = new InsightReportInterface('Audit Report', ['Results'], [['No data returned for selections']]);
         } else {
            $results[] = new InsightReportInterface('Audit Report', ['Date', 'User', 'Module', 'URL', 'Class', 'Action', 'DB Id'], $data);
            //var_dump($results);
         }
        return $results;
    }
}
//select 
// a.dt_created as Date, 
// concat(c.firstname,' ',c.lastname) as User,  
// a.module as Module,
// a.path as Url,
// a.db_class as 'Class',
// a.db_action as 'Action',
// a.db_id as 'DB Id'

// from audit a

// left join user u on u.id = a.creator_id
// left join contact c on c.id = u.contact_id

// where 
// a.dt_created >= '{{dt_from}} 00:00:00' 
// and a.dt_created <= '{{dt_to}} 23:59:59' 
// and ('{{module}}' = '' or a.module = '{{module}}')
// and ('{{action}}' = '' or a.action = '{{action}}') 
// and ('{{user_id}}' = '' or a.creator_id = '{{user_id}}')
