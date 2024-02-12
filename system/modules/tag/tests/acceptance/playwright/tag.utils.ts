import { HOST, CmfiveHelper } from "@utils/cmfive";
import { expect, Page } from "@playwright/test";

export class TagHelper {
    static async createTagOnTask(page: Page, tagName: string, taskId: string)
    {
        await page.goto(HOST + "/task/edit/" + taskId);
        await page.getByText("No tags").click();

        await page.locator(`#display_tags_Task_${taskId}-selectized`).focus();
        await page.keyboard.type(tagName);

        // Check to see if the tag already exists
        const existingLocator = page.locator('.selectize-control .option').getByText(tagName);
        if (await existingLocator.isVisible()) {
            await existingLocator.click();
        } else {
            await page.keyboard.press("Enter");
        }

        await page.locator('.close-reveal-modal').click()
        // await page.waitForResponse(HOST + `/tag/ajaxGetTags/Task/${taskId}`)
        await page.waitForTimeout(100);

        await expect(page.locator(`#tag_container_Task_${taskId}`)).toHaveText(tagName)
    }
}