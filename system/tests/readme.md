# Install
* Ensure that Google Chrome is installed.
* Run `composer update` in the tests directory (the same directory as this readme)
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
replace 'http://localhost:5001' with the url of your site to be tested (including protocol)
below the -\Helper\WaitRunProcess line add the necessary commands to start your webserver and seed the database.

# Writing Tests
available actions can be found in support/_generated/CmfiveGuyActions.php. This file is regenerated every time codeception runs and includes both global codeception actions and custom actions defined by us.

# Running Tests
to
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
