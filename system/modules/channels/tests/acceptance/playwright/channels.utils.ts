import { Locator, Page, expect } from "@playwright/test";
import { CmfiveHelper, HOST } from "@utils/cmfive";

const webChannelFormLabelToId = {
    "Name": "#name",
    "Web API URL": "#url",
    "Run processors?": "#do_processing",
    "Is Active": "#is_active"
}

const emailChannelFormLabelToId = {
    "Name": "#name",
    "Is Active": "#is_active",
    "Run processors?": "#do_processing",
    "Protocol": "#protocol",
    "Server URL": "#server",
    "Username": "#s_username",
    "Password": "#s_password",
    "Port": "#port",
    "Use Auth?": "#use_auth",
    "Verify Peer": "#verify_peer",
    "Allow self signed certificates": "#allow_self_signed",
    "Folder": "#folder",
    "To": "#to_filter",
    "From": "#from_filter",
    "Subject": "#subject_filter",
    "CC": "#cc_filter",
    "Body": "#body_filter",
    "Post Read Data": "#post_read_parameter",
    "Post Read Action": "#post_read_action"
}

const processorFormLabelToId = {
    "Name": "#name",
    "Channel": "#channel_id",
    "Processor Class": "#processor_class"
}

const getFormElementByLabel =
    (page: Page | Locator, labelToId: Record<string, string>) =>
        (label: string) => page.locator(labelToId[label])

export class ChannelsHelper {
    static async createWebChannel(page: Page, channel: Record<string, string | boolean>)
    {
        expect(typeof channel["Name"] == "string" && channel["Name"] != "").toBeTruthy();

        // IMPURE! THIS SUCKS! SHOULD NOT BE USING `page.goto`
        // this pattern for grabbing the modal works in every other test that encounters modals
        // why doesn't it work here?
        /*
            await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
            await page.getByRole("button", { name: "Add Web Channel" }).click();
            await page.waitForSelector("#cmfive-modal");
            const modal = page.locator("#cmfive-modal");
        */
        // Timeouts because why not.
        await page.waitForTimeout(500);
        await page.goto(HOST + "/channels-web/edit");
        await page.waitForTimeout(500);

        const formElement = getFormElementByLabel(page, webChannelFormLabelToId);

        for(const option of ["Name", "Web API URL"])
            if (channel[option] !== undefined)
                await formElement(option).fill(channel[option] as string);

        for(const option of ["Is Active", "Run processors?"])
            if(channel[option] !== undefined) {
                if (channel[option] === true)
                    await formElement(option).check();
                else if (channel[option] === false)
                    await formElement(option).uncheck();
            }

        await page.getByRole("button", {name: "Save"}).click();
    }

    static async editWebChannel(page: Page, channel: Record<string, string | boolean>, edit: Record<string, string | boolean>): Promise<Record<string, string | boolean>>
    {
        expect(edit["Name"] === undefined || (typeof edit["Name"] == "string" && edit["Name"] != "")).toBeTruthy();

        await page.goto(HOST + "/channels/listchannels")
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
		await page.waitForLoadState();

        const formElement = getFormElementByLabel(page, webChannelFormLabelToId);

        for(const option of ["Name", "Web API URL"])
            if (edit[option] !== undefined)
                await formElement(option).fill(edit[option] as string);
        
        for(const option of ["Is Active", "Run processors?"])
            if(edit[option] !== undefined) {
                if (edit[option] === true)
                    await formElement(option).check();
                else if (edit[option] === false)
                    await formElement(option).uncheck();
            }
        
        await page.getByRole("button", {name: "Save"}).click();

        const editedChannel: Record<string, string | boolean> = {};
        for(const option of ["Name", "Web API URL", "Is Active", "Run processors?"])
            if (edit[option] !== undefined)
                editedChannel[option] = edit[option];
            else
                editedChannel[option] = channel[option];
        
        return editedChannel;
    }

