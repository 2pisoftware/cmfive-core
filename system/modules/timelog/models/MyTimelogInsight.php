<?php
use function GuzzleHttp\Promise\task;

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

        // Get primary dataset
        $primary_timelog_query = $w->db->get('timelog')->select()
            ->select("timelog.id as 'timelog_id', unix_timestamp(timelog.dt_start) as 'dt_start', unix_timestamp(timelog.dt_end) as 'dt_end', timelog.time_type as 'time_type', task.title as 'task_title', task.id as 'task_id', task_group.title as 'task_group_title', substring(comment.comment, 1, 50) as 'comment'")
            ->leftJoin('task on task.id = timelog.object_id and timelog.object_class = "Task"')
            ->leftJoin('task_group on task_group.id = task.task_group_id')
            ->leftJoin('comment on comment.obj_id = timelog.id and comment.obj_table = "timelog"');

        if (array_key_exists('task_group', $parameters) && !empty($parameters['task_group'])) {
            $primary_timelog_query->where('task.task_group_id', $parameters['task_group']);
        }
        if (array_key_exists('dt_from', $parameters) && !empty($parameters['dt_from'])) {
            $primary_timelog_query->where('timelog.dt_start >= ?', $parameters['dt_from']);
        }
        if (array_key_exists('dt_to', $parameters) && !empty($parameters['dt_to'])) {
            $primary_timelog_query->where('timelog.dt_end <= ?', $parameters['dt_to']);
        }
        if (array_key_exists('time_type', $parameters) && !empty($parameters['time_type'])) {
            $primary_timelog_query->where('timelog.time_type', $parameters['time_type']);
        }
        $primary_timelog_query->where('timelog.is_deleted', 0)
            ->where('task.is_deleted', 0)
            ->where('task_group.is_deleted', 0)
            ->where('timelog.user_id', AuthService::getInstance($w)->user()->id);
        
        $primary_timelogs = $primary_timelog_query->orderBy("dt_start desc")->fetchAll();
        
        $summary = 0;
        $time_type_summary = [];
        $task_group_summary = [];
        $task_summary = [];
        $detailed_timelog = [];
        foreach ($primary_timelogs as $pt) {
            $time_difference = $pt['dt_end'] - $pt['dt_start'];
            if ($time_difference < 0) {
                continue;
            }

            // Add to summary
            $summary += $time_difference;

            // Add to time type summary
            if (!array_key_exists($pt["time_type"], $time_type_summary)) {
                $time_type_summary[$pt['time_type']] = 0;
            }
            $time_type_summary[$pt['time_type']] += $time_difference;

            // Add to task group summary
            if (!array_key_exists($pt["task_group_title"], $task_group_summary)) {
                $task_group_summary[$pt['task_group_title']] = 0;
            }
            $task_group_summary[$pt['task_group_title']] += $time_difference;

            // Add to task summary
            if (!array_key_exists($pt['task_title'], $task_summary)) {
                $task_summary[$pt['task_title']] = [
                    'task_title' => $pt['task_title'],
                    'task_group_title' => $pt['task_group_title'],
                    'time'=> 0,
                ];
            }
            $task_summary[$pt['task_title']]['time'] += $time_difference;

            // Add to detailed timelog
            $detailed_timelog[] = [
                date('Y-m-d H:i:s', $pt['dt_start']),
                $this->formatDuration($time_difference),
                $pt['task_title'],
                $pt['task_group_title'],
                $pt['time_type'],
                $pt['comment'],
            ];
        }

        // Format times and arrays
        $summary = $this->formatDuration($summary);

        // Found a neat trick to convert an associative array to a 2D array
        $time_type_summary = array_map(fn (string $k, int $v) => [$k, $this->formatDuration($v)], array_keys($time_type_summary), array_values($time_type_summary));

        // And a nice shorter way to sort in PHP8+
        usort($time_type_summary, fn ($a, $b) => $a[0] <=> $b[0]);
        
        $task_group_summary = array_map(fn (string $k, int $v) => [$k, $this->formatDuration($v)], array_keys($task_group_summary), array_values($task_group_summary));
        usort($task_group_summary, fn ($a, $b) => $a[0] <=> $b[0]);

        $task_summary = array_map(fn (array $v) => [$v["task_group_title"], $v['task_title'], $this->formatDuration($v['time'])], array_values($task_summary));

        // Summary
        $results[] = new InsightReportInterface('Summary', ['Hours'], [[$summary]]);

        // Time Type Summary
        $results[] = new InsightReportInterface('Time Type Summary', ['Type', 'Hours'], $time_type_summary);

        // Task group summary
        $results[] = new InsightReportInterface('Task Group Summary', ['Task Group', 'Hours'], $task_group_summary);

        // Task summary
        $results[] = new InsightReportInterface('Task Summary', ['Task Group', 'Task', 'Hours'], $task_summary);

        // Detailed timelog
        $results[] = new InsightReportInterface('Detailed Time Log', ['Start Time', 'Hours', 'Task', 'Group', 'Type', 'Comment'], $detailed_timelog);

        return $results;
    }
}
