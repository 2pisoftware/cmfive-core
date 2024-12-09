import { expect, test } from "@playwright/test";
import { CmfiveHelper, GLOBAL_TIMEOUT, HOST } from "@utils/cmfive";
import { TagHelper } from "@utils/tag";
import { TaskHelper } from "@utils/task";

const tagNameSingle = CmfiveHelper.randomID("tag_");
const taskNameSingle = CmfiveHelper.randomID("task_");
const taskGroupName = CmfiveHelper.randomID("taskgroup_");

test.describe.configure({ mode: 'serial' });

// ignore this rule. it's done in the function calls
// eslint-disable-next-line playwright/expect-expect
test("that users can tag objects", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    await CmfiveHelper.login(page, "admin", "admin");

    await TaskHelper.createTaskGroup(page, isMobile, taskGroupName, "To Do", "MEMBER", "MEMBER", "MEMBER");
    const taskId = await TaskHelper.createTask(page, isMobile, taskNameSingle, taskGroupName, "To Do");

    await TagHelper.createTagOnTask(page, tagNameSingle, taskId);
});

test("that users can tag multiple objects with same tag", async ({ page, isMobile }) => {
	test.slow();
    CmfiveHelper.acceptDialog(page);

    const tagNameMultiple = CmfiveHelper.randomID("tag_");

    const taskGroupName = CmfiveHelper.randomID("taskgroup_");
    const taskName1 = CmfiveHelper.randomID("task_");
    const taskName2 = CmfiveHelper.randomID("task_");
    const taskName3 = CmfiveHelper.randomID("task_");
    await CmfiveHelper.login(page, "admin", "admin");

    await TaskHelper.createTaskGroup(page, isMobile, taskGroupName, "To Do", "MEMBER", "MEMBER", "MEMBER");
    const taskId1 = await TaskHelper.createTask(page, isMobile, taskName1, taskGroupName, "To Do");
    const taskId2 = await TaskHelper.createTask(page, isMobile, taskName2, taskGroupName, "To Do");
    const taskId3 = await TaskHelper.createTask(page, isMobile, taskName3, taskGroupName, "To Do");

    await TagHelper.createTagOnTask(page, tagNameMultiple, taskId1);
    await TagHelper.createTagOnTask(page, tagNameMultiple, taskId2);
    await TagHelper.createTagOnTask(page, tagNameMultiple, taskId3);

    await page.goto(HOST + "/task/tasklist?task__assignee-id=unassigned");
    expect(await page.getByText(tagNameMultiple).count()).toBe(3 * 2);	// 6 total because the mobile form

    // Verify we still have our tags
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Tag", "Tag Admin");
    expect(await page.getByText(tagNameSingle).count()).toBe(1 * 2);
    expect(await page.getByText(tagNameMultiple).count()).toBe(1 * 2);

    // Edit a tag name
    const tagName = CmfiveHelper.randomID("tag_");
    await CmfiveHelper.getRowByText(page, tagNameSingle).getByText("Edit").click();
    await page.locator("#tag").fill(tagName);
    await page.getByText("Save").click();

    await expect(page.getByText("Tag saved")).toBeVisible();

    // Verify that the task tagged with our edited tag shows the new tag name
    await page.goto(HOST + "/task/tasklist?task__assignee-id=unassigned");
    await page.waitForLoadState();
    expect(await page.getByText(tagName).count()).toBe(1 * 2);

    // Delete the tags
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Tag", "Tag Admin");
    await CmfiveHelper.getRowByText(page, tagName).getByText("Delete").click();
    await CmfiveHelper.getRowByText(page, tagNameMultiple).getByText("Delete").click();
});
