<?php
namespace Tests\Support\Helper;

class CmfiveReportModule extends \Codeception\Module
{
    public function createReport($I, $name, $module)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Create a Report');
        $I->fillField('#title', $name);
        $I->selectOption('#module', $module);
        $I->click('Save');
    }

    /**
     * Duplicates a report using $report_name.
     *
     * @param CmfiveUI $I
     * @param string $report_name
     * @return void
     */
    public function duplicateReport($I, $report_name)
    {
        $I->amOnPage("/report/index");
        $I->click("Duplicate", "tbody tr:nth-child(" . $I->findTableRowMatching(1, $report_name) . ")");
        $I->amOnPage("/report/index");
        $I->see($report_name . " - Copy");
    }

    public function defineReportSQL($I, $name, $SQL)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->click('SQL');
        $I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue(\"" . $SQL . "\")");
        // $I->executeJS("$('.CodeMirror').val(\"" . $SQL . "\");");
        $I->click("Save Report", '#code');
    }

    public function attachTemplate($I, $name, $template)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->click("//a[@href='#templates' and text()='Templates']");
        $I->click('Add Template');
        $I->selectOption("#template_id", $template);
        $I->selectOption("#type", 'HTML');
        $I->click('Save');
    }

    public function runReportTemplate($I, $name, $template)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->click($name, 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->selectOption("#template", $template);
        $I->click('Display Report');
    }

    public function requestReport($I, $name)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->click("//div[@id='report']/button");
    }

    public function requestReportWithData($I, $name, $data = [])
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $I->click('Edit', 'tbody tr:nth-child(' . $I->findTableRowMatching(1, $name) . ')');
        $I->click("//div[@id='report']/button");
        $I->fillForm($data);
        $I->click('.savebutton');
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
        $I->selectOption('#db_driver', "mysql");
        $dbs = $I->getDB_Settings();
        $I->fillField("#db_host", $dbs['DB_Hostname']);
        $I->fillField("s_db_user", $dbs['DB_Username']);
        $I->fillField("s_db_password", $dbs['DB_Password']);
        $I->fillField("#db_database", $dbs['DB_Database']);
        $I->click('Save');
        $I->click('Test Connection');
        $I->wait(2);
        $I->see("Connected to DB");
        $I->click("//a[@class='close-reveal-modal']");
    }

    public function linkReportConnection($I, $report)
    {
        $I->clickCmfiveNavbar($I, 'Report', 'Report Dashboard');
        $rowIndex = $I->findTableRowMatching(1, $report);
        $I->click('Edit', 'tbody tr:nth-child(' . $rowIndex . ')');
        $dbs = $I->getDB_Settings();
        $I->selectOption("#report_connection_id", "mysql:"
            . $dbs['DB_Database'] . "@" . $dbs['DB_Hostname']);
        $I->click('Save');
    }

    public function getFeedURL($I, $feed)
    {
        return $I->grabAttributeFrom("//*[@id='feedtext']/parent::*", 'innerHTML');
        // $I->clickCmfiveNavbar($I, 'Report', 'Feeds Dashboard');
        // $rowIndex = $I->findTableRowMatching(1, $feed);
        // $I->click('View', 'tbody tr:nth-child(' . $rowIndex . ')');
        // return $I->grabAttributeFrom("//div[contains(text(),'Feed URL')]/following-sibling::div", "innerHTML");
    }
}
