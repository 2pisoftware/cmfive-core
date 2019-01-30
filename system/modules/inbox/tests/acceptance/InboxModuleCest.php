<?php
class InboxModuleCest
{

	public function testInbox($I) {

		
		$I->loginAsAdmin($I);
		$myFirstName = $I->getAdminFirstName();
		$myLastName = $I->getAdminLastName();
		$myFullName = $myFirstName." ".$myLastName;

		// test validation for missing to address
        $I->clickCmfiveNavbar($I,'Inbox','Inbox');
    	$I->click('#createmessagebutton');
        $I->see('Send a message');
		$I->click('.savebutton');
        $I->acceptPopup();

		// test access restrictions
		$I->createUser($I,'inboxreader','password','inboxreader','jones','fred@jones.com', ['user','inbox_reader']);

		$I->logout($I);
		$I->login($I,'inboxreader','password');
		$I->clickCmfiveNavbar($I,'Inbox','Inbox');
		$I->dontSee('#createmessagebutton');

		$I->logout($I);
        $I->loginAsAdmin($I);

		// send myself some messages
		$I->inboxCreateMessage($I,$myFullName,'test message','content of test message');
		$I->inboxCreateMessage($I,$myFullName,'another test message','content of another test message');

		$row=$I->findMessage($I,'another test message');
		$context='.tablesorter tbody tr:nth-child('.$row.')';

		// view message and check that it moves to the Read list
		$I->click($context." a");
		$I->see('content of another test message');
		// is message now in read list
		$row=$I->findMessage($I,'another test message',"Read Messages");

		// archive message and check that it moves to the Archive list
		$I->checkOption('.tablesorter tbody tr:nth-child('.$row.') input[type="checkbox"]');
		$I->click('#archivebutton');
		$row=$I->findMessage($I,'another test message',"Archive");

		// delete message and check that it moves to the Bin list
		$context='.tablesorter tbody tr:nth-child('.$row.')';
		$I->checkOption($context.' input[type="checkbox"]');
		$I->click('#deletebutton');
		$row=$I->findMessage($I,'another test message',"Bin");

		// really delete message and check that it is removed
		$context='.tablesorter tbody tr:nth-child('.$row.')';
		$I->checkOption($context.' input[type="checkbox"]');
		$I->click('#deleteforevorbutton');
		$I->assertTrue($I->countVisibleMessages($I,'Bin') == 0);


		// create a user fred, login and send a message to admin
		$I->createUser($I,'fred','password','fred','jones','fred@jones.com', ['user','inbox_reader','inbox_sender']);
		// system notifications
		$I->findMessage($I,'An account has changed','Inbox');
		$I->logout($I);
		$I->login($I,'fred','password');
		$I->inboxCreateMessage($I,$myFullName,'test message from fred','content of test message');

		$I->logout($I);
		$I->loginAsAdmin($I);
		// does admin see the message
		$row= $I->findMessage($I,'test message from fred');
		// view message and replytest message from fred
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

		$I->logout($I);
		$I->login($I,'fred','password');
		$I->findMessage($I,'Re:test message from fred','Inbox');

		$I->logout($I);
		$I->loginAsAdmin($I);
		// now test multiple archive delete pathway
		$I->inboxCreateMessage($I,$myFullName,'tm1','tm1');
		$I->inboxCreateMessage($I,$myFullName,'tm2','tm2');
		$I->inboxCreateMessage($I,$myFullName,'tm3','tm3');
		$I->inboxCreateMessage($I,$myFullName,'tm4','tm4');
		$I->inboxCreateMessage($I,$myFullName,'tm5','tm5');
		$I->inboxCreateMessage($I,$myFullName,'tm6','tm6');

		// multi archive
		$tm1=$I->findMessage($I,'tm1','Inbox');
		$tm3=$I->findMessage($I,'tm3','Inbox');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm1.') input[type="checkbox"]');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm3.') input[type="checkbox"]');
		$I->click('#archivebutton');
		$I->findMessage($I,'tm1','Archive');
		$I->findMessage($I,'tm3','Archive');

		// multi delete
		$tm2=$I->findMessage($I,'tm2','Inbox');
		$tm4=$I->findMessage($I,'tm4','Inbox');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm2.') input[type="checkbox"]');
		$I->checkOption('.tablesorter tbody tr:nth-child('.$tm4.') input[type="checkbox"]');
		$I->click('#deletebutton');
		$I->findMessage($I,'tm2','Bin');
		$I->findMessage($I,'tm4','Bin');

		// mark all read
		$I->clickCmfiveNavbar($I,'Inbox','Inbox');
		$I->skipConfirmation($I);
		$I->click('#markallreadbutton');
		//$I->acceptPopup();
		$I->findMessage($I,'tm5','Read Messages');
		$I->findMessage($I,'tm6','Read Messages');

	}
}
