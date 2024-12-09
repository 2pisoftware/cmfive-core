import { expect, Page } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";
import { DateTime } from "luxon";

export class TimelogHelper  {
    static async createTimelogFromTimer(page: Page, isMobile: boolean, timelog: string, taskName: string, taskID: string, start_time: string = "")
    {
        if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
        }

        if(isMobile)
            await page.getByRole("link", {name: "Menu"}).click();

        await page.getByText("Start Timer", {exact: true}).click();
        await page.locator("#start_time").waitFor({state: "visible"});
        await page.locator("#timelog_starttimer_modal").waitFor({state: "visible"});
        await page.locator("#start_time").fill(start_time);

		await page.locator("#timelog_starttimer_modal #quill_Description").click();
		await page.keyboard.type(timelog);

        await page.click('#timelog_starttimer_modal button:text-is("Save")');

        await page.locator("#timelog_widget_stop").waitFor({state: "visible"});
        await page.locator("#timelog_widget_stop").click();

        await page.getByRole("link", {name: "Time Log"}).click();
        await page.reload();
		await page.locator("#timelog").waitFor();
        await expect(page.getByText(timelog).first()).toBeVisible();
    }

    static async createTimelog(page: Page, isMobile: boolean, timelog: string, taskName: string, taskID: string, date: DateTime, start_time: string, end_time: string, check_duplicate: boolean = false)
    {
        if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
        }

        // manually adding navigation due to Add timelog not being a link
        if (isMobile)
            await page.getByRole("link", {name: "Menu"}).click();

        const navbarCategory = page.locator("#topnav_timelog");
        const bootstrap5 = await CmfiveHelper.isBootstrap5(page);
        if (bootstrap5 || isMobile) {
            await navbarCategory.click();
        } else { // Foundation
            await navbarCategory.hover();
        }

        await navbarCategory.getByText('Add Timelog').click();
        await page.locator("#cmfive-modal").waitFor({ state: "visible" });

		const modal = page.locator("#cmfive-modal");

        await CmfiveHelper.fillDatePicker(page, "Date Required", "date_start", date);
        await modal.locator("#time_start").fill(start_time);
        await modal.locator("#time_end").fill(end_time);
        
		await modal.locator("#quill_description").click();
		await page.keyboard.type(timelog);

        await modal.getByRole("button", { name: "Submit" }).click();

        // if(await page.$("#saved_record_id") != null)
        //     console.log(await page.$eval("#saved_record_id", el => el.innerHTML));

        await page.getByRole("link", {name: taskName, exact: false}).first().click();
        await page.getByRole("link", {name: "Time Log"}).click();
        await page.reload();

        if (check_duplicate)
            await expect(page.getByText(timelog).first()).toBeHidden();
        else
            await expect(page.getByText(timelog).first()).toBeVisible();
    }

    static async editTimelog(page: Page, isMobile: boolean, timelog: string, taskName: string, taskID: string, date: DateTime, start_time: string, end_time: string)
    {
        if(page.url() == HOST + "/task/edit/" + taskID + "#timelog") await page.reload();
        else if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
            await page.getByRole("link", {name: "Time Log"}).click();
        }

        await CmfiveHelper.getRowByText(page, timelog).getByRole("button", {name: "Edit"}).click();
        
        await CmfiveHelper.fillDatePicker(page, "Date Required", "date_start", date);
        await page.locator("#time_start").fill(start_time);
        await page.locator("#time_end").fill(end_time);
        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Timelog saved")).toBeVisible();
    }

    static async deleteTimelog(page: Page, isMobile: boolean, timelog: string, taskName: string, taskID: string)
    {
        if(page.url() == HOST + "/task/edit/" + taskID + "#timelog") await page.reload();
        else if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
            await page.getByRole("link", {name: "Time Log"}).click();
        }

        await CmfiveHelper.getRowByText(page, timelog).getByRole("button", {name: "Delete"}).click();

        await page.reload();
        await expect(page.getByText(timelog)).toBeHidden();
    }
}
