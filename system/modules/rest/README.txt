REST module.

[] indicates an optional value

<> indicate a required value

To start, you need an authentication token which can be retrieved from the by sending a GET request to token

/rest/token/?api=<apikey>[&username=<username>&password=<password>]

Username and password are not required if there is already a logged in user

This token needs to be appended to every subsequent request.

----------------------------------------------

To get a single record send a GET request to 

/rest/index/<classname>/id/<id>?token=<authtoken>

----------------------------------------------

To search/retrieve multiple records send a GET request to 

/rest/index/<classname>/[fieldname]/[value]?token=<authtoken>

Fields marked deleted are excluded from the list request, to access all records regardless of deleted status use the deleted request.

/rest/deleted/<classname>/[fieldname]/[value]?token=<authtoken>

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
# fred and age 4-60
/LIMIT/10/name___like/fred/age___between/40___60
# freds aged 0-20,80+ or jill
/SKIP/10/LIMIT/10/AND/name___like/fred/OR/age__between/0___20/age___greater/80/END/name___like/jill
	 
----------------------------------------------

To delete records, send a POST request to 

/rest/delete/<classname>/[id]?token=<authtoken>

----------------------------------------------

To save records POST record data to 

/rest/save/<classname>/?token=<authtoken>

