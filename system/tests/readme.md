# Install
* Ensure that Google Chrome is installed.
* Ensure 'composer.phar' exists in the tests directory (the same directory as this readme)
* Run `php composer.phar update` in the tests directory (the same directory as this readme)
* Create a file called 'acceptance.suite.yml' with the following content:
```
modules:
  enabled:
    - WebDriver:
        url: http://localhost:5001
        browser: chrome
        port: 9515
extensions:
  enabled:
    - \Helper\WaitRunProcess:
        - command to start webserver
        - command to wipe database

```
1) replace 'http://localhost:5001' with the url of your site to be tested (including protocol)
2) replace the port with appropriate listener, see 'tests\services':
      ---------------------------------------------------
          Follow Installation Instructions 
          --> https://codeception.com/docs/modules/WebDriver#Selenium
          --> https://codeception.com/docs/modules/WebDriver#ChromeDriver
          Enable RunProcess extension to start/stop Selenium automatically (optional).

Check versions in tests/services:
Launch ChromeDriver = defaults to 9515
Launch Selenium JAR = defaults to 4444 (Selenium can find/launch ChromeDriver automatically if they are in same folder)
      ---------------------------------------------------
3) below the -\Helper\WaitRunProcess line add the necessary commands to start your webserver and seed the database. eg: - \Helper\WaitRunProcess: 
        - cd C:\cm5\cmfive-boilerplate && php cmfive.php install migrations 

# Writing Tests
available actions can be found in support/_generated/CmfiveGuyActions.php. This file is regenerated every time codeception runs and includes both global codeception actions and custom actions defined by us.

# Running Tests
see --> https://codeception.com/docs/reference/Commands

to run acceptance, eg:
  - WindowsPC -> folders[...]system/tests
  - GitBash terminal (or Windows terminal, GitBash better for colour display&formatting!)
  - "vendor/bin/codecept run acceptance --steps"
to run just failed tests: -g failed

# Configuration
codeception has a hierarcial configuration system.

  suite.yml -> suite.dist.yml -> codeception.yml -> codeception.dist.yml
dist configuration files are commitited  to git and contain generic common configuration. non dist configuration files are not commited to git and contain configuration specific to a developer's setup

# Debugging
## Workaround for uninteractable Autocomplete inputs
Call the jquery autocomplete function manually
`$this->executeJS("$('element locator').autocomplete('search', 'search term')")`
not sure but the root cause of this issue is, but this workaround is good enough
## Mark a test as skipped
Add '$scenario->skip();' to the body of the test's function. Useful for incomplete or failing tests that you don't currently want to run.
## Pause running a test
run with --debug
add $I->pauseExecution();

# Todo:
tests from other modules
ci
override click to check for warnings and notices


# Possible expansion:
get a list of the slowest tests, as seen in https://github.com/johnkary/phpunit-speedtrap
the WaitRunProcess extension is unaware of skipped tests
mailcatcher to test emails

# Where I got to with composer and docker:

'''
version: '3'
services:
  codecept:
    image: codeception/codeception
    depends_on:
      - chrome
      # - web
    volumes:
      - ./system/tests:/project
    ports:
      - 9515
  chrome:
    image: selenium/standalone-chrome-debug:3.12
    ports:
      - 4444
      - 5900
  web:
    image: php:7-apache
  #   depends_on:
  #     - db
    ports:
      - "80:80"
    volumes:
      - .:/var/www/html
'''
