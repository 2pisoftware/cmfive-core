Install
ensure that you have Google Chrome installed.
run 'composer update' in the tests directory (the same directory as this readme)
create a file called 'acceptance.suite.yml' with the following content:
'''
modules:
  enabled:
    - WebDriver:
        url: http://localhost:5001
        browser: chrome
        port: 9515
     - \Helper\WaitRunProcess:
        - commmand to start webserver
        - command to clean and seed the database
'''
replace 'http://localhost:5001' with the url of your site to be tested (including protocol)
below the -\Helper\WaitRunProcess line add the necessary commands to start your webserver and seed the database.

How to write tests:
available actions can be found in the support/_generated/CmfiveGuyActions.php. This file is regenerated every time codeception runs and includes both global codeception actions and custom actions defined by us.

Running tests
failed tests: -g failed

Configuration
codeception has a hierarcial configuration system.

  suite.yml -> suite.dist.yml -> codeception.yml -> codeception.dist.yml


Debugging

Todo:
two tests
tests from other modules
docs
ci
override click to check for warnings and notices


Possible expansion:
get a list of the slowest tests, as seen in https://github.com/johnkary/phpunit-speedtrap
