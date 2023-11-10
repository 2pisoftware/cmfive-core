import { Page, expect } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";

export class ChannelsHelper {
    static async createWebChannel(page: Page, channel: Record<string, string | boolean>)
    {
        await expect(typeof channel["Name"] == "string" && channel["Name"] != "").toBeTruthy();

        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await page.getByRole("button", { name: "Add Web Channel" }).click();
        await page.waitForURL(HOST + "/channel-web/edit", {waitUntil: "load"});

        for(let option of ["Name", "Web API URL"])
            if(channel[option] !== undefined) await page.getByLabel(option, {exact: true}).fill(channel[option] as string);

        for(let option of ["Is Active", "Run processors?"])
            if(channel[option] !== undefined) {
                if(channel[option] === true) await page.getByLabel(option, {exact: true}).check();
                else if(channel[option] === false) await page.getByLabel(option, {exact: true}).uncheck();
            }

        await page.getByRole("button", {name: "Save"}).click();
    }

    static async editWebChannel(page: Page, channel: Record<string, string | boolean>, edit: Record<string, string | boolean>): Promise<Record<string, string | boolean>>
    {
        await expect(edit["Name"] === undefined || (typeof edit["Name"] == "string" && edit["Name"] != "")).toBeTruthy();

        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        for(let option of ["Name", "Web API URL"])
            if(edit[option] !== undefined) await modal.getByLabel(option, {exact: true}).fill(edit[option] as string);
        
        for(let option of ["Is Active", "Run processors?"])
            if(edit[option] !== undefined) {
                if(edit[option] === true) await modal.getByLabel(option, {exact: true}).check();
                else if(edit[option] === false) await modal.getByLabel(option, {exact: true}).uncheck();
            }
        
        await modal.getByRole("button", {name: "Save"}).click();

        let editedChannel: Record<string, string | boolean> = {};
        for(let option of ["Name", "Web API URL", "Is Active", "Run processors?"])
            if(edit[option] !== undefined) editedChannel[option] = edit[option];
            else editedChannel[option] = channel[option];
        
        return editedChannel;
    }

    static async verifyWebChannel(page: Page, channel: Record<string, string | boolean>)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        for(let option of ["Name", "Web API URL"])
            if(channel[option] !== undefined) await expect(modal.getByLabel(option, {exact: true})).toHaveValue(channel[option] as string);
            else await expect(modal.getByLabel(option, {exact: true})).toHaveValue("");

        for(let option of ["Is Active", "Run processors?"])
            if(channel[option] !== undefined) {
                if(channel[option] === true) await expect(await modal.getByLabel(option, {exact: true}).isChecked()).toBeTruthy();
                else if(channel[option] === false) await expect(await modal.getByLabel(option, {exact: true}).isChecked()).toBeFalsy();
            } // web channel defaults to true for both Is Active and Run Processors? if not specified
            else await expect(modal.getByLabel(option, {exact: true}).isChecked()).toBeTruthy();

