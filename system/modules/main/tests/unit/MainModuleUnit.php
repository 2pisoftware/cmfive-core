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

        $admin = AdminService::getInstance($w);
        $auth = AuthService::getInstance($w);
        $channels = ChannelsService::getInstance($w);
        $favorite = FavoriteService::getInstance($w);
        $file = FileService::getInstance($w);
        $form = FormService::getInstance($w);
        $inbox = InboxService::getInstance($w);
        $main = MainService::getInstance($w);
        $report = ReportService::getInstance($w);
        $search = SearchService::getInstance($w);
        $tag = TagService::getInstance($w);
        $task = TaskService::getInstance($w);
        $timelog = TimelogService::getInstance($w);

        $this->assertSame(new AdminService($w), $admin);
        $this->assertSame(new AuthService($w), $auth);
        $this->assertSame(new ChannelsService($w), $channels);
        $this->assertSame(new FavoriteService($w), $favorite);
        $this->assertSame(new FileService($w), $file);
        $this->assertSame(new FormService($w), $form);
        $this->assertSame(new InboxService($w), $inbox);
        $this->assertSame(new MainService($w), $main);
        $this->assertSame(new ReportService($w), $report);
        $this->assertSame(new SearchService($w), $search);
        $this->assertSame(new TagService($w), $tag);
        $this->assertSame(new TaskService($w), $task);
        $this->assertSame(new TimelogService($w), $timelog);
    }
}
