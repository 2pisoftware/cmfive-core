import { test } from "@playwright/test";
import { TimelogHelper } from "@utils/timelog";
import { TaskHelper } from "@utils/task";
import { GLOBAL_TIMEOUT, CmfiveHelper } from "@utils/cmfive";
import { DateTime } from "luxon";

test.describe.configure({mode: 'parallel'});

test("You can create a Timelog using Timer" , async ({page}) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");
    
    const taskgroup = CmfiveHelper.randomID("taskgroup_");
    const taskgroupID = await TaskHelper.createTaskGroup(page, taskgroup, "Software Development", "OWNER", "OWNER", "OWNER");
    await TaskHelper.addMemberToTaskgroup(page, taskgroup, taskgroupID, "admin admin", "OWNER");
    await TaskHelper.setDefaultAssignee(page, taskgroup, taskgroupID, "admin admin");

    const task = CmfiveHelper.randomID("task_");
    const taskID = await TaskHelper.createTask(page, task, taskgroup, "Software Development");
    
    const timelog = CmfiveHelper.randomID("timelog_");
    await TimelogHelper.createTimelogFromTimer(page, timelog, task, taskID);
    
    await TimelogHelper.deleteTimelog(page, timelog, task, taskID);
    await TaskHelper.deleteTask(page, task, taskID);
    await TaskHelper.deleteTaskGroup(page, taskgroup, taskgroupID);
});

test("You can create a Timelog using Add Timelog" , async ({page}) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");
    
    const taskgroup = CmfiveHelper.randomID("taskgroup_");
    const taskgroupID = await TaskHelper.createTaskGroup(page, taskgroup, "Software Development", "OWNER", "OWNER", "OWNER");
    await TaskHelper.addMemberToTaskgroup(page, taskgroup, taskgroupID, "admin admin", "OWNER");
    await TaskHelper.setDefaultAssignee(page, taskgroup, taskgroupID, "admin admin");

    const task = CmfiveHelper.randomID("task_");
    const taskID = await TaskHelper.createTask(page, task, taskgroup, "Software Development");

    const timelog = CmfiveHelper.randomID("timelog_");
    await TimelogHelper.createTimelog(
        page,
        timelog,
        task,
        taskID,
        DateTime.fromFormat("1/1/2021", "d/M/yyyy"),
        "10:00",
        "11:00",
    );
    
    await TimelogHelper.deleteTimelog(page, timelog, task, taskID);
    await TaskHelper.deleteTask(page, task, taskID);
    await TaskHelper.deleteTaskGroup(page, taskgroup, taskgroupID);
});

test("Test that duplicate timelogs are deleted" , async ({page}) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");
    
    const taskgroup = CmfiveHelper.randomID("taskgroup_");
    const taskgroupID = await TaskHelper.createTaskGroup(page, taskgroup, "Software Development", "OWNER", "OWNER", "OWNER");
    await TaskHelper.addMemberToTaskgroup(page, taskgroup, taskgroupID, "admin admin", "OWNER");
    await TaskHelper.setDefaultAssignee(page, taskgroup, taskgroupID, "admin admin");

    const task = CmfiveHelper.randomID("task_");
    const taskID = await TaskHelper.createTask(page, task, taskgroup, "Software Development");

    const timelog1 = CmfiveHelper.randomID("timelog_");
    await TimelogHelper.createTimelog(
        page,
        timelog1,
        task,
        taskID,
        DateTime.fromFormat("6/6/2024", "d/M/yyyy"),
        "1:00",
        "2:00",
    );
    const timelog2 = CmfiveHelper.randomID("timelog_");
    await TimelogHelper.createTimelog(
        page,
        timelog2,
        task,
        taskID,
        DateTime.fromFormat("6/6/2024", "d/M/yyyy"),
        "1:00",
        "2:00",
        true,
    );

    await TimelogHelper.deleteTimelog(page, timelog1, task, taskID);
    await TaskHelper.deleteTask(page, task, taskID);
    await TaskHelper.deleteTaskGroup(page, taskgroup, taskgroupID);
});
