import { expect, Page } from "@playwright/test";
import { CmfiveHelper } from "@utils/cmfive";

export class FormHelper {
    static async createForm(page: Page, isMobile: boolean, form: string, description: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");

        await page.locator(".btn", {hasText: "Add a form"}).click();

        await page.locator("#title").fill(form);
        await page.locator("#description").fill(description);

        await page.locator(".savebutton", {hasText: "Save"}).click();

        await page.waitForLoadState();

        await expect(page.getByText("Form created")).toBeVisible();
    }

    static async deleteForm(page: Page, isMobile: boolean, form: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");

        await CmfiveHelper.getRowByText(page, form).getByRole("button", {name: "Delete"}).click();

        await expect(page.getByText("Form deleted")).toBeVisible();
    }

    static async editForm(page: Page, isMobile: boolean, form: string, newName: string, newDescription: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");

        await CmfiveHelper.getRowByText(page, form).getByRole("button", {name: "Edit"}).click();

        await page.locator("#title").fill(newName);
        await page.locator("#description").fill(newDescription);

        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Form updated")).toBeVisible();
    }

    static async addFormField(page: Page, isMobile: boolean, form: string, name: string, key: string, type: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");

        await page.getByRole("link", {name: form}).click();
        await page.getByRole("button", {name: "Add a field"}).click();   
        await page.locator("#cmfive-modal").waitFor({ state: "visible" });
        const modal = page.locator("#cmfive-modal");

        await modal.locator("#name").fill(name);
        await modal.locator("#technical_name").fill(key);
        await modal.locator("#type").selectOption(type);

        await modal.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Form created")).toBeVisible();
    }

    static async createApplication(page: Page, isMobile: boolean, application: string, description: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
        await page.locator(".btn", {hasText: "Create Application"}).click();

        await page.locator("#title").fill(application);
        await page.locator("#description").fill(description);

        await page.locator("#is_active").check({force: true});
        await page.locator(".savebutton").click();
        // await page.waitForSelector("div[id^='form-application-'] a.button");
        // await page.locator("div[id^='form-application-'] a.button.small:not(.secondary)").click(); // .getByText("Save", {exact: true})

        // await page.waitForResponse(new RegExp('/form-vue/save_application/*'));

        // No need to navigate as the page doesn't redirect
        // await CmfiveHelper.clickCmfiveNavbar(page, "Form", "Applications");
        await expect(page.getByText(application)).toBeVisible();
    }

    static async attachApplicationForm(page: Page, isMobile: boolean, application: string, form: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
        await CmfiveHelper.getRowByText(page, application).getByRole("button", {name: "Edit"}).click();

        await page.locator(".btn", {hasText: "Attach form"}).click();
        await page.locator("#cmfive-modal").waitFor({state: "visible"});
        const modal = page.locator("#cmfive-modal");

        await CmfiveHelper.fillAutoComplete(page, "form", form, form);

        await modal.getByRole("button", {name: "Save"}).click();

        await page.waitForLoadState();
        await expect(page.getByText(form)).toBeVisible();
    }
};