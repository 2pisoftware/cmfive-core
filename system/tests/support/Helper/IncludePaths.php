<?php
// Extension to avoid duplication of configuration when testing runModuleTests
// credit to SamMousa at https://github.com/Codeception/Codeception/issues/4868

namespace Helper;
use Codeception\Extension;
use Codeception\SuiteManager;
use Codeception\Test\Cest;
use Codeception\Test\Loader;

class IncludePaths extends Extension
{

    public static $events = [
        'suite.before' => 'beforeSuite'
    ];

    public function beforeSuite(\Codeception\Event\SuiteEvent $e)
    {
        /** @var SuiteManager $suiteManager */
        $suiteManager = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT + DEBUG_BACKTRACE_IGNORE_ARGS, 4)[3]['object'];
        $method = (new \ReflectionClass($suiteManager))->getMethod('addToSuite');
        $method->setAccessible(true);

        $settings = $e->getSettings();
        foreach($this->config as $k => $relativePath) {
            $absolutePath = $this->getRootDir() . $relativePath;
            $settings['path'] = $absolutePath;
            $loader = new Loader($settings);
            $loader->loadTests();
            /** @var Cest $test */
            foreach($loader->getTests() as $test) {
                $method->invoke($suiteManager, $test);
            }
        }
    }
}
