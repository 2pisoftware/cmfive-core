<?php
namespace Helper;

class CmfiveReportModule extends \Codeception\Module
{
    public function createReport($I, $name, $module)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Create a Report');
        $I->fillField('#title', $name);
        $I->selectOption('#module', $module);
        $I->click('Save');
    }

    public function defineReportSQL($I, $name, $SQL)
    {
        $I->wait(1);
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->wait(2);
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->wait(1);
        $I->click('SQL');
        $I->wait(1);
        $I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue(\"" . $SQL . "\")");
        $I->click("//div[@id='code']/form/div/button");
    }

    public function attachTemplate($I, $name, $template)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->wait(2);
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->wait(2);
        $I->click("//a[@href='#templates' and text()='Templates']");
        $I->wait(1);
        $I->click('Add Template');
        $I->wait(1);
        $I->selectOption("#template_id", $template);
        $I->selectOption("#type", 'HTML');
        $I->click('Save');
    }

    public function runReportTemplate($I, $name, $template)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->wait(2);
        $I->click($name, 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->wait(2);
        $I->selectOption("#template", $template);
        $I->click('Display Report');
    }

    public function requestReport($I, $name)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->wait(1);
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->wait(1);
        $I->click("//div[@id='report']/button");
    }

    public function requestReportWithData($I, $name, $data = [])
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        // $I->wait(1);
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        // $I->wait(1);
        $I->click("//div[@id='report']/button");
        // Add code to fill in data
        $I->fillForm($data);
        $I->click('form > .row:last-child > button:first-child');
    }

    public function createFeed($I, $reportName, $feed)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
        $I->click('Create Feed');
        $I->fillField('#title', $feed);
        $I->selectOption('#rid', $reportName);
        $I->click('Save');
    }

    public function requestReportConnection($I)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Connections');
        $I->click('Add a Connection');
        $I->wait(2);
        $I->selectOption('#db_driver', "mysql");
        $dbs = $I->getDB_Settings();
        $I->fillField("#db_host", $dbs['DB_Hostname']);
        $I->fillField("s_db_user", $dbs['DB_Username']);
        $I->fillField("s_db_password", $dbs['DB_Password']);
        $I->fillField("#db_database", $dbs['DB_Database']);
        $I->wait(2);
        $I->click('Save');
        $I->wait(2);
        $I->click('Test Connection');
        $I->wait(2);
        $I->see("Connected to DB");
        $I->click("//a[@class='close-reveal-modal']");
    }

    public function linkReportConnection($I, $report)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->wait(2);
        $rowIndex = $I->findTableRowMatching(1, $report);
        $I->click('Edit', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->wait(2);
        $dbs = $I->getDB_Settings();
        $I->selectOption("#report_connection_id", "mysql:"
            . $dbs['DB_Database'] . "@" . $dbs['DB_Hostname']);
        $I->click('Save');
    }

    public function getFeedURL($I, $feed)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
        $I->wait(1);
        $rowIndex = $I->findTableRowMatching(1, $feed);
        $I->click('View', 'tbody tr:nth-child(' . $rowIndex . ')');
        return $I->grabAttributeFrom("//div[contains(text(),'Feed URL')]/following-sibling::div", "innerHTML");
    }
}
