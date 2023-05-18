<?php

namespace Tests\Support\Helper;

class CmfiveTimelogModule extends \Codeception\Module
{
    /**
     * Creates a Timelog from the Timer.
     *
     * @param CmfiveUI $I
     * @param string $task
     * @param string $start_time
     * @return void
     */
    public function createTimelogFromTimer($I, $task, $start_time = '')
    {
        $I->clickCmfiveNavbar($I, 'Task', 'Task List');
        $I->click($task);
        $I->click('Start Timer');
        $I->waitForElement('#start_time');
        $I->waitForElement("#timerModal");
        $I->fillField('#start_time', $start_time);
        $I->click('Save', '#timerModal');
        $I->waitForElementVisible('#stop_timer');
        $I->moveMouseOver('#active_log_time');
        $I->click('#active_log_time');
    }

    /**
     * Creates a Timelog using the edit action.
     *
     * @param CmfiveUI $I
     * @param string $task
     * @param string $date
     * @param string $start_time
     * @param string $end_time
     * @return void
     */
    public function createTimelog($I, $task, $date, $start_time, $end_time)
    {
        $I->clickCmfiveNavbar($I, 'Timelog', 'Add Timelog');
        $I->waitForElementVisible('#cmfive-modal');
        // $I->fillForm(['date:date_start' => strtotime($date)]);
        $I->fillField('#date_start', date('Y-m-d', strtotime($date)));
        $I->fillField('#time_start', $start_time);
        $I->fillField('#time_end', $end_time);
        $I->fillForm(['select:object_class' => "Task"]);
        $tagTask = explode('- ', $task)[1];
        $I->executeJS("$('#acp_search').autocomplete('search', '$tagTask')");
        $I->waitForText($task);
        $I->click($task);
        $I->click('Save');
    }

    /**
     * Edits a Timelog using the edit action.
     *
     * @param CmfiveUI $I
     * @param string $task_name
     * @param string $date
     * @param string $start_time
     * @param string $end_time
     * @return void
     */
    public function editTimelog($I, $task_name, $date, $start_time, $end_time)
    {
        $I->clickCmfiveNavbar($I, "Timelog", "Timelog");
        $I->click($task_name);
        $I->click("Time Log");
        $I->wait(1);
        $I->click("Edit");
        $I->waitForElement("#timelog_edit_form");
        $I->fillField("#date_start", date('Y-m-d', strtotime($date)));
        $I->fillField("#time_start", $start_time);
        $I->fillField("#time_end", $end_time);
        $I->click("Save", "#timelog_edit_form");
    }

    /**
     * Deletes a Timelog using the delete action.
     *
     * @param CmfiveUI $I
     * @param string $task_name
     * @return void
     */
    public function deleteTimelog($I, $task_name)
    {
        $I->clickCmfiveNavbar($I, "Timelog", "Timelog");
        $I->click($task_name);
        $I->click("Time Log");
        $I->click("Delete", "#timelog");
        $I->acceptPopup();
    }
}
