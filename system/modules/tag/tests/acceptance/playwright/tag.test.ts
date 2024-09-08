import { expect, test } from "@playwright/test";
import { HOST, GLOBAL_TIMEOUT, CmfiveHelper } from "@utils/cmfive";
import { TagHelper } from "@utils/tag";
import { TaskHelper } from "@utils/task";

const tagNameSingle = CmfiveHelper.randomID("tag_");
const taskNameSingle = CmfiveHelper.randomID("task_");
const taskGroupName = CmfiveHelper.randomID("taskgroup_");

test.describe.configure({ mode: 'serial' });

test("Test that users can tag objects", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    await CmfiveHelper.login(page, "admin", "admin");

    await TaskHelper.createTaskGroup(page, isMobile, taskGroupName, "To Do", "MEMBER", "MEMBER", "MEMBER");
    const taskId = await TaskHelper.createTask(page, isMobile, taskNameSingle, taskGroupName, "To Do");

    await TagHelper.createTagOnTask(page, tagNameSingle, taskId);
});

test("Test that users can tag multiple objects with same tag", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);

    const tagNameMultiple = CmfiveHelper.randomID("tag_");

    CmfiveHelper.acceptDialog(page);
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
    expect(await page.getByText(tagNameMultiple).count()).toBe(3);

    // Verify we still have our tags
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Tag", "Tag Admin");
    expect(await page.getByText(tagNameSingle).count()).toBe(1);
    expect(await page.getByText(tagNameMultiple).count()).toBe(1);

    // Edit a tag name
    const tagName = CmfiveHelper.randomID("tag_");
    await CmfiveHelper.getRowByText(page, tagNameSingle).getByText("Edit").click();
    await page.locator("#tag").fill(tagName);
    await page.getByText("Save").click();

    expect(page.getByText("Tag saved")).toBeVisible();
    await page.getByText("Back to Tag List").click();

    // Verify that the task tagged with our edited tag shows the new tag name
    await page.goto(HOST + "/task/tasklist?task__assignee-id=unassigned");
    await page.waitForTimeout(100);
    expect(await page.getByText(tagName).count()).toBe(1);

    // Delete the tags
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Tag", "Tag Admin");
    await CmfiveHelper.getRowByText(page, tagName).getByText("Delete").click();
    await CmfiveHelper.getRowByText(page, tagNameMultiple).getByText("Delete").click();
});
