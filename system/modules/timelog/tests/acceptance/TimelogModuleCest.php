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
        $uniqid = uniqid();
        $user_first_name = "timelog_first_name_$uniqid";
        $user_last_name = "timelog_last_name_$uniqid";
        $task_group_name = "timelog_taskgroup_$uniqid";
        $task_name = "timelog_task_$uniqid";

        $I->wantTo("Verify that Timelogs can be created, edited and deleted");
        $I->login($I, "admin", "admin");
        $I->createUser($I, "timelog_user_$uniqid", "password", $user_first_name, $user_last_name, "timelog_user@cmfive.com");

        $I->createTaskGroup($I, $task_group_name, [
            "task_group_type" => "To Do",
            "can_assign" => "GUEST",
            "can_view" => "GUEST",
            "can_create" => "GUEST",
            "is_active" => "Yes",
            "description" => "A test group",
        ]);
        $I->addMemberToTaskGroup($I, $task_group_name, "$user_first_name $user_last_name", "MEMBER");
        $task_count = $I->createTask($I, $task_group_name, $task_name, [
            "task_type" => "To Do",
            "status" => "New",
            "priority" => "Normal",
            "dt_due" => "1988-05-27",
            "assignee_id" => "$user_first_name $user_last_name",
            "estimate_hours" => 10,
            "effort" => 11,
            "description" => "a test task",
        ]);

        $I->createTimelogFromTimer($I, $task_name);
        $I->editTimelog($I, $task_name, "+1 day", "09:30", "17:30");
        $I->deleteTimelog($I, $task_name);

        $I->createTimelog($I, "$task_count - $task_name", "now", "09:00", "17:00");
        $I->editTimelog($I, $task_name, "+1 day", "09:30", "17:30");
        $I->deleteTimelog($I, $task_name);
    }
}
