import { expect, test } from "@playwright/test";
import { GLOBAL_TIMEOUT, CmfiveHelper } from "@utils/cmfive";
import { FormHelper } from "@utils/form";
import { DateTime } from "luxon";

test.describe.configure({mode: 'parallel'});

test("Test that forms can be created, edited and deleted", async ({ page }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const form = CmfiveHelper.randomID("form_");
    const form_edited = form + "_edited";
    const description = form + "For testing purposes";
    const description_edited = form + "For testing purposes, but edited";

    await FormHelper.createForm(page, form, description);
    await FormHelper.editForm(page, form, form_edited, description_edited);

    await CmfiveHelper.clickCmfiveNavbar(page, "Form", "Forms");
    await expect(page.getByText(form_edited)).toBeVisible();
    await expect(page.getByText(description_edited)).toBeVisible();

    await FormHelper.deleteForm(page, form_edited);
});

test("Test that singleton forms can be created and deleted", async ({ page }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const form = CmfiveHelper.randomID("form_");
    const description = "For singleton testing purposes";

    await FormHelper.createForm(page, form, description);
    await FormHelper.addFormField(page, form, form+"_field", form+"_field_key", "Text");

    await page.getByText("Mapping").click();
    await page.getByText("single").first().click();
    await page.getByRole("button", {name: "Save"}).click();
    await expect(page.getByText("Form mappings updated")).toBeVisible();

    await FormHelper.deleteForm(page, form);
});

test("Test that form applications can be created, edited and deleted", async ({ page }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const form = CmfiveHelper.randomID("form_");
    const application = CmfiveHelper.randomID("application_");

    await FormHelper.createForm(page, form, "For application testing purposes");
    await FormHelper.createApplication(page, application, "For test wrapping");

    await FormHelper.addFormField(page, form, form+"_name", form+"_name", "Text");
    await FormHelper.addFormField(page, form, form+"_clocked", form+"_clocked", "Time");
    await FormHelper.addFormField(page, form, form+"_truth", form+"_truth", "Yes/No");

    await CmfiveHelper.clickCmfiveNavbar(page, "Form", "Applications");
    await CmfiveHelper.getRowByText(page, application).getByRole("button", { name: "Edit" }).click();
    
    await page.getByRole("button", { name: "Attach form" }).click();
    
    await page.waitForSelector("#form_application_form_modal", {state: "visible"});
    const attach_form_modal = page.locator("#form_application_form_modal");
    await attach_form_modal.getByLabel("Form").selectOption(form);
    await attach_form_modal.getByText("Save").click();

    await CmfiveHelper.clickCmfiveNavbar(page, "Form", "Applications");
    await page.getByRole("link", {name: application}).click();
    await page.getByRole("button", {name: "Add new "+form}).click();
    await page.waitForSelector("#cmfive-modal", { state: "visible" });
    const modal = page.locator("#cmfive-modal");

    await modal.getByLabel(form+"_name").fill(form+" name");

    await modal.getByLabel(form+"_clocked").click();
    await page.waitForSelector("#ui-datepicker-div");
    await page.locator("#ui-datepicker-div").getByRole("link", {name: "1", exact: true}).click();
    await expect(page.locator("#"+form+"_clocked")).toHaveValue(DateTime.now().set({day: 1}).toFormat("dd/MM/yyyy") as string + " 12:00 am");

    await modal.getByText(form+"_truth").click();

    await modal.getByRole("button", {name: "Save"}).click();

    await expect(page.getByText(form+" name")).toBeVisible();

    await FormHelper.deleteForm(page, form);

    await CmfiveHelper.clickCmfiveNavbar(page, "Form", "Applications");
    await page.getByRole("link", {name: application}).click();

    await expect(page.getByRole("button", {name: "Add new "+form})).not.toBeVisible();
});