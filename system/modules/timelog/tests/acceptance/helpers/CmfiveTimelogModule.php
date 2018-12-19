<?php
namespace Helper;

class CmfiveTimelogModule extends \Codeception\Module
{
	public function createTimelogFromTimer($I, $task, $start_time = '') {
		$I->clickCmfiveNavbar($I, 'Task', 'Task List');
		// $row = $I->findTableRowMatching($I, 2, $task);
		$I->click($task);
		$I->click('Start Timer');
		$I->waitForElement('#start_time');
		$I->fillField('#start_time', $start_time);
		$I->click('Save', '#timerModal');
		$I->waitForElementVisible('#stop_timer');
		$I->moveMouseOver('#active_log_time');
		$I->click('#active_log_time');
	}

	public function createTimelog($I, $task, $date, $start_time, $end_time) {
		$I->clickCmfiveNavbar($I, 'Timelog', 'Add Timelog');
		$I->waitForElementVisible('#cmfive-modal');
		$I->fillForm(['date:date_start' => strtotime($date)]);
		$I->fillField('#time_start', $start_time);
		$I->fillField('#time_end', $end_time);
		$I->executeJS("$('#acp_search').autocomplete('search', '{$task}')");
		$I->waitForText($task);
        $I->click($task);
		$I->click('Save');
	}
}