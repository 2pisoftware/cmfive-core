import { test, expect } from "@playwright/test";
import { TimelogHelper } from "@utils/timelog";
import { TaskHelper } from "@utils/task";
import { GLOBAL_TIMEOUT, CmfiveHelper } from "@utils/cmfive";
import { DateTime } from "luxon";

test.describe.configure({mode: 'parallel'});

test.describe('Create Timelog', () => {
    test.beforeEach(async ({page}) => {
        test.setTimeout(GLOBAL_TIMEOUT);
        CmfiveHelper.acceptDialog(page);

        await CmfiveHelper.login(page, "admin", "admin");
    });

    test("using Timer" , async ({page}) => {
        test.setTimeout(GLOBAL_TIMEOUT);
        
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

    test("using Add Timelog, filling the endTime field" , async ({page}) => {
        test.setTimeout(GLOBAL_TIMEOUT);
        
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

    test("using Add Timelog, filling the hours and minutes fields" , async ({page}) => {
        test.setTimeout(GLOBAL_TIMEOUT);
        
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
            null,
            "1",
            "30",
        );
        
        await TimelogHelper.deleteTimelog(page, timelog, task, taskID);
        await TaskHelper.deleteTask(page, task, taskID);
        await TaskHelper.deleteTaskGroup(page, taskgroup, taskgroupID);
    });
});

test.describe('Edit Timelog', () => {
    test.beforeEach(async ({page}) => {
        test.setTimeout(GLOBAL_TIMEOUT);
        CmfiveHelper.acceptDialog(page);

        await CmfiveHelper.login(page, "admin", "admin");
    });

    test("Edit the end time" , async ({page}) => {
        test.setTimeout(GLOBAL_TIMEOUT);
        
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

        await expect(TimelogHelper.editTimelog(
            page,
            timelog,
            task,
            taskID,
            DateTime.fromFormat("1/1/2021", "d/M/yyyy"),
            "12:00",
            "11:00",
        )).toThrowError("End time is before start time");

        await TimelogHelper.editTimelog(
            page,
            timelog,
            task,
            taskID,
            DateTime.fromFormat("1/1/2021", "d/M/yyyy"),
            "11:00",
            "12:00",
        );


        
        await TimelogHelper.deleteTimelog(page, timelog, task, taskID);
        await TaskHelper.deleteTask(page, task, taskID);
        await TaskHelper.deleteTaskGroup(page, taskgroup, taskgroupID);
    });

    test("Edit the elapsed hours and minutes" , async ({page}) => {
            test.setTimeout(GLOBAL_TIMEOUT);
            
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
        
            await TimelogHelper.editTimelog(
                page,
                timelog,
                task,
                taskID,
                DateTime.fromFormat("1/1/2021", "d/M/yyyy"),
                "11:00",
                null,
                "1",
                "30",
            );
            
            await TimelogHelper.deleteTimelog(page, timelog, task, taskID);
            await TaskHelper.deleteTask(page, task, taskID);
            await TaskHelper.deleteTaskGroup(page, taskgroup, taskgroupID);
    });
});