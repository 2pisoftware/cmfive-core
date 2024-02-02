import { HOST, CmfiveHelper } from "@utils/cmfive";
import { expect, Page } from "@playwright/test";
import { DateTime } from "luxon";

export class TimelogHelper  {
    static async createTimelogFromTimer(page: Page, timelog: string, taskName: string, taskID: string, start_time: string = "")
    {
        if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
        }
        
        await page.locator("#start_timer").click();
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

    static async createTimelog(page: Page, timelog: string, taskName: string, taskID: string, date: DateTime, start_time: string, end_time?: string, hours?: string, minutes?: string)
    {
        if (!end_time && !hours && !minutes) {
            throw new Error('Either end_time or hours and minutes must be provided to createTimelog');
        }
        
        if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
        }

        await CmfiveHelper.clickCmfiveNavbar(page, "Timelog", "Add Timelog");
        await page.waitForSelector("#cmfive-modal", {state: "visible"});

        await CmfiveHelper.fillDatePicker(page, "Date Required", "date_start", date);
        await page.locator("#time_start").fill(start_time);
        if (end_time){
            await page.locator("#time_end").fill(end_time);
        } else {
            await page.locator("input[type=radio][name=select_end_method][value=hours]").click();
            await page.locator("#hours_worked").fill(hours);
            await page.locator("#minutes_worked").fill(minutes);
        }
        await page.locator("#timelog_edit_form #description").fill(timelog);
        await page.locator("#timelog_edit_form").getByRole("button", { name: "Save" }).click();

        await page.getByRole("link", {name: taskName, exact: true}).first().click();
        await page.getByRole("link", {name: "Time Log"}).click();
        await page.reload();
        await expect(page.getByText(timelog)).toBeVisible();
    }

    static async editTimelog(page: Page, timelog: string, taskName: string, taskID: string, date: DateTime, start_time: string, end_time?: string, hours?: string, minutes?: string)
    {
        if (!end_time && !hours && !minutes) {
            throw new Error('Either end_time or hours and minutes must be provided to editTimelog');
        }

        if(page.url() == HOST + "/task/edit/" + taskID + "#timelog") page.reload();
        else if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
            await page.getByRole("link", {name: "Time Log"}).click();
        }

        await CmfiveHelper.getRowByText(page, timelog).getByRole("button", {name: "Edit"}).click();
        
        await CmfiveHelper.fillDatePicker(page, "Date Required", "date_start", date);
        await page.locator("#time_start").fill(start_time);

        if (end_time){
            await page.locator("#time_end").fill(end_time);
        } else {
            await page.locator("input[type=radio][name=select_end_method][value=hours]").click();
            await page.locator("#hours_worked").fill(hours);
            await page.locator("#minutes_worked").fill(minutes);
        }
        
        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Timelog saved")).toBeVisible();
    }

    static async deleteTimelog(page: Page, timelog: string, taskName: string, taskID: string)
    {
        if(page.url() == HOST + "/task/edit/" + taskID + "#timelog") page.reload();
        else if(page.url() != HOST + "/task/edit/" + taskID + "#details") {
            if(page.url() != HOST + "/task/tasklist")
                await CmfiveHelper.clickCmfiveNavbar(page, "Task", "Task List");
            
            await page.getByRole("link", {name: taskName, exact: true}).click();
            await page.getByRole("link", {name: "Time Log"}).click();
        }

        await CmfiveHelper.getRowByText(page, timelog).getByRole("button", {name: "Delete"}).click();

        await page.reload();
        await expect(page.getByText(timelog)).not.toBeVisible();
    }
}