        await modal.getByRole("button", {name: "Cancel"}).click();
    }

    static async createEmailChannel(page: Page, channel: Record<string, string | boolean>)
    {
        for(let required of ["Name", "Protocol", "Server URL", "Username", "Password"])
            await expect(typeof channel[required] == "string" && channel[required] != "").toBeTruthy();

        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await page.getByRole("button", { name: "Add Email Channel" }).click();
        console.log(page.url())
        await page.waitForURL(HOST + "/channel-email/edit", {waitUntil: "load"});

        await page.getByRole("combobox", {name: "Protocol"}).selectOption(channel["Protocol"] as string);

        if(channel["Post Read Action"] !== undefined)
            await page.getByRole("combobox", {name: "Post Read Action"}).selectOption(channel["Post Read Action"] as string);

        if(channel["Port"] !== undefined) await page.getByLabel("Port Only required for non-standard port configuarations", {exact: true}).fill(channel["Port"] as string);

        for(let option of ["Name", "Server URL", "Username", "Password"])
            await page.getByLabel(option + " Required", {exact: true}).fill(channel[option] as string);

        for(let option of ["Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data"])
            if(channel[option] !== undefined) await page.getByLabel(option, {exact: true}).fill(channel[option] as string);

        for(let option of ["Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(channel[option] !== undefined) {
                if(channel[option] === true) await page.getByLabel(option, {exact: true}).check();
                else if(channel[option] === false) await page.getByLabel(option, {exact: true}).uncheck();
            }

        await page.getByRole("button", {name: "Save"}).click();
    }

    static async editEmailChannel(page: Page, channel: Record<string, string | boolean>, edit: Record<string, string | boolean>)
    {
        for(let required of ["Name", "Protocol", "Server URL", "Username", "Password"])
            await expect(edit[required] === undefined || (typeof edit[required] == "string" && edit[required] != "")).toBeTruthy();

        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        if(edit["Port"] !== undefined) await modal.getByLabel("Port Only required for non-standard port configuarations", {exact: true}).fill(edit["Port"] as string);

        for(let option of ["Protocol", "Post Read Action"])
            if(edit[option] !== undefined) await modal.getByRole("combobox", {name: option}).selectOption(edit[option] as string);

        for(let option of ["Name", "Server URL", "Username", "Password"])
            if(edit[option] !== undefined) await modal.getByLabel(option + " Required", {exact: true}).fill(edit[option] as string);

        for(let option of ["Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data"])
            if(edit[option] !== undefined) await modal.getByLabel(option, {exact: true}).fill(edit[option] as string);

        for(let option of ["Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(edit[option] !== undefined)
                if(edit[option] === true) await modal.getByLabel(option, {exact: true}).check();
                else if(edit[option] === false) await modal.getByLabel(option, {exact: true}).uncheck();

        await modal.getByRole("button", {name: "Save"}).click();

        let editedChannel: Record<string, string | boolean> = {};
        for(let option of ["Name", "Protocol", "Server URL", "Username", "Password", "Port", "Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data", "Post Read Action", "Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(edit[option] !== undefined) editedChannel[option] = edit[option];
            else if(channel[option] !== undefined) editedChannel[option] = channel[option];
        
        return editedChannel;
    }

    static async verifyEmailChannel(page: Page, channel: Record<string, string | boolean>)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        await expect(modal.getByRole("combobox", {name: "Protocol"}).locator('option[selected="true"]')).toHaveValue(channel["Protocol"] as string);

        if(channel["Post Read Action"] !== undefined) await expect(modal.getByRole("combobox", {name: "Post Read Action"}).locator('option[selected="true"]')).toHaveValue(channel["Post Read Action"] as string);
        else await expect(await modal.getByRole("combobox", {name: "Post Read Action"}).locator('option[selected="true"]').count()).toBe(0);

        if(channel["Port"] !== undefined) await expect(modal.getByLabel("Port Only required for non-standard port configuarations", {exact: true})).toHaveValue(channel["Port"] as string);
        else await expect(modal.getByLabel("Port Only required for non-standard port configuarations", {exact: true})).toHaveValue("0");

        for(let option of ["Name", "Server URL", "Username", "Password"])
            await expect(modal.getByLabel(option + " Required", {exact: true})).toHaveValue(channel[option] as string);

        for(let option of ["Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data"])
            if(channel[option] !== undefined) await expect(modal.getByLabel(option, {exact: true})).toHaveValue(channel[option] as string);
            else await expect(modal.getByLabel(option, {exact: true})).toHaveValue("");

        for(let option of ["Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(channel[option] !== undefined) {
                if(channel[option] === true) await expect(await modal.getByLabel(option, {exact: true}).isChecked()).toBeTruthy();
                else if(channel[option] === false) await expect(await modal.getByLabel(option, {exact: true}).isChecked()).toBeFalsy();
            }
        
        for(let option of ["Is Active", "Run processors?"])
            if(channel[option] === undefined) await expect(modal.getByLabel(option, { exact: true }).isChecked()).toBeTruthy();

        await modal.getByRole("button", {name: "Cancel"}).click();
    }

    static async deleteChannel(page: Page, channel: Record<string, string | boolean>)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Delete"}).click();
        await expect(page.getByText("Channel deleted")).toBeVisible();
    }

    static async createProcessor(page: Page, processorName: string, channel: string, processorClass: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Processors");
        await page.getByRole("button", {name: "Add Processor"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        await modal.getByLabel("Name Required", {exact: true}).fill(processorName);
        await modal.getByRole("combobox", {name: "Channel Required"}).selectOption(channel);
        await modal.getByRole("combobox", {name: "Processor Class Required"}).selectOption(processorClass);

        await modal.getByRole("button", {name: "Save"}).click();
    }

    static async verifyProcessor(page: Page, processorName: string, channel: string, processorClass: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Processors");
        await CmfiveHelper.getRowByText(page, processorName).getByRole("button", {name: "Edit"}).click(); // if this doesn't fail, name is verified as correct
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        await expect(modal.getByRole("combobox", {name: "Channel Required"}).locator('option[selected="true"]')).toHaveText(channel);
        await expect(modal.getByRole("combobox", {name: "Processor Class Required" }).locator('option[selected="true"]')).toHaveText(processorClass);

        await modal.getByRole("button", {name: "Cancel"}).click();
    }

    static async deleteProcessor(page: Page, processorName: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Processors");
        await CmfiveHelper.getRowByText(page, processorName).getByRole("button", {name: "Delete"}).click();
        page.reload();
    }
}