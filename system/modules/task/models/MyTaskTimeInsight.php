<?php

/**
 * This insight report collects timelogs from any tasks that a user
 * has worked on within the given time period.
 *
 * The logs are presented in 3 views: per day, per taskgroup and showing
 * a detailed view.
 */
class MyTaskTimeInsight extends InsightBaseClass
{
    public $name = "My Task Time";
    public $description = "Shows your timelog spent on tasks and taskgroups.";

    /**
     * returns a string of the format HH:mm for the number of seconds
     */
    private function formatDuration($seconds)
    {
        return sprintf('%02d:%02d', ($seconds / 3600), (($seconds / 60) % 60));
    }

    /**
     * Displays Filters for start date and end date
     */
    public function getFilters(Web $w, $parameters = []): array
    {
        return [
            "Options" => [
                [
                    (new \Html\Form\InputField\Date([
                        'id|name' => 'dt_start',
                        'value' => array_key_exists('dt_start', $parameters) ? $parameters['dt_start'] : null,
                        'label' => 'Date From (optional)',
                    ])),
                    (new \Html\Form\InputField\Date([
                        'id|name' => 'dt_end',
                        'value' => array_key_exists('dt_end', $parameters) ? $parameters['dt_end'] : null,
                        'label' => 'Date To (optional)',
                    ])),
                ],
            ]
        ];
    }

    /**
     * Displays insights for selections made in the above "Options"
     */
    public function run(Web $w, $parameters = []): array
    {
        $timelogs = TimelogService::getInstance($w)->getTimelogsForUserAndClass(
            AuthService::getInstance($w)->user(),
            "Task",
            false,
            $parameters['dt_start'],
            $parameters['dt_end'],
        );

        if (is_null($timelogs)) {
            $results[] = new InsightReportInterface('My Task Timelogs', ['Results'], [['No data returned for selections']]);
        } else {
            // convert $data from list of objects to array of values
            $detailData = [];
            $hoursPerDay = [];
            $hoursPerDayFormatted = [];
            $hoursPerTaskGroup = [];
            $hoursPerTaskGroupFormatted = [];
            foreach ($timelogs as $log) {
                $row = [];
                $task = TaskService::getInstance($w)->getTask($log->object_id);
                $taskgroup = $task->getTaskgroup();
                $row['Date'] = formatDatetime($log->dt_start, "Y-m-d H:i:s");
                $row['Duration'] = $this->formatDuration($log->getDuration());
                $row['Type'] = $log->time_type;
                $row['Description'] = $log->getComment()->comment;

                if (!is_null($task)) {
                    $row['Task'] = $task->title;
                    $row['Taskgroup'] = $taskgroup->title;
                    // add up hours per taskgroup
                    array_key_exists($taskgroup->title, $hoursPerTaskGroup) ?
                        $hoursPerTaskGroup[$taskgroup->title] += $log->getDuration() :
                        $hoursPerTaskGroup[$taskgroup->title] = $log->getDuration();
                } else {
                    $row['Task'] = "ERROR: No Task found for ID (" . $log->object_id . ")";
                    $row['Taskgroup'] = "n/a";
                }
                $detailData[] = $row;
                // add up hours per day
                array_key_exists(formatDatetime($log->dt_start, "Y-m-d"), $hoursPerDay) ?
                    $hoursPerDay[formatDatetime($log->dt_start, "Y-m-d")] += $log->getDuration() :
                    $hoursPerDay[formatDatetime($log->dt_start, "Y-m-d")] = $log->getDuration();
            }
            // reformat the hours per day
            foreach ($hoursPerDay as $day => $duration) {
                $hoursPerDayFormatted[] = ["Date" => $day, "Duration" => $this->formatDuration($duration)];
            }

            // reformat the hours per taskgroup
            foreach ($hoursPerTaskGroup as $taskgroup => $duration) {
                $hoursPerTaskGroupFormatted[] = ["Taskgroup" => $taskgroup, "Duration" => $this->formatDuration($duration)];
            }

            // setting up the three different report views
            $results[] = new InsightReportInterface('Timelog per Day', ['Date', 'Duration (hrs:min)'], $hoursPerDayFormatted);
            $results[] = new InsightReportInterface('Timelog per TaskGroup', ['Taskgroup Name', 'Duration (hrs:min)'], $hoursPerTaskGroupFormatted);
            $results[] = new InsightReportInterface('Timelog Details', ['Date / Time', 'Task', 'Duration (hrs:min)', 'TaskGroup', 'Type', 'Description'], $detailData);
        }
        return $results;
    }
}
