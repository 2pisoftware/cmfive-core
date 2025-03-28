import { expect, test } from "@playwright/test";
import { CmfiveHelper, GLOBAL_TIMEOUT } from "@utils/cmfive";
import { FormHelper } from "@utils/form";

test.describe.configure({mode: 'parallel'});

test("that forms can be created, edited and deleted", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const form = CmfiveHelper.randomID("form_");
    const form_edited = form + "_edited";
    const description = form + "For testing purposes";
    const description_edited = form + "For testing purposes, but edited";

    await FormHelper.createForm(page, isMobile, form, description);
    await FormHelper.editForm(page, isMobile, form, form_edited, description_edited);

    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Forms");
    await expect(page.getByRole('link', { name: form_edited})).toBeVisible();
    await expect(page.getByRole('cell', { name: description_edited }).getByRole('paragraph')).toBeVisible();

    await FormHelper.deleteForm(page, isMobile, form_edited);
});

test("that singleton forms can be created and deleted", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const form = CmfiveHelper.randomID("form_");
    const description = "For singleton testing purposes";

    await FormHelper.createForm(page, isMobile, form, description);
    await FormHelper.addFormField(page, isMobile, form, form+"_field", form+"_field_key", "Text");

    await page.getByText("Mapping").click();
    await page.getByText("single").first().click();
    await page.getByRole("button", {name: "Save"}).click();
    await expect(page.getByText("Form mappings updated")).toBeVisible();

    await FormHelper.deleteForm(page, isMobile, form);
});

test("that form applications can be created, edited and deleted", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const form = CmfiveHelper.randomID("form_");
    const application = CmfiveHelper.randomID("application_");

    await FormHelper.createForm(page, isMobile, form, "For application testing purposes");
    await FormHelper.createApplication(page, isMobile, application, "For test wrapping");

    await FormHelper.addFormField(page, isMobile, form, form+"_name", form+"_name", "Text");
    await FormHelper.addFormField(page, isMobile, form, form+"_clocked", form+"_clocked", "Time");
    await FormHelper.addFormField(page, isMobile, form, form+"_truth", form+"_truth", "Yes/No");

    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
    await page.getByRole("link", { name: application }).click();
    await page.locator(".btn", { hasText: "Manage" }).click();
    await page.locator(".btn", { hasText: "Attach Form" }).click();
    
    // await page.waitForSelector("#form_application_form_modal", {state: "visible"});
    // const attach_form_modal = page.locator("#form_application_form_modal");
    await CmfiveHelper.fillAutoComplete(page, "form", form, form);
    await page.keyboard.press("Escape");
    // await page.locator("#form").selectOption(form);

    // Current fix for issue with tom select not closing after selection
    // await page.keyboard.press("Escape");
    await page.locator(".savebutton").click();

    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
    await page.getByRole("link", {name: application}).click();
    await page.locator(".btn", {hasText: "Add new "+form}).click();
    await page.locator("#cmfive-modal").waitFor({ state: "visible" });
    const modal = page.locator("#cmfive-modal");

    await modal.locator(`#${form}_name`).fill(form+" name");

    await page.locator(`#${form}_clocked`).click();

    await page.keyboard.type("05:15PM");

    await modal.getByText(form+"_truth").click();

    await modal.locator(".savebutton").click();

    await expect(page.getByText(form+" name")).toBeVisible();

    await FormHelper.deleteForm(page, isMobile, form);

    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Form", "Applications");
    await page.getByRole("link", {name: application}).click();

    await expect(page.getByRole("button", {name: "Add new "+form})).toBeHidden();
});