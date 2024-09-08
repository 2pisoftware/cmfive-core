import { expect, Page } from "@playwright/test";
import { HOST, CmfiveHelper } from "@utils/cmfive";

export class AdminHelper {
    static async createUser(page: Page, isMobile: boolean, username: string, password: string, firstname: string, lastname: string, email: string, permissions: string[] = [])
    {
        await page.waitForTimeout(100); // let page load so next line doesn't fail if previous function ended on a redirect to user list
        if(page.url() != HOST + "/admin/users#internal")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Users");
        await page.waitForURL(HOST + "/admin/users#internal");

        await page.getByRole("button", {name: "Add New User"}).click();

        await page.waitForSelector("#cmfive-modal", {state: "visible"});

        await page.locator("#is_active").check();
        await page.locator("#login").fill(username);
        await page.locator("#password").fill(password);
        await page.locator("#password2").fill(password);
        await page.locator("#firstname").fill(firstname);
        await page.locator("#lastname").fill(lastname);
        await page.locator("#email").fill(email);

        await page.getByRole("button", {name: "Save"}).click();
        await expect(page.getByText("User " + username + " added")).toBeVisible();

        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${username}"))) button:text("Permissions")`);
        else
            await CmfiveHelper.getRowByText(page, username).getByRole("button", {name: "Permissions"}).click();
    
        if(permissions.length == 0)
            permissions.push("user");
        
        for(let permission of permissions)
            await page.locator("#check_"+permission).check();

        await page.getByRole("button", { name: "Save" }).click({force: true});
        await expect(page.getByText("Permissions updated")).toBeVisible();
    }

    static async deleteUser(page: Page, isMobile: boolean, username: string)
    {
        await page.waitForTimeout(100); // let page load so next line doesn't fail if previous function ended on a redirect to user list
        if(page.url() != HOST + "/admin/users#internal")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Users");
        await page.waitForURL(HOST + "/admin/users#internal");

        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${username}"))) button:text("Delete")`);
        else
            await CmfiveHelper.getRowByText(page, username).getByRole("button", {name: "Delete"}).click();
    
        await page.getByRole("button", {name: "Delete user", exact: true}).click();

        await expect(page.getByText("User " + username + " deleted.")).toBeVisible();
    }

