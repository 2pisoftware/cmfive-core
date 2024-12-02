import { expect, test } from "@playwright/test";
import { AdminHelper } from "@utils/admin";
import { CmfiveHelper, GLOBAL_TIMEOUT, HOST } from "@utils/cmfive";

test.describe.configure({ mode: 'parallel' });

test("Admin can update password of user", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const user = CmfiveHelper.randomID("user_");
    await AdminHelper.createUser(
        page,
        isMobile,
        user,
        user + "_password",
        user + "_firstName",
        user + "_lastName",
        user + "@localhost.com"
    );

    await page.goto(`${HOST}/admin/users`);

    const row = CmfiveHelper.getRowByText(page, user);
    const edit = row.getByRole("button", { name: "Edit" });
    await edit.click();

    const security = page.getByRole('link', { name: 'Security' });
    await security.waitFor();
    await security.click();

    await page.locator('input[name="password"]').fill("test password");
    await page.locator('input[name="repeat_password"]').pressSequentially("test password");
    await page.getByRole('button', { name: 'Update Password' }).click();

    await CmfiveHelper.logout(page);

    await CmfiveHelper.login(page, user, "test password");
});

test("that an admin can create and delete a user", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const user = CmfiveHelper.randomID("user_");
    await AdminHelper.createUser(
        page,
        isMobile,
        user,
        user + "_password",
        user + "_firstName",
        user + "_lastName",
        user + "@localhost.com"
    );

    await AdminHelper.deleteUser(page, isMobile, user);
});

test("that users, groups & permissions are assignable", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const user = CmfiveHelper.randomID("user_");
    await AdminHelper.createUser(
        page,
        isMobile,
        user,
        user + "_password",
        user + "_firstName",
        user + "_lastName",
        user + "@localhost.com"
    );

    const parentgroup = CmfiveHelper.randomID("usergroup_");
    const usergroup = CmfiveHelper.randomID("usergroup_");
    const parentgroupID = await AdminHelper.createUserGroup(page, isMobile, parentgroup);
    const usergroupID = await AdminHelper.createUserGroup(page, isMobile, usergroup);
    await AdminHelper.addUserGroupMember(page, isMobile, parentgroup, parentgroupID, usergroup.toUpperCase());
    await AdminHelper.addUserGroupMember(page, isMobile, usergroup, usergroupID, user + "_firstName " + user + "_lastName");

    await AdminHelper.editUserGroupPermissions(page, isMobile, usergroup, usergroupID, ["user", "comment"]);
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Users");

    if (isMobile)
        await page.click(`ul:has(li:has(span:text("${user}"))) button:text("Permissions")`);
    else
        await CmfiveHelper.getRowByText(page, user).getByRole("button", { name: "Permissions" }).click();

    await expect(page.locator("#check_comment")).toBeChecked();
    await expect(page.locator("#check_comment")).toBeDisabled();

    await AdminHelper.deleteUserGroup(page, isMobile, usergroup);
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Users");

    if (isMobile)
        await page.click(`ul:has(li:has(span:text("${user}"))) button:text("Permissions")`);
    else
        await CmfiveHelper.getRowByText(page, user).getByRole("button", { name: "Permissions" }).click();
    await expect(page.locator("#check_comment")).not.toBeChecked();
    await expect(page.locator("#check_comment")).toBeEnabled();

    await AdminHelper.deleteUserGroup(page, isMobile, parentgroup);
    await AdminHelper.deleteUser(page, isMobile, user);
});

test("that Cmfive Admin handles lookups", async ({ page, isMobile }) => {
    test.slow();
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    const user = CmfiveHelper.randomID("user_");
    await AdminHelper.createUser(
        page,
        isMobile,
        user,
        user + "_password",
        user + "_firstName",
        user + "_lastName",
        user + "@localhost.com"
    );

    const lookup_1 = user + "_lookup_1";
    const lookup_2 = user + "_lookup_2";
    const lookup_3 = user + "_lookup_3";

    await AdminHelper.createLookupType(page, isMobile, "title", "Title", "Title");
    await AdminHelper.createLookup(page, isMobile, "title", lookup_1, lookup_1);
    await AdminHelper.editUser(page, isMobile, user, [["Title", lookup_1]]);
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Lookups");
    await expect(page.getByText(lookup_1).nth(isMobile ? 2 : 1)).toBeVisible();

    await AdminHelper.editLookup(page, isMobile, lookup_1, { "Title": lookup_2 });
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "List Users");

    if (isMobile)
        await page.click(`ul:has(li:has(span:text("${user}"))) button:text("Edit")`);
    else
        await CmfiveHelper.getRowByText(page, user).getByRole("button", { name: "Edit" }).click();

    await page.getByRole('combobox', { name: 'Title' }).click();
    expect((await page.content()).includes(lookup_2)).toBeTruthy();

    await AdminHelper.deleteLookup(page, isMobile, lookup_2);
    await expect(page.getByText("Cannot delete lookup as it is used as a title for the contacts: " + user + "_firstName " + user + "_lastName")).toBeVisible();

    await AdminHelper.createLookup(page, isMobile, "title", lookup_3, lookup_3);
    await expect(page.getByText(lookup_3).nth(isMobile ? 2 : 1)).toBeVisible();

    await AdminHelper.editUser(page, isMobile, user, [["Title", lookup_3]]);
    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Lookups");
    await AdminHelper.deleteLookup(page, isMobile, lookup_2);
    await expect(page.getByText("Lookup Item deleted")).toBeVisible();

    await AdminHelper.deleteUser(page, isMobile, user);

    await AdminHelper.deleteLookup(page, isMobile, lookup_3);
    await expect(page.getByText("Lookup Item deleted")).toBeVisible();
});

