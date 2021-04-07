<?php

class MyTaskTimeInsight extends InsightBaseClass
{
    public $name = "My Task Time";
    public $description = "Shows your timelog spent on tasks and taskgroups.";

    //Displays Filters 
    public function getFilters(Web $w, $parameters = []): array
    {
        return [
            "Options" => [
                [
                    ["Date From (optional)", "date", "dt_start", array_key_exists('dt_start', $parameters) ? $parameters['dt_start'] : null],
                ], 
                [
                    ["Date To (optional)", "date", "dt_end", array_key_exists('dt_end', $parameters) ? $parameters['dt_end'] : null],
                ],
            ]
        ];
    }

    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array
    {
        $timelogs = TimelogService::getInstance($w)->getTimelogsForUserAndClass(
            $w->Auth->user(), 
            "Task", 
            false, 
            $parameters['dt_start'], 
            $parameters['dt_end']);

        if (!is_null($timelogs)) {
            $results[] = new InsightReportInterface('My Task Timelogs', ['Results'], [['No data returned for selections']]);
        } else {
            // convert $data from list of objects to array of values
            $convertedData = [];
            foreach ($timelogs as $log) {
                $row = [];
                $task = TaskService::getInstance($w)->getTask($log->object_id);
                if (!is_null($task)) {
                    $row['Date'] = formatDatetime($log->dt_start, "Y-m-d H:i:s");
                    $row['Task'] = "";
                    $row['Duration'] = $log->getDuration();
                    $row['Taskgroup'] = "";
                    $row['Type'] = $log->time_type;
                    $row['Description'] = $log->description;
                }
                else {
                    $row['Date'] = formatDatetime($log->dt_start, "Y-m-d H:i:s");
                    $row['Task'] = "ERROR: No Task found for ID (".$log->object_id.")";
                    $row['Duration'] = $log->getDuration();
                    $row['Taskgroup'] = "n/a";
                    $row['Type'] = $log->time_type;
                    $row['Description'] = $log->description;

                }
                $convertedData[] = $row;
            }
            $results[] = new InsightReportInterface('Timelog Details', ['Date / Time', 'Task', 'Duration (hrs)', 'TaskGroup' ,'Type', 'Description'], $convertedData);
        }
        return $results;
    }
}
