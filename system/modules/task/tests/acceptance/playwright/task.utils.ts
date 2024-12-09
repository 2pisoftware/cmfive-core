import { expect, Page } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";
import { DateTime } from "luxon";

export class TaskHelper  {
    /**
     * returns the taskgroup id
     */
    static async createTaskGroup(
        page: Page,
        isMobile: boolean,
        groupName: string,
        groupType: "To Do" | "Software Development" | "Cmfive Support",
        whoCanAssign:         "GUEST" | "MEMBER" | "OWNER",
        whoCanView:   "ALL" | "GUEST" | "MEMBER" | "OWNER",
        whoCanCreate: "ALL" | "GUEST" | "MEMBER" | "OWNER",
        automaticSubscription: boolean = true
    ): Promise<string> {
        if(page.url() != HOST + "/task-group/viewtaskgrouptypes#dashboard")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task Groups");

        await page.getByRole("link", {name: "New Task Group"}).click();

        await page.getByLabel("Title").fill(groupName);

        await page.getByRole('combobox', {name: "Task Group Type"}).selectOption(groupType);
        switch (groupType) {
        case "To Do":
            await page.locator("#task_type").selectOption("To Do");
            break;
        case "Software Development":
            await page.locator("#task_type").selectOption("Programming Task");
            break;
        case "Cmfive Support":
            await page.locator("#task_type").selectOption("Support Ticket");
            break;
        }

        await page.getByRole("combobox", {name: "Who Can Assign"}).selectOption(whoCanAssign);
        await page.getByRole("combobox", {name: "Who Can View"}).selectOption(whoCanView);
        await page.getByRole("combobox", {name: "Who Can Create"}).selectOption(whoCanCreate);
        await page.locator("#priority").selectOption("Normal");

        const subcheckbox = page.getByRole("checkbox", {name: "Automatic Subscription"});
        if (automaticSubscription)
            await subcheckbox.check();
        else
            await subcheckbox.uncheck();

        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Task Group " + groupName + " added")).toBeVisible();

        return page.url().split("/viewmembergroup/")[1].split("#")[0];
    }

    static async editTaskGroup(
        page: Page,
        isMobile: boolean,
        groupName: string,
        groupID: string,
        edit: Record<string, string | boolean>)
    {
        if(page.url() != HOST + "/task-group/viewmembergroup/" + groupID + "#members") {
            if(page.url() != HOST + "/task-group/viewtaskgrouptypes#dashboard")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task Groups");
            
            await page.getByRole("link", {name: groupName, exact: true}).click();
        }

        await page.getByRole("button", {name: "Edit Task Group"}).click();
        await page.locator('#cmfive-modal').waitFor();
        const modal = page.locator('#cmfive-modal');

        if(edit["Title"] !== undefined) await modal.getByLabel("Title").fill(edit["Title"] as string);

        for(const option of ["Who Can Assign", "Who Can View", "Who Can Create", "Default Task Type", "Default Priority", "Default Assignee"])
           if(edit[option] !== undefined) await modal.getByRole("combobox", {name: option}).selectOption(edit[option] as string);

        if(edit["Automatic Subscription"] !== undefined) await modal.getByLabel("Automatic Subscription").setChecked(edit["Automatic Subscription"] as boolean);

        await modal.getByRole("button", {name: "Update"}).click();
    }

    static async deleteTaskGroup(page: Page, isMobile: boolean, groupName: string, groupID: string)
    {
        if(page.url() != HOST + "/task-group/viewmembergroup/" + groupID + "#members") {
            if(page.url() != HOST + "/task-group/viewtaskgrouptypes#dashboard")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task Groups");
            
            await page.getByRole("link", {name: groupName, exact: true}).click();
        }

        await page.getByRole("button", {name: "Delete Task Group"}).click();
        await page.click('#cmfive-modal button:text-is("Delete")');

        await expect(page.getByText("Task Group " + groupName + " deleted.")).toBeVisible();
    }

    static async addMemberToTaskgroup(page: Page, isMobile: boolean, groupName: string, groupID: string, memberName: string, role: "ALL" | "GUEST" | "MEMBER" | "OWNER")
    {
        if(page.url() != HOST + "/task-group/viewmembergroup/" + groupID + "#members") {
            if(page.url() != HOST + "/task-group/viewtaskgrouptypes#dashboard")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task Groups");
            
            await page.getByRole("link", {name: groupName, exact: true}).click();
        }

        await page.getByRole("button", {name: "Add New Members"}).click();

		await page.locator("#cmfive-modal").waitFor();

        await page.locator("#cmfive-modal #role").selectOption(role);
        await page.locator("#cmfive-modal #member").selectOption(memberName);
        await page.locator("#cmfive-modal").getByRole("button", {name: "Submit"}).click();

        await expect(page.getByText("Task Group updated")).toBeVisible();
    }

    static async setDefaultAssignee(page: Page, isMobile: boolean, groupName: string, groupID: string, assignee: string)
    {
        if(page.url() != HOST + "/task-group/viewmembergroup/" + groupID + "#members") {
            if(page.url() != HOST + "/task-group/viewtaskgrouptypes#dashboard")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task Groups");
            
            await page.getByRole("link", {name: groupName, exact: true}).click();
        }
        
        await page.getByRole("button", {name: "Edit Task Group"}).click();

        await page.getByRole("combobox", {name: "Default Assignee"}).selectOption(assignee);

        await page.getByRole("button", {name: "Update"}).click();

        await expect(page.getByText("Task Group " + groupName + " updated.")).toBeVisible();
    }

    static async createTask(page: Page, isMobile: boolean, task: string, taskgroup: string, taskType: string, data?: Record<string, string>): Promise<string>
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile , "Task", "New Task");
        const promise = page.waitForResponse((res) => res.url().includes("taskAjaxSelectbyTaskGroup"));
        await CmfiveHelper.fillAutoComplete(page, "task_group", taskgroup, taskgroup);
        await promise;
        await page.keyboard.press("Escape");
        await page.screenshot({path: "./tas123k.png"});


        await page.locator("#title").fill(task);
        // await page.locator("#task_type").click();
        await page.locator("#task_type").selectOption(taskType);

        if (data !== undefined) {
            for (const option of ["Priority", "Status", "Assigned To"]) {
                if (data[option] !== undefined) {
                    await page.getByRole("combobox", {name: option}).selectOption(data[option]);
                }
            }

            for (const option of ["Estimated hours", "Effort"]) {
                if (data[option] !== undefined) {
                    await page.getByLabel(option).fill(data[option]);
                }
            }

            if (data["Description"] !== undefined) {
				await page.locator("#quill_description").click();
				await page.keyboard.type(data["Description"]);
			}
        }

        // NOTE: this previously checked if a jquery datepicker worked
		// now however, the page uses the built in browser date picker
        await page.getByLabel("Date Due").fill(DateTime.now().set({day: 1}).toFormat("yyyy-MM-dd"))

        await page.getByRole("button", {name: "Save"}).click();
        await page.waitForURL(HOST + "/task/edit/*");

        // return task ID
        return page.url().split("/edit/")[1].split("#")[0];
    }

    static async deleteTask(page: Page, isMobile: boolean, taskName: string, taskID: string)
    {
        // await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Task", "Task List");
        // await page.getByRole("link", {name: taskName, exact: true}).click();

		await page.goto(`${HOST}/task/edit/${taskID}`)
     
        await page.getByRole("button", {name: "Delete", exact: true}).first().click();
        await expect(page.getByRole("link", {name: taskName, exact: true})).toBeHidden();
    }
}