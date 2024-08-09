<?php
use function GuzzleHttp\Promise\task;

/**
 * My Timelog insight - replaces the My Timelog report
 */
class TaskTimelogInsight extends InsightBaseClass
{
    public $name = "Task Time Log";
    public $description = "Shows task timelog information based on selected criteria";

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
                    (new \Html\Form\Select([
                        'id|name' => 'user_id',
                        'value' => array_key_exists('user_id', $parameters) ? $parameters['user_id'] : null,
                        'label' => 'User',
                        'options' => AuthService::getInstance($w)->getUsers(),
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
            ->select("
                concat(contact.firstname, ' ', contact.lastname) as 'name',
                timelog.id as 'timelog_id',
                unix_timestamp(timelog.dt_start) as 'dt_start',
                unix_timestamp(timelog.dt_end) as 'dt_end',
                timelog.time_type as 'time_type',
                task.title as 'task_title',
                task.id as 'task_id',
                task_group.title as 'task_group_title',
                substring(comment.comment, 1, 50) as 'comment',
                user.id as 'user_id'")
            ->leftJoin('task on task.id = timelog.object_id and timelog.object_class = "Task"')
            ->leftJoin('task_group on task_group.id = task.task_group_id')
            ->leftJoin('comment on comment.obj_id = timelog.id and comment.obj_table = "timelog"')
            ->leftJoin('user on user.id = timelog.user_id')
            ->leftJoin('contact on contact.id = user.contact_id');

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
            ->where('task_group.is_deleted', 0);

        if (!AuthService::getInstance($w)->user()->hasRole("insights_admin")) {
            $primary_timelog_query->leftJoin('task_group_member on task_group_member.task_group_id = task_group.id')
                ->where('(task_group.can_view = "OWNER" and task_group_member.role = "OWNER")
                    or (task_group.can_view = "MEMBER" and task_group_member.role in ("OWNER", "MEMBER"))
                    or (task_group.can_view = "GUEST" and task_group_member.role in ("OWNER", "MEMBER", "GUEST"))
                    or task.assignee_id = ?', AuthService::getInstance($w)->user()->id);
        }
        
        $primary_timelogs = $primary_timelog_query->orderBy("dt_start asc")->fetchAll();

        $user_summary = [];
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
            if (!array_key_exists($pt['user_id'], $user_summary)) {
                $user_summary[$pt['user_id']] = [
                    "name" => $pt['name'],
                    "time" => 0,
                ];
            }
            $user_summary[$pt['user_id']]['time'] += $time_difference;

            // Add to time type summary
            if (!array_key_exists($pt["user_id"], $time_type_summary)) {
                $time_type_summary[$pt['user_id']] = [];
            }
            if (!array_key_exists($pt["time_type"], $time_type_summary[$pt['user_id']])) {
                $time_type_summary[$pt['user_id']][$pt["time_type"]] = [
                    'name' => $pt['name'],
                    'time' => 0,
                ];
            }
           
            $time_type_summary[$pt['user_id']][$pt['time_type']]['time'] += $time_difference;

            // Add to task group summary
            if (!array_key_exists($pt["task_group_title"], $task_group_summary)) {
                $task_group_summary[$pt['task_group_title']] = 0;
            }
            $task_group_summary[$pt['task_group_title']] += $time_difference;

            // Add to task summary
            if (!array_key_exists($pt['task_title'], $task_summary)) {
                $task_summary[$pt['task_title']] = [
                    'task_link' => HtmlBootstrap5::a(href: $w->localUrl("/task/edit/" . $pt['task_id']), title: $pt['task_title'], target: "_blank"),
                    'task_title' => $pt['task_title'],
                    'task_group_title' => $pt['task_group_title'],
                    'time'=> 0,
                ];
            }
            $task_summary[$pt['task_title']]['time'] += $time_difference;

            // Add to detailed timelog
            $detailed_timelog[] = [
                $pt['name'],
                date('Y-m-d H:i:s', $pt['dt_start']),
                $this->formatDuration($time_difference),
                HtmlBootstrap5::a(href: $w->localUrl("/task/edit/" . $pt['task_id']), title: $pt['task_title'], target: "_blank"),
                $pt['task_group_title'],
                $pt['time_type'],
                $pt['comment'],
            ];
        }

        // Format times and arrays
        $user_summary = array_map(fn (array $v) => [$v['name'], $this->formatDuration($v['time'])], array_values($user_summary));

        // Found a neat trick to convert an associative array to a 2D array
        // This one is a bit more complicated than the rest as it is tallying time for both user and time type - not sure why the values are being put inside an array at position 0 though
        $time_type_summary = array_map(fn (string $k, array $v) => [$v['name'], $k, $this->formatDuration($v['time'])], array_keys(array_values($time_type_summary)[0]), array_values(array_values($time_type_summary)[0]));
        
        // And a nice shorter way to sort in PHP7.4+
        // This is checking if the user names are the same, if they are it'll sort by time type, otherwise it'll sort by user name
        // @see {https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.spaceship-op}
        // @see {https://www.php.net/manual/en/functions.arrow.php}
        usort($time_type_summary, fn ($a, $b) => $a[0] === $b[0] ? $a[1] <=> $b[1] : $a[0] <=> $b[0]);
        
        $task_group_summary = array_map(fn (string $k, int $v) => [$k, $this->formatDuration($v)], array_keys($task_group_summary), array_values($task_group_summary));
        usort($task_group_summary, fn ($a, $b) => $a[0] <=> $b[0]);

        $task_summary = array_map(fn (array $v) => [$v["task_group_title"], $v['task_link'], $this->formatDuration($v['time'])], array_values($task_summary));

        // Summary
        $results[] = new InsightReportInterface('User Summary', ['User', 'Hours'], $user_summary);

        // Time Type Summary
        $results[] = new InsightReportInterface('Time Type Summary', ['User', 'Type', 'Hours'], $time_type_summary);

        // Task group summary
        $results[] = new InsightReportInterface('Task Group Summary', ['Task Group', 'Hours'], $task_group_summary);

        // Task summary
        $results[] = new InsightReportInterface('Task Summary', ['Task Group', 'Task', 'Hours'], $task_summary);

        // Detailed timelog
        $results[] = new InsightReportInterface('Detailed Time Log', ['User', 'Start Time', 'Hours', 'Task', 'Group', 'Type', 'Comment'], $detailed_timelog);

        return $results;
    }
}
