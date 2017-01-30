## Reports
### Working with reports
The reports module provides tools to develop and show summary reporting from a database system.

Report administrators can use a powerful combination of templating and sql queries to build reports.

The module provides fine grained access control by associating users with reports as described below under Report Members.

Users must have one of `report_admin`, `report_editor`, `report_user` roles to access this module.

All newly created reports, or edits of existing reports will be reviewed and approved by the Report Administrator prior to being made available to report_users.


### Report Dashboard
The reports dashboard lists reports that are available to the logged in user.

Where the user has permission, reports can be edited and deleted.

The report results can be accessed by clicking the report title in the list.
![Report Dashboard](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/report_dashboard.png)

### Creating a Report
Start by clicking the `Create a Report` menu item. You only need to provide a title.
When you click `Save Report`, additional tabs are shown to enter more details about the report.


#### Basics
The SQL tab provides a textarea to enter SQL queries that will be used in the report.

Report SQL lines are wrapped in double @ symbols allowing for multiple SQL statements.

Each SQL line contains multiple fields ending in a raw SQL query. Fields are seperated by double pipes (||). There must be at least two fields, a title and a sql query.

> @@All Contacts||SELECT id, concat(firstname, ' ', surname) as fullname, email FROM contact@@

The `View Database` tab may be useful in creating your SQL query.


#### Marker Replacements
Double curly brackets {{<fieldName>}} in the SQL query are replaced by report parameters and special values.

Special marker replacements include

- {{current_user_id}}	Will be substituted with the User ID of the person who is running the report.
- {{roles}}	Will be substituted with the list of roles of the person running the report.
- {{webroot}}	Will be substituted by the full web site URL.
- {{<parameter>}}	Will be substituted by the selected value of the parameter of the same name.



#### Parameters
The report can be configured to respond to parameters. Parameters are added as a block before the SQL lines surrounded by double square brackets in the format `[[fieldName||fieldType||fieldLabel]]`.
**This syntax means that you cannot use || in your SQL statements. Use OR instead. **

Field types include date, datetime, time, text, textarea, select, autocomplete, radio.
**Date and time fields must start with dt_, d_ or t_  **

` @@[[date_created||date||Created ]] || [[ date_modified||date||Modified]] | All Contacts||SELECT id, concat(firstname, ' ', surname) as fullname, email FROM contact where dt_created > {{date_created}}@@`

These parameters can be filled when the report is run.
![Report Parameters](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/report_parameters.png)

Select and autocomplete type fields can be populated with a sql query. Your SQL must rename the appropriate fields as `value` and `title`, for populating selectable options.

`[[status||select||Status||select distinct status as value, status as title from task order by status]] `

See Advanced Reports below for more details.


### Templates

By default a report is rendered in HTML table format listing all columns and rows.

The Templates tab allows multiple templates to be associated with the report which can be selected to override the layout and potentially collate the results.
![Report Templates List](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/report_templates_list.png)

Templates are applied to the HTML output when they are selected prior to running a report. 
They are also applied to the generation of PDF format reports that are available for download as a button once the report has run.
**Templates are not applied to XML or CSV export formats which render automatically and are not customisable.**

Templates can be checked as enabled for email. Email enabled templates are added as attachments when reports are bulk emailed.
![Report Templates List](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/report_template_edit.png)

A scheduler such as cron can be used to trigger the email action for regular reporting.

#### System Templates
You must first create templates to associate using the Template menu item in Admin. 

When you create a new template you must 

- check the template as active 
- fill the module field as `report`
- ![Report Template Details](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/report_template_edit_details.png)
- fill the Template Body field under the Template tab.
- ![Report Template ](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/report_template_edit_template.png)

The template processer uses the Twig language, you can find more information about this on the [Twig Website](http://twig.sensiolabs.org/).

A good first step when creating a new template, is to look at the data. You can use the following twig statement in your template to do this:

`{{dump(data)}}`


### Viewing a report

Report results can be accessed from the Report Dashboard or using the `Execute Report` button when editing a report.
If there are associated templates or report parameters, the user is given the option to make selections.

Report results are generated as HTML can be exported as CSV, PDF, XML using the buttons at the top. 



### Report Members
Users can be associated with a report as OWNER, EDITOR or USER.

Users cannot access a report unless explictly associated in one of these roles.

All roles enable a user to list and view a reports results.

Only report administrators and EDITORs can edit and approve reports.

Only report adminstrators and OWNERs can delete reports.

When generating reports by email, report members are the recipients of personalised report emails.

### Report connections
This module stores Database connection parameters to be used in reports. By default the site installation database connection is used.

### Feeds dashboard
Reports can be exported as feeds. The feeds dashboard allows management of exported feeds.
!! TODO

### Advanced queries

#### Counting Rows
You can use mysql variables to add a count column to your results. This will work with ordering and grouping.

> SELECT contact.*, @num := @num + 1 contact_order from contact, (SELECT @num := 0) d;

[More Information](http://www.xaprb.com/blog/2006/12/02/how-to-number-rows-in-mysql/)


#### Images


#### Hyperlinks

Links can be created for reports by selecting a field and an alias named as <realfield>_link filled with a concatenation of text and field values resulting in a link.

`concat('{{webroot}}','task/viewtask',id) as title_link, title ....`

**If using {{webroot}}, DO NOT include a leading '/' in your URL. **

These selections can be used in templates and are used in the default HTML and PDF templates.

**The default HTML and PDF templates require that the selected fields are named as above and will generate the link `<a href='http://yoursite.com/task/viewtask/54">Task Title</a>` in the report output  **

