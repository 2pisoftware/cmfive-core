<?php
$_SERVER['HTTPS'] = '';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = realpath(getenv('thisTestRun_testIncludePath'));


chdir(getenv('thisTestRun_testIncludePath'));
require_once "system".DIRECTORY_SEPARATOR."web.php";
require_once getenv('thisTestRun_testRunnerPath').DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."FileSystemTools.php";

