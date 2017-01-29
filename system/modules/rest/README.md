#REST module

The rest module implements an HTTP interface to generic CRUD persistence. It can be used to search, save and delete records.

All endpoints in the rest API return JSON responses.

It is particularly useful in the development of ajax applications to load data subsets in the background.

Additional CmFive record based access controls canEdit, canDelete, canView are respected by requests to the API.

Validation rules on CmFive objects are supported and the save endpoint returns the errors as JSON.


##QuickStart

1. Configure the rest module in the global config file so that the object you want included is in the system.rest_allow array. 
>		// use the API_KEY to authenticate with username and password
>		Config::set('system.rest_api_key', "abcdefghijklmnopqrstuv");
>		Config::set('system.rest_allow', array(
>			"User",
>			"Contact"
>		));

2. To use the rest api in your module you will need a rest authentication token which can be retrieved from the by sending a GET request to 
`/rest/token/?api=<apikey>[&username=<username>&password=<password>]`

Username and password are not required if there is already a logged in user

This token needs to be appended to every subsequent request.

>		$.ajax(
>			"/rest/token?apikey=<?php echo Config::get("system.rest_api_key") ?>",
>			{cache: false,dataType: "json"}
>		).done(function(token) {
>		  // make requests here
>		});

3. Then, make requests to the API.
>		// SEARCH
>		$.ajax(
>			"/rest/index/WikiPage?token=" + token,
>			{cache: false,dataType: "json"}
>		).done(function(token) {
>			if (response.success && response.success.length > 0) {
>				// we got results
>				console.log(response.success);
>			}
>		});
>		// SAVE/DELETE
>		$.ajax(
>			"/rest/save/WikiPage?token=" + token,
>			// OR "/rest/delete/WikiPage?token=" + token,
>			var data={id: '<?php echo $obj->id ?>',body: '<?php echo $obj->body ?>'}
>			{cache: false,dataType: "json",method:"POST"}
>		).done(function(token) {
>			if (response.success > 0) {
>				// we got a result containing the updated record
>				console.log(response.success);
>			}
>		});


## REST API

- [] indicates an optional value
- <> indicate a required value

To get records send a GET request to 

`/rest/index/<classname>/[fieldname]/[value]?token=<authtoken>`

Fields marked deleted are excluded from the list request, to access all records regardless of deleted status use the deleted request.

`/rest/deleted/<classname>/[fieldname]/[value]?token=<authtoken>`

Both index and delete support advanced search criteria ie /rest/index/<classname>/<advanced criteria>?token=<authtoken>
where <advancecriteria> works as follows

- if it starts with /SKIP/<integer> search results are skipped according to the parameter
- if it starts with or skip is followed by /LIMIT/<integer> a limit is applied to the number of search results (by default 10)
- if AND or OR is found, a query group is created
	- END closes as sub group
	- otherwise config pairs are processed as <field__operator>, <data1__data2> until AND or OR is found or the end of the configuration
	- if AND or OR is found again, a sub query group is created
- if criteria pairs are found before AND or OR, the default condition is AND

eg
># fred and age 4-60
>/LIMIT/10/name___like/fred/age___between/40___60
># freds aged 0-20,80+ or jill
>/SKIP/10/LIMIT/10/AND/name___like/fred/OR/age__between/0___20/age___greater/80/END/name___like/jill
	 
----------------------------------------------

To delete records, send a POST request to 

`/rest/delete/<classname>/[id]?token=<authtoken>`

----------------------------------------------

To save records POST record data to 

`/rest/save/<classname>/?token=<authtoken>`

----------------------------------------------

## Dates and other data manipulations in the rest module
Cmfive provides automatic data conversions  when getObject or getObjects is called on a Service and when insert and update are called on a DbObject.

Fields are identified as date fields if their name starts with dt_, d_ or t_.

Date fields inside cmfive DbObjects are stored as timestamps. 

Records are stored in the database as MYSQL date types.

`getObject()` and `getObjects()` query timestamps from mysql and do no automatic conversions.

`$obj->fill($array,$doConversion)`  can be used with a true value for $doConversion to convert date strings back into timestamps.

`insert()` always converts values from timestamps to formatted dates for mysql date fields to inject into queries.
`update()` calls the updateConvert method which also converts values from timestamps to formatted dates  for mysql date fields.


In the rest module -

- All dates in the rest module are expected to be timestamps.
- Search request parameters are interpreted as timestamps.
- Search queries return timestamps inside the JSON success records.
- Save requests expect dates as timestamps inside the post data.
- Save responses return timestamps inside the JSON success records.

The timestamps for query responses are derived from mysql unix_timestamp(dt_dateField) 


