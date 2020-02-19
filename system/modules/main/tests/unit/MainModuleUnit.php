<?php

class MainModuleUnit extends \Codeception\Test\Unit
{
    public function runUnitTests()
    {
        $this->testSingleton();
    }

    private function testSingleton()
    {
        $w = new Web();

        $admin1 = AdminService::getInstance($w);
        $auth1 = AuthService::getInstance($w);
        $channels1 = ChannelsService::getInstance($w);
        $favorite1 = FavoriteService::getInstance($w);
        $file1 = FileService::getInstance($w);
        $form1 = FormService::getInstance($w);
        $inbox1 = InboxService::getInstance($w);
        $main1 = MainService::getInstance($w);
        $report1 = ReportService::getInstance($w);
        $search1 = SearchService::getInstance($w);
        $tag1 = TagService::getInstance($w);
        $task1 = TaskService::getInstance($w);
        $timelog1 = TimelogService::getInstance($w);

        $admin2 = AdminService::getInstance($w);
        $auth2 = AuthService::getInstance($w);
        $channels2 = ChannelsService::getInstance($w);
        $favorite2 = FavoriteService::getInstance($w);
        $file2 = FileService::getInstance($w);
        $form2 = FormService::getInstance($w);
        $inbox2 = InboxService::getInstance($w);
        $main2 = MainService::getInstance($w);
        $report2 = ReportService::getInstance($w);
        $search2 = SearchService::getInstance($w);
        $tag2 = TagService::getInstance($w);
        $task2 = TaskService::getInstance($w);
        $timelog2 = TimelogService::getInstance($w);

        $this->assertSame($admin1, $admin2);
        $this->assertSame($auth1, $auth2);
        $this->assertSame($channels1, $channels2);
        $this->assertSame($favorite1, $favorite2);
        $this->assertSame($file1, $file2);
        $this->assertSame($form1, $form2);
        $this->assertSame($inbox1, $inbox2);
        $this->assertSame($main1, $main2);
        $this->assertSame($report1, $report2);
        $this->assertSame($search1, $search2);
        $this->assertSame($tag1, $tag2);
        $this->assertSame($task1, $task2);
        $this->assertSame($timelog1, $timelog2);
    }
}
