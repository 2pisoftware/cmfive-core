import { expect, Page } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";

export class TagHelper {
    static async createTagOnTask(page: Page, tagName: string, taskId: string)
    {
        await page.goto(HOST + "/task/edit/" + taskId);
        await page.getByText("No tags").click();

		CmfiveHelper.fillAutoComplete(page, `display_tags_Task_${taskId}`, tagName, tagName);

		await page.waitForResponse((res) => res.url().includes("/ajaxAddTag/Task/"))

        await page.locator('button[data-bs-dismiss="modal"]').click()
        // await page.waitForResponse(HOST + `/tag/ajaxGetTags/Task/${taskId}`)
        await page.waitForTimeout(100); 

        await expect(page.locator(`.tags-container[data-tag-id="Task_${taskId}"]`)).toContainText(tagName)
    }
}