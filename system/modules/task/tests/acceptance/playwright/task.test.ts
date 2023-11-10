import { expect, test } from "@playwright/test";
import { HOST, GLOBAL_TIMEOUT, CmfiveHelper } from "@utils/cmfive";
import { TaskHelper} from "@utils/task";
import { AdminHelper } from "@utils/admin";

test.describe.configure({mode: 'parallel'});

test("Test that you can manage taskgroups, taskgroup members, and tasks", async ({ page }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    // create user
    const user = CmfiveHelper.randomID("user_")
    await AdminHelper.createUser(page, user, user+"_password", user+"_firstname", user+"_lastname", user+"@example.com", /* ["user", "task_user", "task_group"] */);

    // here we diverge from codeception task test, codeception expects no taskgroups to exist
    // playwright however does not have that luxury due to its parallel nature (timelog tests create taskgroups!)
    // therefore, we cannot* reliably test the case when there are no taskgroups
    // *(if we set up this test to always run before the timelog tests, this condition can be tested)

    // create taskgroup
    const taskgroup = CmfiveHelper.randomID("taskgroup_");
    const taskgroupID = await TaskHelper.createTaskGroup(page, taskgroup, "To Do", "GUEST", "GUEST", "GUEST");

    // add user to/edit taskgroup
    await TaskHelper.addMemberToTaskgroup(page, taskgroup, taskgroupID, user+"_firstname "+user+"_lastname", "MEMBER");
    await TaskHelper.editTaskGroup(page, taskgroup, taskgroupID, {
        "Title": taskgroup+"_edited",
        "Who Can Assign": "MEMBER",
        "Who Can Create": "MEMBER",
        "Who Can View": "MEMBER",
        "Default Assignee": user+"_firstname "+user+"_lastname",
    });

    // create task
    const task = CmfiveHelper.randomID("task_");
    const taskID = await TaskHelper.createTask(page, task, taskgroup, "To Do", {
        "Status": "New",
        "Priority": "Normal",
        "Assigned To": user+"_firstname "+user+"_lastname",
        "Date Due": "26/11/2023",
        "Estimated hours": "10",
        "Effort": "11",
        "Description": "This is a test task",
    });

    // duplicate task
    await page.getByRole("button", {name: "Duplicate Task"}).click();
    await expect(page.getByText("Task duplicated")).toBeVisible();
    const duplicateTaskID = page.url().split("/edit/")[1].split("#")[0];
    await expect(duplicateTaskID).not.toBe(taskID);

    // edit task
    await page.getByRole("combobox", {name: "Status"}).selectOption("Wip");
    await expect(page.getByText("changed")).toBeVisible();
    await page.getByRole("button", {name: "Save"}).click();
    await expect(page.getByRole("combobox", {name: "Status Required"})).toHaveValue("Wip");

    // edit task group member details
    await CmfiveHelper.clickCmfiveNavbar(page, "Task", "Task Groups");
    await page.getByRole("link", {name: taskgroup+"_edited", exact: true}).click();
    await CmfiveHelper.getRowByText(page, user+"_firstname "+user+"_lastname").getByRole("button", {name: "Edit"}).click();
    await page.waitForSelector("#cmfive-modal");
    const modal = await page.locator("#cmfive-modal");

    await modal.getByRole("combobox", {name: "Role"}).selectOption("ALL");
    await modal.getByRole("button", {name: "Update"}).click();
    await expect(await CmfiveHelper.getRowByText(page, user+"_firstname "+user+"_lastname").getByText("ALL")).toBeVisible();

    // delete member from taskgroup
    await CmfiveHelper.getRowByText(page, user+"_firstname "+user+"_lastname").getByRole("button", {name: "Delete"}).click();
    await page.waitForSelector("#cmfive-modal");
    await page.locator("#cmfive-modal").getByRole("button", {name: "Delete"}).click();

    // delete tasks/taskgroup/user
    const tasks = [[task, taskID], [task+" -Copy", duplicateTaskID]];

    for(let [taskName, taskNum] of tasks) {
        await page.goto(HOST + "/task/edit/" + taskNum + "#details");
        await page.waitForTimeout(1_000);

        await expect(page.getByRole("button", {name: "Delete", exact: true}).first()).toBeVisible();
        await page.getByRole("button", {name: "Delete", exact: true}).first().click();

        await expect(page.getByRole("link", {name: taskName, exact: true})).not.toBeVisible();
    }

    await TaskHelper.deleteTaskGroup(page, taskgroup+"_edited", taskgroupID);
    await AdminHelper.deleteUser(page, user);
});