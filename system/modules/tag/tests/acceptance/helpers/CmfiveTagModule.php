<?php

namespace Tests\Support\Helper;

class CmfiveTagModule extends \Codeception\Module
{

    public function createTag($I, $tag)
    {

        $I->click("//div[@class='tag_container']");
        $I->wait(2);
        $I->click("//div[@class='selectize-control small-12 columns multi plugin-remove_button plugin-optgroup_columns']");
        $I->fillField("//div[@class='selectize-control small-12 columns multi plugin-remove_button plugin-optgroup_columns']/div/input", $tag); //input[@id='display_tags_Task_1-selectized']",$tag); 
        $I->click("//div[@class='selectize-dropdown-content']");
        $I->click("//a[@class='close-reveal-modal']");
        $I->wait(4);
    }

    public function deleteTag($I, $tag)
    {
        $I->clickCmfiveNavbar($I, 'Tag', 'Tag Admin');
        $row = $I->findTableRowMatching(1, $tag);
        $I->click('Delete', 'tbody tr:nth-child(' . $row . ')');
        $I->acceptPopup();
        $I->wait(2);
    }

    public function editTag($I, $tag, $change)
    {
        $I->clickCmfiveNavbar($I, 'Tag', 'Tag Admin');
        $row = $I->findTableRowMatching(1,  $tag);
        $I->click('Edit', 'tbody tr:nth-child(' . $row . ')');
        $I->fillField("tag", $change);
        $I->click("Save");
        $I->click("Back to Tag list");
        $I->wait(2);
    }


    public function detachTag($I, $tag)
    {

        $I->click("//div[@class='tag_container']");
        $I->wait(2);
        $I->click("//div[@class='selectize-control small-12 columns multi plugin-remove_button plugin-optgroup_columns']");
        $I->click("//div[contains(text(),'" . $tag . "')]/a");
        $I->click("//a[@class='close-reveal-modal']");
        $I->wait(4);
    }

    public function reattachTag($I, $tag)
    {

        $I->click("//div[@class='tag_container']");
        $I->wait(2);
        $I->click("//div[@class='selectize-control small-12 columns multi plugin-remove_button plugin-optgroup_columns']");
        $I->click("//div[@class='option' and contains(text(),'" . $tag . "')]");
        $I->click("//a[@class='close-reveal-modal']");
        $I->wait(4);
    }
}