    static async verifyWebChannel(page: Page, channel: Record<string, string | boolean>)
    {
       	await page.goto(HOST + "/channels/listchannels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", { name: "Edit" }).click();
		await page.waitForLoadState();

        const formElement = getFormElementByLabel(page, webChannelFormLabelToId);

        for(const option of ["Name", "Web API URL"])
            if (channel[option] !== undefined)
                await expect(formElement(option)).toHaveValue(channel[option] as string);
            else
                await expect(formElement(option)).toHaveValue("");

        for(const option of ["Is Active", "Run processors?"])
            if(channel[option] !== undefined) {
                if (channel[option] === true)
                    await expect(formElement(option)).toBeChecked();
                else if (channel[option] === false)
                    await expect(formElement(option)).not.toBeChecked();
            } // web channel defaults to true for both Is Active and Run Processors? if not specified
            else
                expect(formElement(option).isChecked()).toBeTruthy();

        await page.getByRole("button", {name: "Cancel"}).click();
    }

    static async createEmailChannel(page: Page, channel: Record<string, string | boolean>)
    {
        for(const required of ["Name", "Protocol", "Server URL", "Username", "Password"])
            expect(typeof channel[required] == "string" && channel[required] != "").toBeTruthy();

        // await CmfiveHelper.clickCmfiveNavbar(page, "Channels", "List Channels");
        // await page.getByRole("button", { name: "Add Email Channel" }).click();
        // await page.waitForSelector("#cmfive-modal.show");
        // const modal = page.locator("#cmfive-modal.show");
        await page.waitForTimeout(500);
        await page.goto(HOST + "/channels-email/edit");
        await page.waitForTimeout(500);

        const formElement = getFormElementByLabel(page, emailChannelFormLabelToId);

        await formElement("Protocol").selectOption(channel["Protocol"] as string);

        if(channel["Post Read Action"] !== undefined)
            await formElement("Post Read Action").selectOption(channel["Post Read Action"] as string);

        if (channel["Port"] !== undefined)
            await formElement("Port").fill(channel["Port"] as string);

        for(const option of ["Name", "Server URL", "Username", "Password"])
            await formElement(option).fill(channel[option] as string);

        for(const option of ["Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data"])
            if (channel[option] !== undefined)
                await formElement(option).fill(channel[option] as string);

        for(const option of ["Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(channel[option] !== undefined) {
                if (channel[option] === true)
                    await formElement(option).check();
                else if (channel[option] === false)
                    await formElement(option).uncheck();
            }

        await page.getByRole("button", {name: "Save"}).click();
    }

    static async editEmailChannel(page: Page, channel: Record<string, string | boolean>, edit: Record<string, string | boolean>)
    {
        for(const required of ["Name", "Protocol", "Server URL", "Username", "Password"])
            expect(edit[required] === undefined || (typeof edit[required] == "string" && edit[required] != "")).toBeTruthy();

        await page.goto(HOST + "/channels/listchannels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
		await page.waitForLoadState();

        const formElement = getFormElementByLabel(page, emailChannelFormLabelToId);

        if (edit["Port"] !== undefined)
            await formElement("Port").fill(edit["Port"] as string);

        for(const option of ["Protocol", "Post Read Action"])
            if (edit[option] !== undefined)
                await formElement(option).selectOption(edit[option] as string);

        for(const option of ["Name", "Server URL", "Username", "Password"])
            if (edit[option] !== undefined)
                await formElement(option).fill(edit[option] as string);

        for(const option of ["Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data"])
            if (edit[option] !== undefined)
                await formElement(option).fill(edit[option] as string);

        for(const option of ["Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(edit[option] !== undefined)
                if (edit[option] === true)
                    await formElement(option).check();
                else if (edit[option] === false)
                    await formElement(option).uncheck();

        await page.getByRole("button", {name: "Save"}).click();

        const editedChannel: Record<string, string | boolean> = {};
        for(const option of ["Name", "Protocol", "Server URL", "Username", "Password", "Port", "Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data", "Post Read Action", "Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if (edit[option] !== undefined)
                editedChannel[option] = edit[option];
            else if (channel[option] !== undefined)
                editedChannel[option] = channel[option];
        
        return editedChannel;
    }

    static async verifyEmailChannel(page: Page, channel: Record<string, string | boolean>)
    {
        await page.goto(HOST + "/channels/listchannels");
        await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", {name: "Edit"}).click();
		await page.waitForLoadState();

        const formElement = getFormElementByLabel(page, emailChannelFormLabelToId);

        await expect(formElement("Protocol")).toHaveValue(channel["Protocol"] as string);

        if (channel["Post Read Action"] !== undefined)
            await expect(formElement("Post Read Action")).toHaveValue(channel["Post Read Action"] as string);
        else
            await expect(formElement("Post Read Action").locator('option[selected="true"]')).toHaveCount(0);

        if (channel["Port"] !== undefined)
            await expect(formElement("Port")).toHaveValue(channel["Port"] as string);
        else
            await expect(formElement("Port")).toHaveValue("");

        for(const option of ["Name", "Server URL", "Username", "Password"])
            await expect(formElement(option)).toHaveValue(channel[option] as string);

        for(const option of ["Folder", "To", "From", "Subject", "CC", "Body", "Post Read Data"])
            if (channel[option] !== undefined)
                await expect(formElement(option)).toHaveValue(channel[option] as string);
            else
                await expect(formElement(option)).toHaveValue("");

        for(const option of ["Is Active", "Run processors?", "Use Auth?", "Verify Peer", "Allow self signed certificates"])
            if(channel[option] !== undefined) {
                if (channel[option] === true)
                    await expect(formElement(option)).toBeChecked();
                else if (channel[option] === false)
                    await expect(formElement(option)).not.toBeChecked();
            }
        
        for(const option of ["Is Active", "Run processors?"])
            if (channel[option] === undefined)
                expect(formElement(option).isChecked()).toBeTruthy();

        await page.getByRole("button", {name: "Cancel"}).click();
    }

    static async deleteChannel(page: Page, isMobile: boolean, channel: Record<string, string | boolean>)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Channels", "List Channels");

        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${channel["Name"]}"))) button:text("More")`);
        else
            await CmfiveHelper.getRowByText(page, channel["Name"] as string).getByRole("button", { name: "More" }).click();

        await page.getByRole("button", {name: "Delete"}).click();
        await expect(page.getByText("Channel deleted")).toBeVisible();
    }

    static async createProcessor(page: Page, isMobile: boolean, processorName: string, channel: string, processorClass: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Channels", "List Processors");
        // await page.getByRole("button", {name: "Add Processor"}).click();
        // await page.waitForSelector("#cmfive-modal", {state: "visible"});
        // const modal = page.locator("#cmfive-modal");
        await page.waitForTimeout(500);
        await page.goto(HOST + "/channels-processor/edit");
        await page.waitForTimeout(500);

        const formElement = getFormElementByLabel(page, processorFormLabelToId);

        await formElement("Name").fill(processorName);
        await formElement("Channel").selectOption(channel);
        await formElement("Processor Class").selectOption(processorClass);

        await page.getByRole("button", {name: "Save"}).click();
    }

    static async verifyProcessor(page: Page, processorName: string, channel: string, processorClass: string)
    {
        await page.goto(HOST + "/channels/listprocessors");
        await CmfiveHelper.getRowByText(page, processorName).getByRole("button", {name: "Edit"}).click(); // if this doesn't fail, name is verified as correct	
		await page.waitForLoadState();

        const formElement = getFormElementByLabel(page, processorFormLabelToId);

        await expect(formElement("Channel").locator('option[selected="true"]')).toHaveText(channel);
        await expect(formElement("Processor Class").locator('option[selected="true"]')).toHaveText(processorClass);

        await page.getByRole("button", {name: "Cancel"}).click();
    }

    static async deleteProcessor(page: Page, isMobile: boolean, processorName: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Channels", "List Processors");

        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${processorName}"))) button:text("More")`);
        else
            await CmfiveHelper.getRowByText(page, processorName).getByRole("button", { name: "More" }).click();
        
        await page.getByRole("button", {name: "Delete"}).click();
        await page.reload();
    }
}