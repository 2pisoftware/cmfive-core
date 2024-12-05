import { expect, Page } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";

export class TagHelper {
    static async createTagOnTask(page: Page, tagName: string, taskId: string)
    {
        await page.goto(HOST + "/task/edit/" + taskId);
        await page.getByText("No tags").click();

        let promise = page.waitForResponse((res) => res.url().includes("tag/ajaxAddTag"));

        await CmfiveHelper.fillAutoComplete(page, `display_tags_Task_${taskId}`, tagName, tagName);

        await promise;

        // await page.waitForResponse((res) => res.url().includes("/ajaxAddTag/Task/"));

        promise = page.waitForResponse((res) => res.url().includes("tag/ajaxGetTags"));

        await page.locator('button[data-bs-dismiss="modal"]').click();
        await page.locator("#cmfive-modal").waitFor({ state: "hidden" });

        await promise;

        await expect(page.locator(`.tags-container[data-tag-id="Task_${taskId}"]`)).toContainText(tagName)
    }
}