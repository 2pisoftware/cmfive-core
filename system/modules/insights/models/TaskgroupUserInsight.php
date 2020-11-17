<?php
class TaskgroupUserInsight extends InsightBaseClass

{
    public $name = "Taskgroup User Insight";
    public $description = "Displays taskgroups associated with a chosen user";

    //Displays Filters to select user
    public function getFilters(Web $w): array

    {
        return ["Select User" =>[
            [
                ["Users", "select", "users", null, AuthService::getInstance($w)->getUsers()]
            ]
        ]];
    }

    //Displays insights for selected member
    public function run(Web $w, $parameters = []): array

    {
        $results = [];
        $results[] = new InsightReportInterface('Test Title', ['column 1', 'column 2'], [['data 1', 'data 2'], ['data 1', 'data 2']]);
        return $results;
    }
}
?>