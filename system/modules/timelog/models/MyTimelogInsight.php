<?php

/**
 * My Timelog insight - replaces the My Timelog report
 */
class MyTimelogInsight extends InsightBaseClass
{
    public $name = "My Timelog";
    public $description = "Shows your timelog information based on selected criteria";

    private function formatDuration($seconds): string
    {
        return sprintf('%02d:%02d', $seconds / 3600, ($seconds / 60) % 60);
    }

    //Displays Filters to select user
    public function getFilters(Web $w, $parameters = []): array
    {
        $user_id = AuthService::getInstance($w)->user()->id;
        $task_groups = TaskService::getInstance($w)->getTaskGroupsForMember($user_id);
        $time_types = TimelogService::getInstance($w)->getTimeTypesLoggedByUser($user_id);
        
        return [
            "Options" => [
                [
                    (new \Html\Form\Select([
                        'id|name' => 'task_group_id',
                        'value' => array_key_exists('task_group_id', $parameters) ? $parameters['task_group_id'] : null,
                        'label' => 'Task Group',
                        'options' => $task_groups,
                    ])),
                ],
                [
                    (new \Html\Form\InputField\Date([
                        'id|name' => 'dt_from',
                        'value' => array_key_exists('dt_from', $parameters) ? $parameters['dt_from'] : null,
                        'label' => 'Date From',
                    ])),
                    (new \Html\Form\InputField\Date([
                        'id|name' => 'dt_to',
                        'value' => array_key_exists('dt_to', $parameters) ? $parameters['dt_to'] : null,
                        'label' => 'Date To',
                    ]))
                ],
                [
                    (new \Html\Form\Select([
                        'id|name' => 'time_type',
                        'value' => array_key_exists('time_type', $parameters) ? $parameters['time_type'] : null,
                        'label' => 'Time Type',
                        'options' => $time_types,
                    ])),
                ]
            ]
        ];
    }

    public function run(Web $w, $parameters = []): array
    {
        $results = [];

        // Summary
        $timelog_query = $w->db->get('timelog')->where('timelog.user_id', AuthService::getInstance($w)->user()->id)
            ->select()->select("sum(unix_timestamp(timelog.dt_end) - unix_timestamp(timelog.dt_start)) as 'Time'");
        
        if (array_key_exists('task_group', $parameters) && !empty($parameters['task_group'])) {
            $timelog_query->leftJoin('task on task.id = timelog.object_id and timelog.object_class = "Task"')
                ->leftJoin('task_group on task_group.id = task.task_group_id')
                ->where('task.task_group_id', $parameters['task_group'])
                ->where('task.is_deleted', 0)
                ->where('task_group.is_deleted', 0);
        }
        if (array_key_exists('dt_from', $parameters) && !empty($parameters['dt_from'])) {
            $timelog_query->where('timelog.dt_start >= ?', $parameters['dt_from']);
        }
        if (array_key_exists('dt_to', $parameters) && !empty($parameters['dt_to'])) {
            $timelog_query->where('timelog.dt_end <= ?', $parameters['dt_to']);
        }
        if (array_key_exists('time_type', $parameters) && !empty($parameters['time_type'])) {
            $timelog_query->where('timelog.time_type', $parameters['time_type']);
        }
        $timelog_query->where('timelog.is_deleted', 0);
        
        $timelogs = $timelog_query->fetchAll();
        $hours = array_reduce($timelogs, fn ($carry, $t) => $carry + $t['Time']);
        $results[] = new InsightReportInterface('Summary', ['User', 'Hours'], [[AuthService::getInstance($w)->user()->getFullName(), $this->formatDuration($hours)]]);

        //below service is referred to as $where in subsequent notes in this block for purpose of examples
        // $data = AuditService::getInstance($w)->getAudits(($parameters['dt_from']), ($parameters['dt_to']), ($parameters['user_id']), ($parameters['module']), ($parameters['action']));

        // if (!$data) {
        //      $results[] = new InsightReportInterface('Audit Report', ['Results'], [['No data returned for selections']]);
        // } else {
        //     // convert $data from list of objects to array of values
        //     $convertedData = [];
        //     while ($datarow = array_pop($data)) {
        //     // foreach ($data as $datarow) {
        //         $row = [];
        //         $row['Date'] = formatDateTime($datarow['dt_created']);
        //         $creator = AuthService::getInstance($w)->getUser($datarow['creator_id']);
        //         $row['User'] = empty($creator) ? '' : $creator->getFullName();
        //         unset($creator);
        //         $row['Module'] = $datarow['module'];
        //         $row['URL'] = $datarow['path'];
        //         $row['Class'] = $datarow['db_class'];
        //         $row['Action'] = $datarow['db_action'];
        //         $row['DB Id'] = $datarow['db_id'];
        //         $convertedData[] = $row;
        //     }
        //     $results[] = new InsightReportInterface('Audit Report', ['Date', 'User', 'Module', 'URL', 'Class', 'Action', 'DB Id'], $convertedData);
        // }

        return $results;
    }
}
