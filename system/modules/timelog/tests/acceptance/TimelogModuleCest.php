<?php

class TimelogModuleCest
{
    /**
     * Tests the Timelog module.
     *
     * @param CmfiveUI $I
     * @return void
     */
    public function testTimelogModule($I)
    {
        $user_first_name = "timelog_first_name";
        $user_last_name = "timelog_last_name";
        $task_group_name = "timelog_taskgroup";
        $task_name = "timelog_task";

        $I->wantTo("Verify that timelogs can be created");
        $I->login($I, "admin", "admin");
        $I->createUser($I, "timelog_user", "password", $user_first_name, $user_last_name, "timelog_user@cmfive.com");

        $I->createTaskGroup($I, $task_group_name, [
            "task_group_type" => "To Do",
            "can_assign" => "GUEST",
            "can_view" => "GUEST",
            "can_create" => "GUEST",
            "is_active" => "Yes",
            "description" => "A test group",
        ]);
        $I->addMemberToTaskGroup($I, $task_group_name, "$user_first_name $user_last_name", "MEMBER");
        $I->createTask($I, $task_group_name, $task_name, [
            "task_type" => "To Do",
            "status" => "New",
            "priority" => "Normal",
            "dt_due" => strtotime("27/05/1988"),
            "assignee_id" => "$user_first_name $user_last_name",
            "estimate_hours" => 10,
            "effort" => 11,
            "description" => "a test task",
        ]);

        // $I->clickCmfiveNavbar($I, "Timelog", "Add Timelog");
        // $I->waitForElement("#cmfive-modal");
        // $I->waitForElement(("#timelog_edit_form"));
        $I->createTimelogFromTimer($I, $task_name);
    }
}
