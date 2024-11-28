import { expect, Page } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";

export class TagHelper {
    static async createTagOnTask(page: Page, tagName: string, taskId: string)
    {
        await page.goto(HOST + "/task/edit/" + taskId);
        await page.getByText("No tags").click();

        await CmfiveHelper.fillAutoComplete(page, `display_tags_Task_${taskId}`, tagName, tagName);

        // await page.waitForResponse((res) => res.url().includes("/ajaxAddTag/Task/"));

        page.locator('button[data-bs-dismiss="modal"]').click();
        await page.locator("#cmfive-modal").waitFor({ state: "hidden" });

        // await page.waitForResponse((res) => res.url().includes("tag/ajaxGetTags"));
        await page.waitForTimeout(100);

        await expect(page.locator(`.tags-container[data-tag-id="Task_${taskId}"]`)).toContainText(tagName)
    }
}