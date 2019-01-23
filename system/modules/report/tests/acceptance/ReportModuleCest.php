<?php


class ReportModuleCest
{
	
	private $reportTitle = "Rhetoric Generator";
	private $reportFeed = "PullRhetoric";

	public function testReport($I) {

		$I->wantTo('Verify that reports can use connections and feeds');
		$I->loginAsAdmin($I); 
		$I->createReport($I, $this->reportTitle, 'Admin');
		$report_string = 
		"@@HEADER|| select 'known' as 'pedigree' , 'established' as 'precedent' @@ "
		. "@@INFO||select distinct classname from migration @@"	;	 
		 $I->defineReportSQL($I, $this->reportTitle, $report_string);  
		 $I->requestReport($I, $this->reportTitle);
		 $I->wait(2);
		 $I->see('known');
		 $I->see('precedent');
		 $I->createFeed($I,$this->reportTitle,$this->reportFeed);
		 $I->amOnPage(parse_url($I->getFeedURL($I,$this->reportFeed))['path']."&format=html");
		 $I->wait(2);
		 $I->See('known');
		 $I->See('precedent');
		 $I->amOnPage('/admin');
		 $I->requestReportConnection($I);
		 $I->wait(10);
		
		 
	}

}
