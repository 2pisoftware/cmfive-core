<?php
namespace Tests\Support\Helper;

class CmfiveInboxModule extends \Codeception\Module
{

/**
 * Create a new inbox message as the logged in user
 */
    public function inboxCreateMessage($I, $to, $subject, $message) {
    	$I->clickCmfiveNavbar($I,'Inbox','Inbox');
    	$I->click('#createmessagebutton');
		$I->see('Send a message');
		$I->fillForm([
			'autocomplete:receiver_id'=>$to,
			'subject'=>$subject,
			'rte:message'=>$message
		]);
		$I->click('Send');
		$I->see('Message Sent');
    }

    /**
     * Count the number of messages in the inbox
     */
    public function countVisibleMessages($I,$listType='Inbox') {  // $listType can be New Messages(same as Inbox), Read Messages, Archive, Bin
    	$I->clickCmfiveNavbar($I,'Inbox',$listType);
    	return count($I->grabMultiple('.tablesorter tbody tr'));
    }
    /**
     * Search for a message with matching title
     *
     * @return boolean(false) || integer(row number)
     */
    public function findMessage($I,$title,$listType='Inbox') {  // $listType can be New Messages(Inbox), Read Messages, Archive, Bin
    	$I->clickCmfiveNavbar($I,'Inbox',$listType);
    	$row = $I->findTableRowMatching(2,$title);
    	if ($row===false) {
    		 $I->fail('Cannot see message matching - '.$title,' in '.$listType);
    	}
    	return $row;
    }
}