import { test } from "@playwright/test";
import { CmfiveHelper, GLOBAL_TIMEOUT } from "@utils/cmfive";
import { TaskHelper } from "@utils/task";
import { TimelogHelper } from "@utils/timelog";
import { DateTime } from "luxon";

test.describe.configure({mode: 'parallel'});

// eslint-disable-next-line playwright/expect-expect
test("You can create a Timelog using Timer" , async ({page, isMobile}) => {
    test.setTimeout(GLOBAL_TIMEOUT * 2);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");
    
    const taskgroup = CmfiveHelper.randomID("taskgroup_");
    const taskgroupID = await TaskHelper.createTaskGroup(page, isMobile, taskgroup, "Software Development", "OWNER", "OWNER", "OWNER");
    await TaskHelper.addMemberToTaskgroup(page, isMobile, taskgroup, taskgroupID, "admin admin", "OWNER");
    await TaskHelper.setDefaultAssignee(page, isMobile, taskgroup, taskgroupID, "admin admin");

    const task = CmfiveHelper.randomID("task_");
    const taskID = await TaskHelper.createTask(page, isMobile, task, taskgroup, "Programming Task");
    
    const timelog = CmfiveHelper.randomID("timelog_");
    await TimelogHelper.createTimelogFromTimer(page, isMobile, timelog, task, taskID);
    
    await TimelogHelper.deleteTimelog(page, isMobile, timelog, task, taskID);
    await TaskHelper.deleteTask(page, isMobile, task, taskID);
    await TaskHelper.deleteTaskGroup(page, isMobile, taskgroup, taskgroupID);
});

// eslint-disable-next-line playwright/expect-expect
test("You can create a Timelog using Add Timelog" , async ({page, isMobile}) => {
    test.setTimeout(GLOBAL_TIMEOUT * 2);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");
    
    const taskgroup = CmfiveHelper.randomID("taskgroup_");
    const taskgroupID = await TaskHelper.createTaskGroup(page, isMobile, taskgroup, "Software Development", "OWNER", "OWNER", "OWNER");
    await TaskHelper.addMemberToTaskgroup(page, isMobile, taskgroup, taskgroupID, "admin admin", "OWNER");
    await TaskHelper.setDefaultAssignee(page, isMobile, taskgroup, taskgroupID, "admin admin");

    const task = CmfiveHelper.randomID("task_");
    const taskID = await TaskHelper.createTask(page, isMobile, task, taskgroup, "Programming Task");

    const timelog = CmfiveHelper.randomID("timelog_");
    await TimelogHelper.createTimelog(
        page,
        isMobile,
        timelog,
        task,
        taskID,
        DateTime.fromFormat("1/1/2021", "d/M/yyyy"),
        "10:00",
        "11:00",
    );
    
    await TimelogHelper.deleteTimelog(page, isMobile, timelog, task, taskID);
    await TaskHelper.deleteTask(page, isMobile, task, taskID);
    await TaskHelper.deleteTaskGroup(page, isMobile, taskgroup, taskgroupID);
});

// test("Test that duplicate timelogs are deleted" , async ({page, isMobile}) => {
//     test.setTimeout(GLOBAL_TIMEOUT);
//     CmfiveHelper.acceptDialog(page);

//     await CmfiveHelper.login(page, "admin", "admin");
    
//     const taskgroup = CmfiveHelper.randomID("taskgroup_");
//     const taskgroupID = await TaskHelper.createTaskGroup(page, isMobile, taskgroup, "Software Development", "OWNER", "OWNER", "OWNER");
//     await TaskHelper.addMemberToTaskgroup(page, isMobile, taskgroup, taskgroupID, "admin admin", "OWNER");
//     await TaskHelper.setDefaultAssignee(page, isMobile, taskgroup, taskgroupID, "admin admin");

//     const task = CmfiveHelper.randomID("task_");
//     const taskID = await TaskHelper.createTask(page, isMobile, task, taskgroup, "Software Development");

//     const timelog1 = CmfiveHelper.randomID("timelog_");
//     await TimelogHelper.createTimelog(
//         page,
//         isMobile,
//         timelog1,
//         task,
//         taskID,
//         DateTime.fromFormat("6/6/2024", "d/M/yyyy"),
//         "1:00",
//         "2:00",
//     );
//     const timelog2 = CmfiveHelper.randomID("timelog_");
//     await TimelogHelper.createTimelog(
//         page,
//         isMobile,
//         timelog2,
//         task,
//         taskID,
//         DateTime.fromFormat("6/6/2024", "d/M/yyyy"),
//         "1:00",
//         "2:00",
//         true
//     );

//     await TimelogHelper.deleteTimelog(page, isMobile, timelog1, task, taskID);
//     await TaskHelper.deleteTask(page, isMobile, task, taskID);
//     await TaskHelper.deleteTaskGroup(page, isMobile, taskgroup, taskgroupID);
// });
