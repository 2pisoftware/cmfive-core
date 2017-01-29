<?php

/**
 * Some composer packages require git installed before it can clone.
 * Ensure git is installed and properly configured to be run as 'git' from CLI
 */

//ini_set('memory_limit', '512M');
//ini_set('max_execution_time', 300);
//
//define('EXTRACT_DIRECTORY', SYSTEM_PATH . "/composer");
//
//if (file_exists(EXTRACT_DIRECTORY.'/tmp/vendor/autoload.php') !== true) {
//    ini_set("phar.readonly", 0);
//    $composerPhar = new Phar(SYSTEM_PATH . "/composer.phar");
//    //php.ini setting phar.readonly must be set to 0
//    $composerPhar->extractTo(EXTRACT_DIRECTORY . "/tmp");
//}
//
////This requires the phar to have been extracted successfully.
//require_once (EXTRACT_DIRECTORY.'/tmp/vendor/autoload.php');
//
////Use the Composer classes
//use Composer\Console\Application;
//use Symfony\Component\Console\Input\ArrayInput;
//use Composer\Command\UpdateCommand;
//use Symfony\Component\Console\Output\StreamOutput;

function composer_ALL(Web $w) {
//    echo "<pre>".file_get_contents(ROOT_PATH . '/log/composer.log') . "</pre>";
    // Collect dependencies
    $dependencies_array = array();
    foreach($w->modules() as $module) {
        $dependencies = Config::get("{$module}.dependencies");
        if (!empty($dependencies)) {
            $dependencies_array = array_merge($dependencies, $dependencies_array);
        }
    }
    
    $json_obj = array();
    $json_obj["config"] = array();
    $json_obj["config"]["vendor-dir"] = 'composer/vendor';
    $json_obj["config"]["cache-dir"] = 'composer/cache';
    $json_obj["config"]["bin-dir"] = 'composer/bin';
    $json_obj["require"] = $dependencies_array;

    // Need to change dir so composer can find the json file
    // chdir(SYSTEM_PATH);
    
    // Create the JSON file
    file_put_contents(SYSTEM_PATH . "/composer.json", json_encode($json_obj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

	$w->out("Created composer.json file: <pre>" . file_get_contents(SYSTEM_PATH . "/composer.json") . "</pre>");
	
    //Create the commands
//    $input = new ArrayInput(array('command' => 'update', '--prefer-dist' => 'true'));
//    $filestream = new StreamOutput(fopen(ROOT_PATH . '/log/composer.log', 'w'));
//    
//    //Create the application and run it with the commands
//    $application = new Application();
//    $exitcode = $application->run($input, $filestream);
//    // Change dir back to root
//    chdir(ROOT_PATH);
//    
//    // This doesn't happen for some reason
//    $w->msg("Composer update return exit code " . $exitcode . " (0 is OK)<br/>Check the /log/composer.log for output", "/admin");
}
