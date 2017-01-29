<?php
class inboxCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // auth details
	var $username='admin';
	var $password='admin';

	public function testInbox($I) {
		$I->login($I,$this->username,$this->password);
		
		// test validation for missing to address
		 $this->inboxCreateMessage($I,'','test message','content of test message');
		
		// test access restrictions
		$I->createUser($I,'inboxreader','password','inboxreader','jones','fred@jones.com');
		$I->setUserPermissions($I,'inboxreader',['user','inbox_reader']);
		$inboxreader = $I->haveFriend('inboxreader');
		$inboxreader->does(function(AcceptanceGuy $I) {
			$I->login($I,'inboxreader','password');
			$I->navigateTo($I,'inbox','Inbox');
			$I->dontSee('#createmessagebutton');
		});
		
		$I->navigateTo($I,'inbox','Inbox');
		// send myself some messages
		$this->inboxCreateMessage($I,'Administrator','test message','content of test message');
		$this->inboxCreateMessage($I,'Administrator','another test message','content of another test message');
		
		$row=$this->findMessage($I,'another test message');
		$context='.tablesorter tbody tr:nth-child('.$row.')';
		
		// view message and check that it moves to the Read list
		$I->click($context." a");
		$I->see('content of another test message');
		// is message now in read list
		$row=$this->findMessage($I,'another test message',"Read");
			
		// archive message and check that it moves to the Archive list
		$I->checkOption('.tablesorter tbody tr:nth-child('.$row.') input[type="checkbox"]');
		$I->click('#archivebutton');
		$row=$this->findMessage($I,'another test message',"Archive");
		
		// delete message and check that it moves to the Bin list
		$context='.tablesorter tbody tr:nth-child('.$row.')';
		$I->checkOption($context.' input[type="checkbox"]');
		$I->click('#deletebutton');
		$row=$this->findMessage($I,'another test message',"Bin");
		
		// really delete message and check that it is removed
		$context='.tablesorter tbody tr:nth-child('.$row.')';
		$I->checkOption($context.' input[type="checkbox"]');
		$I->click('#deleteforevorbutton');
		$I->assertTrue($this->countVisibleMessages($I,'Bin') == 0);
		
		
		// create a user fred, login and send a message to admin
		$I->createUser($I,'fred','password','fred','jones','fred@jones.com');
		$I->setUserPermissions($I,'fred',['user','inbox_reader','inbox_sender']);
		// system notifications
		$this->findMessage($I,'An account has changed','Inbox');
		
		$fred = $I->haveFriend('fred');
		$fred->does(function(AcceptanceGuy $I) {
			$I->login($I,'fred','password');
			$this->inboxCreateMessage($I,'Administrator','test message from fred','content of test message');
		});
		// does admin see the message
		$row=$this->findMessage($I,'test message from fred');
		// view message and reply
		$I->click('.tablesorter tbody tr:nth-child('.$row.') a');
		$I->click('#replybutton');
		// see prefilled reply user
		$user=$I->grabValueFrom('input[id="acp_receiver_id"]');
		$subject=$I->grabValueFrom('input[id="subject"]');
		$I->assertEquals(trim($user),"fred jones");
		// Re: in subject
		$I->assertEquals(trim($subject),"Re:test message from fred");
		$I->fillForm($I,[
			'rte:message'=>'thanks fred'
		]);
		$I->click('.savebutton');
		$fred->does(function(AcceptanceGuy $I) {
			$this->findMessage($I,'Re:test message from fred','Inbox');
		});
		
		// now test multiple archive delete pathway
		$this->inboxCreateMessage($I,'Administrator','tm1','tm1');
		$this->inboxCreateMessage($I,'Administrator','tm2','tm2');
		$this->inboxCreateMessage($I,'Administrator','tm3','tm3');
		$this->inboxCreateMessage($I,'Administrator','tm4','tm4');
		$this->inboxCreateMessage($I,'Administrator','tm5','tm5');
		$this->inboxCreateMessage($I,'Administrator','tm6','tm6');
		
		// multi archive
		$tm1=$this->findMessage($I,'tm1','Inbox');
		$tm3=$this->findMessage($I,'tm3','Inbox');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm1.') input[type="checkbox"]');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm3.') input[type="checkbox"]');
		$I->click('#archivebutton');
		$this->findMessage($I,'tm1','Archive');
		$this->findMessage($I,'tm3','Archive');
		
		// multi delete
		$tm2=$this->findMessage($I,'tm2','Inbox');
		$tm4=$this->findMessage($I,'tm4','Inbox');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm2.') input[type="checkbox"]');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm4.') input[type="checkbox"]');
		$I->click('#deletebutton');
		$this->findMessage($I,'tm2','Bin');
		$this->findMessage($I,'tm4','Bin');
		
		// mark all read
		$I->navigateTo($I,'inbox','Inbox');
		// disable dialog
		$I->executeJS('window.confirm = function(){return true;}');
		$I->click('#markallreadbutton');
		//$I->acceptPopup();
		$this->findMessage($I,'tm5','Read Messages');
		$this->findMessage($I,'tm6','Read Messages');
		
	}
	/**
	 * Create a new inbox message as the logged in user
	 */
	private function inboxCreateMessage($I,$to,$subject,$message) {
		$I->navigateTo($I,'inbox','Inbox');
		$I->click('#createmessagebutton');
		if (empty($to)) {
			$I->fillForm($I,[
				'subject'=>$subject,
				'rte:message'=>$message
			]);
			// disable dialog
			$I->executeJS('window.alert = function(){return true;}');
			$I->click('.savebutton');
			//$I->seeInPopup('You must enter a message recipient');
			//$I->cancelPopup();
		} else {
			$I->fillForm($I,[
				'autocomplete:receiver_id'=>$to,
				'subject'=>$subject,
				'rte:message'=>$message
			]);
			$I->click('.savebutton');
			$I->see('Message Sent');
		}
	}
	
	/**
	 * Count the number of messages in the inbox
	 */	 
	private function countVisibleMessages($I,$listType='Inbox') {  // $listType can be New Messages(same as Inbox), Read Messages, Archive, Bin
		$I->navigateTo($I,'inbox',$listType);
		return count($I->grabMultiple('.tablesorter tbody tr'));
	}
	/** 
	 * Search for a message with matching title 
	 * 
	 * @return boolean(false) || integer(row number)
	 */
	private function findMessage($I,$title,$listType='Inbox') {  // $listType can be New Messages(Inbox), Read Messages, Archive, Bin
		$I->navigateTo($I,'inbox',$listType);
		$row = $I->findTableRowMatching($I,2,$title);
		if ($row===false) {
			 $I->fail('Cannot see message matching - '.$title,' in '.$listType);
		}
        return $row;
	}
}