    static async editUser(page: Page, isMobile: boolean, username: string, data: [string, string][]) {
        await page.waitForTimeout(100); // let page load so next line doesn't fail if previous function ended on a redirect to user list
        if(page.url() != HOST + "/admin/users#internal")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Users");
        await page.waitForURL(HOST + "/admin/users#internal");

        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${username}"))) button:text("Edit")`);
        else
            await CmfiveHelper.getRowByText(page, username).getByRole("button", { name: "Edit" }).click();

        for (let [label, value] of data) {
            if (label == "Title") {
                await page.getByLabel(label, { exact: true }).selectOption(value);
            }
            else if (label == "Active" || label == "Admin" || label == "External") {
                if (value == "true") {
                    await page.getByLabel(label, { exact: true }).check();
                }
                else {
                    await page.getByLabel(label, { exact: true }).uncheck();
                }
            }
            else {
                await page.getByLabel(label, { exact: true }).fill(value);
            }
        }

        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("User details updated")).toBeVisible();
    }

    static async createLookupType(page: Page, isMobile: boolean, type: string, code: string, lookup: string)
    {
        if(page.url() != HOST + "/admin/lookup#tab-1")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Lookup");

        await page.getByRole("link", {name: "New Item", exact: true}).click();

        const type_dropdown = await page.locator("#type option", {hasText: type}).count();
        if (type_dropdown == 0) {
            await page.getByLabel("or Add New Type").fill(type);
        } else {
            await page.getByRole("combobox").selectOption(type);
        }
        await page.getByLabel("Code").fill(code);
        await page.getByLabel("Title", { exact: true }).fill(lookup);
        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Lookup Item added")).toBeVisible();
    }

    static async createLookup(page: Page, isMobile: boolean, type: string, code: string, lookup: string)
    {
        if(page.url() != HOST + "/admin/lookup#tab-1")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Lookup");
            
        await page.getByRole("link", { name: "New Item", exact: true }).click();

        await page.getByRole("combobox").selectOption(type);
        await page.getByLabel("Code").fill(code);
        await page.getByLabel("Title", { exact: true }).fill(lookup);

        await page.getByRole("button", {name: "Save"}).click();
        await expect(page.getByText("Lookup Item added")).toBeVisible();
    }

    static async deleteLookup(page: Page, isMobile: boolean, lookup: string)
    {
        if(page.url() != HOST + "/admin/lookup#tab-1")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Lookup");

        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${lookup}"))) button:text("Delete")`);
        else
            await CmfiveHelper.getRowByText(page, lookup).getByRole("button", {name: "Delete"}).click();
    }

    static async editLookup(page: Page, isMobile: boolean, lookup: string, data: Record<string, string>)
    {
        if(page.url() != HOST + "/admin/lookup#tab-1")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Lookup");
        
        if(isMobile)
            await page.click(`ul:has(li:has(span:text("${lookup}"))) button:text("Edit")`);
        else
            await CmfiveHelper.getRowByText(page, lookup).getByRole("button", {name: "Edit"}).click();
        await page.waitForSelector("#cmfive-modal", { state: "visible" });
        const modal = page.locator("#cmfive-modal");

        if(data["Type"] != undefined)
            await modal.locator("#type").selectOption(data["Type"]);

        if(data["Code"] != undefined)
            await modal.locator("#code").fill(data["Code"]);
        
        if(data["Title"] != undefined)
            await modal.locator("#title").fill(data["Title"]);

        await modal.getByRole("button", {name: "Update"}).click();

        await expect(page.getByText("Lookup Item edited")).toBeVisible();
    }

    /**
     * returns the usergroup ID 
     */
    static async createUserGroup(page: Page, isMobile: boolean, usergroup: string): Promise<string>
    {
        if(page.url() != HOST + "/admin/groups")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Groups");
        
        await page.getByRole("button", {name: "New Group"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});

        await page.locator("#title").fill(usergroup);
        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("New group added")).toBeVisible();

        if(isMobile)
            await page.click(`ul:has(li:has(u:text("${usergroup}"))) button:text("Edit")`);
        else
            await CmfiveHelper.getRowByText(page, usergroup).getByRole("button", {name: "Edit"}).click();
        return page.url().split("/moreInfo/")[1];
    }

    static async deleteUserGroup(page: Page, isMobile: boolean, usergroup: string)
    {
        if(page.url() != HOST + "/admin/groups")
            await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Groups");

        if(isMobile)
            await page.click(`ul:has(li:has(u:text("${usergroup}"))) button:text("Delete")`);
        else
            await CmfiveHelper.getRowByText(page, usergroup).getByRole("button", {name: "Delete"}).click();

        await expect(page.getByText("Group deleted")).toBeVisible();
    }

    static async addUserGroupMember(page: Page, isMobile: boolean, usergroup: string, usergroupID: string, user: string, owner: boolean = false)
    {
        if(page.url() != HOST + "/admin/moreInfo" + usergroupID) {
            if(page.url() != HOST + "/admin/groups")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Groups");
            
            if(isMobile)
                await page.click(`ul:has(li:has(u:text("${usergroup}"))) button:text("Edit")`);
            else
                await CmfiveHelper.getRowByText(page, usergroup).getByRole("button", {name: "Edit"}).click();   
        }

        await page.getByRole("button", {name: "New Member"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});

        await page.locator("#member_id").selectOption(user);

        if(owner)
            await page.locator("#is_owner").check();
        else
            await page.locator("#is_owner").uncheck();

        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText(user).first()).toBeVisible();
    }

    static async deleteUserGroupMember(page: Page, isMobile: boolean, usergroup: string, usergroupID: string, user: string)
    {
        if(page.url() != HOST + "/admin/moreInfo" + usergroupID) {
            if(page.url() != HOST + "/admin/groups")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Groups");
            
            if(isMobile)
                await page.click(`ul:has(li:has(u:text("${usergroup}"))) button:text("Edit")`);
            else
                await CmfiveHelper.getRowByText(page, usergroup).getByRole("button", {name: "Edit"}).click();   
        }

        await CmfiveHelper.getRowByText(page, user).getByRole("link", {name: "Delete"}).click();

        await expect(page.getByText("Member deleted")).toBeVisible();
    }

    static async editUserGroupPermissions(page: Page, isMobile: boolean, usergroup: string, usergroupID: string, permissions: string[])
    {
        if(page.url() != HOST + "/admin/moreInfo" + usergroupID) {
            if(page.url() != HOST + "/admin/groups")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Groups");
            
            if(isMobile)
                await page.click(`ul:has(li:has(u:text("${usergroup}"))) button:text("Edit")`);
            else
                await CmfiveHelper.getRowByText(page, usergroup).getByRole("button", {name: "Edit"}).click();   
        }

        await page.getByRole("button", {name: "Edit Permissions"}).click();

        for(let permission of permissions)
            await page.locator("#check_"+permission).check();

        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Permissions updated")).toBeVisible();
    }

    /**
     * returns the template ID
     */
    static async createTemplate(page: Page, isMobile: boolean, templateTitle: string, module: string, category: string, code: string[]): Promise<string>
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Templates");
        await page.getByRole("button", {name: "Add Template"}).click();

        await page.getByLabel("Active").check();
        await page.getByLabel("Title").fill(templateTitle);
        await page.getByLabel("Module").fill(module);
        await page.getByLabel("Category").fill(category);
        await page.getByRole("button", {name: "Save"}).click();
        await expect(page.getByText("Template saved")).toBeVisible();

        await page.getByRole("link", {name: "Template", exact: true}).click();
        await page.locator("#template_title").fill(templateTitle);
        
        await page.locator("#template_body").nth(1).click();

        // code mirror auto completes open/closed html tags, so skip writing them
        // typing <table> puts cursor on new line between <table> and </table>
        // typing <tr>/<td> writes </tr>/</td> respectively on the same line, cursor before the closing tag

        for(let line of code) {
            if(line.indexOf("</") != -1) {    
                await page.keyboard.type(line.split("</")[0]);

                for(let i = 0; i < line.split("</")[1].length + 2; i++)
                    await page.keyboard.press("ArrowRight");

                await page.keyboard.press("Enter");
                continue;
            }

            await page.keyboard.type(line);
            await page.keyboard.press("Enter");
        }
        
        await page.getByRole("button", {name: "Save"}).click();
        await expect(page.getByText("Template saved")).toBeVisible();

        return page.url().split("/admin-templates/edit/")[1].split("#")[0];
    }

    static async demoTemplate(page: Page, isMobile: boolean, templateTitle: string, templateID: string): Promise<Page>
    {
        if(page.url() != HOST + "/admin-templates/edit/" + templateID + "#details") {
            if(page.url() != HOST + "/admin-templates")
                await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Templates");
            if(isMobile)
                await page.click(`ul:has(li:has(span:text("${templateTitle}"))) button:text("Edit")`);
            else
                await CmfiveHelper.getRowByText(page, templateTitle).getByRole("button", {name: "Edit"}).click();
        }

        await page.getByRole("link", {name: "Test Output"}).click();

        const [pages] = await Promise.all([page.context().waitForEvent("page")]);
        const tabs = pages.context().pages();
        await tabs[1].waitForLoadState("load");

        return tabs[1];
    }

    static async installDatabaseSeeds(page: Page, isMobile: boolean, module: string){
        //installs databse seeds if not installed
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Migrations");
        await page.getByRole('link', {name: 'Database Seeds'}).click();
        const moduleTab = await page.locator(`#${module}-tab-seed`);

        //collect number of buttons
        await moduleTab.click()
        const installButtons = await page.getByRole('button', {name: 'Install'});
        const installButtonsCount = await installButtons.count();

        //loop for number of buttons aka how many seeds to install
        for (let i = 0; i < installButtonsCount; i++) {
            await moduleTab.click()
            //each time you click the button it takes one locator result off the page
            await installButtons.first().click(); //this always resolves the first button
            await page.waitForSelector('div.alert-success', {state: 'visible'});
        }
    }
}
