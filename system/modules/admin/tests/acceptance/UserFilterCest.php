<?php

class UserFilterCest
{
    private $lookupTitle = "Senior";
    private $username = "TypicalJolene";
    private $firstname = "Jolene";
    private $lastname = "Typical";
    private $username2 = "HondaJo";
    private $firstname2 = "Jo";
    private $lastname2 = "Honda";


    public function testUserFilter($I)
    {
        $I->wantTo('Search for users using filters');
        $I->login($I, 'admin', 'admin');
        $I->createUser(
            $I,
            $this->username,
            'password',
            $this->firstname,
            $this->lastname,
            $this->firstname . '@' . $this->lastname . '.com',
            ['user']
        );
        $I->createUser(
            $I,
            $this->username2,
            'password',
            $this->firstname2,
            $this->lastname2,
            $this->firstname2 . '@' . $this->lastname2 . '.com',
            ['user']
        );

        // Check that Filters find what they expect to find
        // Check Login
        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-login"]');
        $I->fillField('//*[@id="admin/user__filter-login"]', "Jol");
        $I->click("Filter");
        $I->see("{$this->firstname} {$this->lastname}");

        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-login"]');
        $I->fillField('//*[@id="admin/user__filter-login"]', "Jo");
        $I->click("Filter");
        $I->see("{$this->firstname} {$this->lastname}");
        $I->see("{$this->firstname2} {$this->lastname2}");

        // Check Name
        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-name"]');
        $I->fillField('//*[@id="admin/user__filter-name"]', "Typ");
        $I->click("Filter");
        $I->see("{$this->firstname} {$this->lastname}");

        // Check Email
        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-email"]');
        $I->fillField('//*[@id="admin/user__filter-email"]', "Jol");
        $I->click("Filter");
        $I->see($this->firstname . "@" . $this->lastname . ".com");

        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-email"]');
        $I->fillField('//*[@id="admin/user__filter-email"]', "d");
        $I->click("Filter");
        $I->see($this->firstname2 . "@" . $this->lastname2 . ".com");

        // Check combination of Login and Email
        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-login"]');
        $I->fillField('//*[@id="admin/user__filter-login"]', "Jo");
        $I->click('//*[@id="admin/user__filter-email"]');
        $I->fillField('//*[@id="admin/user__filter-email"]', "H");
        $I->click("Filter");
        $I->see($this->firstname2 . "@" . $this->lastname2 . ".com");
        $I->dontSee("{$this->firstname} {$this->lastname}");

        // Check that Filters don't find what they don't expect to find
        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-login"]');
        $I->fillField('//*[@id="admin/user__filter-login"]', "xxx");
        $I->click("Filter");
        $I->dontSee("{$this->firstname} {$this->lastname}");

        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-name"]');
        $I->fillField('//*[@id="admin/user__filter-name"]', "yyy");
        $I->click("Filter");
        $I->dontSee("{$this->firstname} {$this->lastname}");

        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-email"]');
        $I->fillField('//*[@id="admin/user__filter-email"]', "zzz");
        $I->click("Filter");
        $I->dontSee($this->firstname . "@" . $this->lastname . ".com");

        $I->click("Reset");
        $I->click('//*[@id="admin/user__filter-name"]');
        $I->fillField('//*[@id="admin/user__filter-name"]', "yyy");
        $I->click("Filter");
        $I->dontSee("{$this->firstname} {$this->lastname}");
        $I->click('//*[@id="admin/user__filter-email"]');
        $I->fillField('//*[@id="admin/user__filter-email"]', "Honda");
        $I->click("Filter");
        $I->dontSee($this->firstname2 . "@" . $this->lastname2 . ".com");

    }
}
