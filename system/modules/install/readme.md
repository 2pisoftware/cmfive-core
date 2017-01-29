# Install Module

## User Guide
The install module provides a user friendly way to set up a cmfive installation.
A wizard process asks for all necessary configuration details, creates a database and admin user if required and writes it all to a configuration file.

The install wizard is divided into seven steps

- General
- Timezone
- Database
- Tables
- Admin
- Email
- Complete


## Developer Guide

The default entry point for the install module is /install/install.actions.php.

Other entry points include

- general
- timezone
- database
- tables
- admin
- email




In the models folder there are a number of services and an object InstallStep.

- InstallStep
- ConfigService provides functions to manage the rendering and staging of the cmfive site configuration file.
- ValidationService captures validation rules per install step and provides validation support functions.
- InstallDatabaseService
- InstallService

The cmfive configuration file is generated from a Twig template stored in the assets folder.


In the assets folder there are a number of javascript libraries and install.js which provides javascript helper functions specific to the install too.
