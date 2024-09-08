import { HOST, CmfiveHelper } from "@utils/cmfive";
import { expect, Page } from "@playwright/test";
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
        await page.waitForSelector("#start_time", {state: "visible"});
        await page.waitForSelector("#timerModal", {state: "visible"});
        await page.locator("#start_time").fill(start_time);
        await page.getByLabel("Enter Description", {exact: true}).fill(timelog);
        await page.click('#timerModal button:text-is("Save")');

        await page.waitForSelector("#stop_timer", {state: "visible"});
        await page.locator("#stop_timer").click();

        await page.getByRole("link", {name: "Time Log"}).click();
        await page.reload();
        await expect(page.getByText(timelog)).toBeVisible();
    }

    static async createTimelog(page: Page, isMobile: boolean, timelog: string, taskName: string, taskID: string, date: DateTime, start_time: string, end_time: string, check_duplicate: boolean = false, time_type?: string)
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
        await page.waitForSelector("#cmfive-modal", {state: "visible"});

        await CmfiveHelper.fillDatePicker(page, "Date Required", "date_start", date);
        await page.locator("#time_start").fill(start_time);
        await page.locator("#time_end").fill(end_time);
        await page.getByLabel("Description", {exact: true}).fill(timelog);
        if (time_type) {
            await page.getByRole("combobox", {name: 'Task time' }).selectOption({ value: time_type });
        }
        await page.locator("#timelog_edit_form").getByRole("button", { name: "Save" }).click();

        // if(await page.$("#saved_record_id") != null)
        //     console.log(await page.$eval("#saved_record_id", el => el.innerHTML));

        await page.getByRole("link", {name: taskName, exact: false}).first().click();
        await page.getByRole("link", {name: "Time Log"}).click();
        await page.reload();

        if (check_duplicate)
            await expect(page.getByText(timelog)).toBeHidden();
        else
            await expect(page.getByText(timelog)).toBeVisible();
    }

    static async editTimelog(page: Page, isMobile: boolean, timelog: string, taskName: string, taskID: string, date: DateTime, start_time: string, end_time: string)
    {
        if(page.url() == HOST + "/task/edit/" + taskID + "#timelog") page.reload();
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
        if(page.url() == HOST + "/task/edit/" + taskID + "#timelog") page.reload();
        else if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
            await page.getByRole("link", {name: "Time Log"}).click();
        }

        await CmfiveHelper.getRowByText(page, timelog).getByRole("button", {name: "Delete"}).click();

        await page.reload();
        await expect(page.getByText(timelog)).not.toBeVisible();
    }
}
