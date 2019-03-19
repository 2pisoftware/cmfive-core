<?php


class ReportModuleCest
{
	
	private $reportTitle = "Rhetoric Generator";
	private $reportFeed = "PullRhetoric";

	public function testReport($I) {

		$I->wantTo('Verify that reports can use templates, connections and feeds');
		$I->loginAsAdmin($I); 
		$I->createReport($I, $this->reportTitle, 'Admin');
		$report_string = 
				// "				[[RUN|| ".
				//  "				select 1 as 'won' ]] ".
				"@@headers|| select 'known' as 'pedigree' , 'established' as 'precedent' @@ "
				. "@@info||select distinct classname from migration @@"	;	 
		 $I->defineReportSQL($I, $this->reportTitle, $report_string);  
		 $I->requestReport($I, $this->reportTitle);
		 $I->wait(2);
		 $I->see('known');
		 $I->see('precedent');
		 $I->createFeed($I,$this->reportTitle,$this->reportFeed);
		 $feed = $I->getFeedURL($I,$this->reportFeed);
		 $path = parse_url($feed)['path'];
		 $query = parse_url($feed)['query'];
		 $I->amOnPage($path."?".$query."&format=html");
		 $I->wait(2);
		 $I->See('known');
		 $I->See('precedent');
		 $I->createTemplate ($I,'Test Template','Report'
			,'Templates',
			"	  <table width='100%' align='center' class='form-table' cellpadding='1'>	"
			."	           	"
			."	            <tr>	"
			."	             <td colspan='2' style='border:none;'><img width='400' src='http://2pisoftware.com/wp-content/uploads/2014/02/logo-transparent-742x1901.png' style='width: 400px;' />	"
			."	            </td>	"
			."	             <td colspan='2' style='border:none; text-align:right;'>	"
			."	            2pi Software<br/>	"
			."	            1 Millowine Ln, Bega, NSW 2550<br/>	"
			."	            info@2pisoftware.com<br/>	"
			."	            ACN 159945454<br/>	"
			."	            ABN 42159945454	"
			."	             </td>	"
			."	            </tr>	"
			."	             	"
			."	            </table> <br/><br/>	Pedigree is:    "
			." 			{% for th in results['headers'] %}"
			."			   {{th}}<br/> {% endfor %}"
			); 
		
			$I->attachTemplate($I,$this->reportTitle,'Test Template');
			$I->runReportTemplate($I,$this->reportTitle,'Test Template');
			$I->See('2pi');
			$I->See('Pedigree'); 
		  $I->requestReportConnection($I);
		  $I->linkReportConnection($I,$this->reportTitle);
		  $I->runReportTemplate($I,$this->reportTitle,'Test Template');
		  $I->See('2pi');
			$I->See('Pedigree'); 
		//  $I->wait(10);

	}

}
