<?php

class ReportModuleCest
{

    private $reportTitle = "Rhetoric Generator";
    private $reportFeed = "PullRhetoric";

    public function testReport($I)
    {
        $I->wantTo('Verify that reports can use templates, connections and feeds');
        $I->loginAsAdmin($I);
        $I->createReport($I, $this->reportTitle, 'Admin');
        $I->defineReportSQL($I, $this->reportTitle, "[[test||text||Test]]@@headers|| select 'known' as 'pedigree' , 'established' as 'precedent' @@ @@info||select distinct classname from migration @@");
        $I->requestReportWithData($I, $this->reportTitle, ['test' => 'Hello']);
        $I->see('known');
        $I->see('precedent');
        // $I->createFeed($I, $this->reportTitle, $this->reportFeed);
        // $feed = $I->getFeedURL($I, $this->reportFeed);
        // $path = parse_url($feed, PHP_URL_PATH);
        // $query = parse_url($feed, PHP_URL_QUERY);
        // $I->amOnPage($path . "?" . $query . "&format=html");
        // $I->See('known');
        // $I->See('precedent');
        $I->createTemplate(
            $I,
            'Test Template',
            'Report',
            'Templates',
            "<table width='100%' align='center' class='form-table' cellpadding='1'><tr><td colspan='2' style='border:none;'><img width='400' src='http://2pisoftware.com/wp-content/uploads/2014/02/logo-transparent-742x1901.png' style='width: 400px;' />d><td colspan='2' style='border:none; text-align:right;'>Software<br/>Millowine Ln, Bega, NSW 2550<br/>2pisoftware.com<br/>159945454<br/>42159945454</td>r>able> <br/><br/>	Pedigree is: {% for th in results['headers'] %}{{th}}<br/> {% endfor %}"
        );

        $I->attachTemplate($I, $this->reportTitle, 'Test Template');
        $I->runReportTemplate($I, $this->reportTitle, 'Test Template');
        $I->See('2pi');
        $I->See('Pedigree');
        $I->requestReportConnection($I);
        $I->linkReportConnection($I, $this->reportTitle);
        $I->runReportTemplate($I, $this->reportTitle, 'Test Template');
        $I->See('2pi');
        $I->See('Pedigree');

        $I->duplicateReport($I, $this->reportTitle);

        // Test the report forms - including new multicolform layout
        $report_name = "Test Report";
        $I->createReport($I, $report_name, "Report");
        $I->defineReportSQL($I, $report_name, "[[test||text||Test]] @@Report|| select * from report @@");
        $I->requestReportWithData($I, $report_name, ['test' => 'hello']);

        $multicol_report_name = "Test Report Multicol";
        $I->createReport($I, $multicol_report_name, "Report");
        $I->defineReportSQL($I, $multicol_report_name, "[[Test||[[[[test||text||Test]][[test2||text||Test 2]]]] @@Report|| select * from report @@");
        $I->requestReportWithData($I, $multicol_report_name, ['test' => 'hello', 'test2' => 'hello']);
    }
}