test("that Cmfive Admin handles templates", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);

    await CmfiveHelper.login(page, "admin", "admin");

    const template = CmfiveHelper.randomID("template_");
    const templateID = await AdminHelper.createTemplate(page, isMobile, template, "Admin", "Templates", [
        "<table width='100%' align='center' class='form-table' cellpadding='1'>"
        , "    <tr>"
        , "        <td colspan='2' style='border:none;'>"
        , "            <img width='400' src='' style='width: 400px;' />"
        , "        </td>"
        , "        <td colspan='2' style='border:none; text-align:right;'>"
        , "            Test Company<br/>"
        , "            123 Test St, Test Town, NSW 1234<br/>"
        , "            test@example.com<br/>"
        , "            ACN: 123456789<br/>"
        , "            ABN: 12345678901<br/>"
        , "        </td>"
        , "    </tr>"
        , "</table>"
    ]);


    const templateTestPage = await AdminHelper.demoTemplate(page, isMobile, template, templateID);

    await expect(templateTestPage.getByText("Test Company")).toBeVisible();
});


test("that Cmfive Admin handles bad templates", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);

    await CmfiveHelper.login(page, "admin", "admin");

    const template = CmfiveHelper.randomID("template_");
    const templateID = await AdminHelper.createTemplate(page, isMobile, template, "Admin", "Templates", [
        "<link href=\"https://fonts.googleapis.com/css?family=Open+Sans\" rel=\"stylesheet\">",
        "<style>",
        "body {text-align: left;font-family: \"open sans' sans-serif;",
        "    color: #2b286a;",
        "}",
        "</style>",
        "<table width='100%' align='center' class='form-table' cellpadding='1'>",
        "    <tr>",
        "        <td colspan='2' style='border:none;'>",
        "            <img width='400' src='' style='width: 400px;' />",
        "        </td>",
        "        <td colspan='2' style='border:none; text-align:right;'>",
        "            Test Company<br/>",
        "            123 Test St, Test Town, NSW 1234<br/>",
        "            test@example.com<br/>",
        "            ACN: 123456789<br/>",
        "            ABN: 12345678901<br/>",
        "        </td>",
        "    </tr>",
        "</table>",
    ]);

    await AdminHelper.viewTemplate(page, isMobile, template, templateID);
    await page.getByRole("link", { name: "Template" }).click();

    await expect(page.locator('.cm-line')).toHaveCount(22);
});

test("that Cmfive Admin can create/run/rollback migrations", async ({ page, isMobile }) => {
    test.setTimeout(GLOBAL_TIMEOUT);
    CmfiveHelper.acceptDialog(page);

    await CmfiveHelper.login(page, "admin", "admin");

    await CmfiveHelper.clickCmfiveNavbar(page, isMobile, "Admin", "Migrations");

    // create migration
    await page.getByRole("link", { name: "Individual" }).click();
    await page.getByRole("button", { name: "Create an admin migration" }).click();
    await page.locator("#cmfive-modal").waitFor({ state: "visible" });
    const modal = page.locator("#cmfive-modal");

    const migration = "Migration" + CmfiveHelper.randomID("") + "_TestMigration";
    await modal.locator("#name").fill(migration);
    await modal.getByRole("button", { name: "Save" }).click();
    await expect(page.getByText("Migration created")).toBeVisible();

    if (await page.locator("#admin table tr td", { hasText: migration }).count() != 1) {
        console.warn("Could not create migration " + migration + ", skipping tests");
        return;
    }

    // test that migration can be run/rolled back from "Individual" migrations tab
    await page.getByRole("link", { name: "Individual" }).click();

    const migrationsCount = await page.getByRole('button', { name: 'Migrate to here' }).count();
    const plural = migrationsCount == 1 ? " has" : "s have";

    if (isMobile)
        await page.click(`ul:has(li:has(span:text("Admin${migration}"))) button:text("Migrate to here")`);
    else
        await CmfiveHelper.getRowByText(page, "Admin" + migration).getByRole("button", { name: "Migrate to here" }).click();

    await expect(page.getByText(`${migrationsCount} migration${plural} run.`)).toBeVisible();

    if (isMobile)
        await page.click(`ul:has(li:has(span:text("Admin${migration}"))) button:text("Rollback to here")`);
    else
        await CmfiveHelper.getRowByText(page, "Admin" + migration).getByRole("button", { name: "Rollback to here" }).click();

    await expect(page.getByText("1 migration has rolled back")).toBeVisible();

    // test that migration can be run/rolled back from "Individual" migrations tab
    await page.getByRole("link", { name: "Batch" }).click();
    
    const locator = isMobile
        ? page.locator("span", { hasText: "Admin - Admin" + migration })
        : page.getByRole("cell", { name: "admin - Admin" + migration });

    await expect(locator).toBeVisible();

    await page.getByRole("button", { name: "Install migrations" }).click();
    await expect(page.getByText("1 migration has run.")).toBeVisible();

    await page.getByRole("button", { name: "Rollback latest batch" }).click();
    await expect(page.getByText("1 migration rolled back")).toBeVisible();
});