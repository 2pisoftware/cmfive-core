import { expect, test } from "@playwright/test";
import { HOST, CmfiveHelper } from "@utils/cmfive";
import { AdminHelper } from "@utils/admin";

const report_name = CmfiveHelper.randomID("report_");

test.describe.configure({ mode: 'serial' });

test("Test that users can create reports", async ({ page }) => {
    await CmfiveHelper.login(page, "admin", "admin");
    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Create a Report");
    await page.locator("#title").fill(report_name);
    await page.locator("#module").selectOption({ label: "Admin" });
    await page.locator(".savebutton").click()

    expect(page.getByText("Report created")).toBeVisible();
    await page.locator("a[href='#code']", {hasText: "SQL"}).click()

    // Define report SQL
    const reportSQL = "[[test||text||Test]]@@headers|| select 'known' as 'pedigree' , 'established' as 'precedent' @@ @@info||select distinct classname from migration @@";
    await page.locator(".CodeMirror .CodeMirror-code").click();
    await page.keyboard.type(reportSQL);

    // Save report
    await page.getByRole('button', { name: 'Save Report' }).click();
});

test("Test that users can run reports", async ({ page }) => {
    await CmfiveHelper.login(page, "admin", "admin");
    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Report Dashboard");

    expect(page.getByText(report_name)).toBeVisible();

    await page.getByText(report_name).click();
    await page.locator("#test").fill("Hello");
    await page.getByRole('button', { name: 'Display Report' }).click();

    expect(page.getByText("known")).toBeVisible();
    expect(page.getByText("precedent")).toBeVisible();
})

test("Test that users can attach templates to reports", async ({ page }) => {
    await CmfiveHelper.login(page, "admin", "admin");

    // @TODO: template creation broken

    const template_id = await AdminHelper.createTemplate(page, "Test Template", "Report", "report_template",
    [
        "<table width='100%' align='center' class='form-table' cellpadding='1'>"
       ,"    <tr>"
       ,"        <td colspan='2' style='border:none;'>"
       ,"            <img width='400' src='' style='width: 400px;' />"
       ,"        </td>"
       ,"        <td colspan='2' style='border:none; text-align:right;'>"
       ,"            Test Company<br/>"
       ,"            123 Test St, Test Town, NSW 1234<br/>"
       ,"            test@example.com<br/>"
       ,"            ACN: 123456789<br/>"
       ,"            ABN: 12345678901<br/>"
       ,"        </td>"
       ,"    </tr>"
       ,"</table>"
   ]);

    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Report Dashboard");
    expect(page.locator("div", {has: page.getByText(report_name, {exact: true})})).toBeVisible();

    // Attach template to report
    await CmfiveHelper.getRowByText(page, report_name).getByText("Edit").click();
    await page.locator("a[href='#templates']", {hasText: "Templates"}).click();
    await page.locator("#templates button", {hasText: "Add Template"}).click();
    await page.locator("#template_id").selectOption({ value: template_id });
    await page.locator("#type").selectOption({ label: "HTML" });
    await page.getByRole('button', { name: 'Save' }).click();

    // Run report
    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Report Dashboard");
    await CmfiveHelper.getRowByText(page, report_name).getByText(report_name).click();

    await page.locator(".body #template").selectOption("Test Template");
    await page.getByRole('button', { name: 'Display Report' }).click();

    expect(page.getByText("Test Company")).toBeVisible();
    expect(page.getByText("ABN: 12345678901")).toBeVisible();
});

test("Test that users can duplicate reports", async ({ page }) => {
    await CmfiveHelper.login(page, "admin", "admin");
    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Report Dashboard");
    expect(page.getByText(report_name)).toBeVisible();
    await CmfiveHelper.getRowByText(page, report_name).getByText("Duplicate").click();
    expect(page.getByText("Successfully duplicated Report")).toBeVisible();
    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Report Dashboard");
    expect(await page.locator(".body table tbody a", {hasText: report_name + " - Copy"})).toHaveCount(1)
    // expect(page.getByText(report_name + " - Copy")).toBeVisible();
});

test("Test that users can delete reports", async ({ page }) => {
    await CmfiveHelper.login(page, "admin", "admin");
    await CmfiveHelper.acceptDialog(page);
    await CmfiveHelper.clickCmfiveNavbar(page, "Report", "Report Dashboard");
    expect(page.getByRole('link', { name: report_name, exact: true })).toBeVisible();

    await CmfiveHelper.getRowByText(page, report_name).getByText("Delete").click();
    expect(page.getByText("Report deleted")).toBeVisible();
});