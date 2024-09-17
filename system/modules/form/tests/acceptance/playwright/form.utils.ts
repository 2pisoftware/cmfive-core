import { HOST, CmfiveHelper } from "@utils/cmfive";
import { expect, Page } from "@playwright/test";

export class FormHelper {
    static async createForm(page: Page, isMobile: boolean, form: string, description: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");

        await page.getByRole("button", {name: "Add a form"}).click();

        await page.getByLabel("Title").fill(form);
        await page.getByLabel("Description").fill(description);

        await page.getByRole("button", {name: "Save"}).click();

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

        await page.getByLabel("Title").fill(newName);
        await page.getByLabel("Description").fill(newDescription);

        await page.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Form updated")).toBeVisible();
    }

    static async addFormField(page: Page, isMobile: boolean, form: string, name: string, key: string, type: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");

        await page.getByRole("link", {name: form}).click();
        await page.getByRole("button", {name: "Add a field"}).click();   
        await page.waitForSelector("#cmfive-modal", { state: "visible" });
        await page.waitForSelector("#cmfive-modal", { state: "visible" });
        const modal = page.locator("#cmfive-modal");

        await modal.getByLabel("Name").fill(name);
        await modal.getByLabel("Key").fill(key);
        await modal.getByLabel("Type").selectOption(type);

        await modal.getByRole("button", {name: "Save"}).click();

        await expect(page.getByText("Form created")).toBeVisible();
    }

    static async createApplication(page: Page, isMobile: boolean, application: string, description: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
        await page.getByRole("button", {name: "Create Application"}).click();

        await page.getByLabel("Title").fill(application);
        await page.getByLabel("Description").fill(description);

        await page.getByLabel("Active").check({force: true});

        await page.waitForSelector("div[id^='form-application-'] a.button");
        await page.locator("div[id^='form-application-'] a.button.small:not(.secondary)").click(); // .getByText("Save", {exact: true})

        await page.waitForResponse(new RegExp('/form-vue/save_application/*'));

        // No need to navigate as the page doesn't redirect
        // await CmfiveHelper.clickCmfiveNavbar(page, "Form", "Applications");
        await expect(page.getByText(application)).toBeVisible();
    }

    static async attachApplicationForm(page: Page, isMobile: boolean, application: string, form: string)
    {
        await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
        await CmfiveHelper.getRowByText(page, application).getByRole("button", {name: "Edit"}).click();

        await page.getByRole("button", {name: "Attach form"}).click();
        await page.waitForSelector("#cmfive-modal", {state: "visible"});
        const modal = page.locator("#cmfive-modal");

        await modal.getByLabel("Form").selectOption(form);

        await modal.getByRole("button", {name: "Save"}).click();

        await page.waitForTimeout(1_000);
        await expect(page.getByText(form)).toBeVisible();
    }
};