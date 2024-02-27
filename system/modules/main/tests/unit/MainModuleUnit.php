<?php
use PHPUnit\Framework\TestCase;

class MainModuleUnit extends TestCase //\Codeception\Test\Unit
{
    public function testSingleton()
    {
        require_once("system/web.php");

        $w = new Web();
        $w->initDB();

        $admin1 = AdminService::getInstance($w);
        $auth1 = AuthService::getInstance($w);
        $channels1 = ChannelsService::getInstance($w);
        $favourite1 = FavoriteService::getInstance($w);
        $file1 = FileService::getInstance($w);
        $form1 = FormService::getInstance($w);
        $main1 = MainService::getInstance($w);
        $report1 = ReportService::getInstance($w);
        $search1 = SearchService::getInstance($w);
        $tag1 = TagService::getInstance($w);
        $task1 = TaskService::getInstance($w);
        $timelog1 = TimelogService::getInstance($w);

        // To test that the instances are the same object we need to
        // change the properties of one and assert the other has this
        // change reflected.

        // Also testing backwards compatibility with Web::__get
        $this->assertNotNull($admin1->_db);
        $admin2 = AdminService::getInstance($w);
        $admin1->_db = null;
        $this->assertNull($admin2->_db);
        $this->assertNull(AdminService::getInstance($w)->_db);

        $this->assertNotNull($auth1->_db);
        $auth2 = AuthService::getInstance($w);
        $auth1->_db = null;
        $this->assertNull($auth2->_db);
        $this->assertNull(AuthService::getInstance($w)->_db);

        $this->assertNotNull($channels1->_db);
        $channels2 = ChannelsService::getInstance($w);
        $channels1->_db = null;
        $this->assertNull($channels2->_db);
        $this->assertNull(ChannelsService::getInstance($w)->_db);

        $this->assertNotNull($favourite1->_db);
        $favourite2 = FavoriteService::getInstance($w);
        $favourite1->_db = null;
        $this->assertNull($favourite2->_db);
        $this->assertNull(FavoriteService::getInstance($w)->_db);

        $this->assertNotNull($file1->_db);
        $file2 = FileService::getInstance($w);
        $file1->_db = null;
        $this->assertNull($file2->_db);
        $this->assertNull(FileService::getInstance($w)->_db);

        $this->assertNotNull($form1->_db);
        $form2 = FormService::getInstance($w);
        $form1->_db = null;
        $this->assertNull($form2->_db);
        $this->assertNull(FormService::getInstance($w)->_db);

        // $this->assertNotNull($inbox1->_db);
        // $inbox1->_db = null;
        // $this->assertNull($inbox2->_db);

        $this->assertNotNull($main1->_db);
        $main2 = MainService::getInstance($w);
        $main1->_db = null;
        $this->assertNull($main2->_db);
        $this->assertNull(MainService::getInstance($w)->_db);

        $this->assertNotNull($report1->_db);
        $report2 = ReportService::getInstance($w);
        $report1->_db = null;
        $this->assertNull($report2->_db);
        $this->assertNull(ReportService::getInstance($w)->_db);

        $this->assertNotNull($search1->_db);
        $search2 = SearchService::getInstance($w);
        $search1->_db = null;
        $this->assertNull($search2->_db);
        $this->assertNull(SearchService::getInstance($w)->_db);

        $this->assertNotNull($tag1->_db);
        $tag2 = TagService::getInstance($w);
        $tag1->_db = null;
        $this->assertNull($tag2->_db);
        $this->assertNull(TagService::getInstance($w)->_db);

        $this->assertNotNull($task1->_db);
        $task2 = TaskService::getInstance($w);
        $task1->_db = null;
        $this->assertNull($task2->_db);
        $this->assertNull(TaskService::getInstance($w)->_db);

        $this->assertNotNull($timelog1->_db);
        $timelog2 = TimelogService::getInstance($w);
        $timelog1->_db = null;
        $this->assertNull($timelog2->_db);
        $this->assertNull(TimelogService::getInstance($w)->_db);

        // AssertSame will check that they're the same type but
        // not that they are the exact same object (i.e. point at
        // the same piece of memory)
        // $this->assertSame($admin1, $admin2);
        // $this->assertSame($auth1, $auth2);
        // $this->assertSame($channels1, $channels2);
        // $this->assertSame($favorite1, $favorite2);
        // $this->assertSame($file1, $file2);
        // $this->assertSame($form1, $form2);
        // $this->assertSame($inbox1, $inbox2);
        // $this->assertSame($main1, $main2);
        // $this->assertSame($report1, $report2);
        // $this->assertSame($search1, $search2);
        // $this->assertSame($tag1, $tag2);
        // $this->assertSame($task1, $task2);
        // $this->assertSame($timelog1, $timelog2);
    }
}
