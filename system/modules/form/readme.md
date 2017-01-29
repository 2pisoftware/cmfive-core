# Generic Forms Module


The `form` module provides a UI to create forms with customised fields. Forms can be attached to other record editing views as additional tabs providing power users the ability to extend the database model for their needs.


## Forms Administration User Guide
The form module is available as a top level item in the cmfive menu. On the index page is a list of forms that have been created and links to add, edit and delete forms.  
[Forms List](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/forms_list.png)

The title of the form is linked to a form view that allows management of form fields and other details.

In the fields tab, a list of fields is shown and fields can be added, edited and deleted.
Fields can be ordered by dragging and dropping the handle at the left of the list.
[Form Fields](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/form_fields.png)

In the mapping tab, you can choose what objects this form is enabled for. 
[Form Mapping](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/form_mapping.png)

The row and summary templates tabs allow customisation of the form layout.
The template ....

Having created a form and associated it with a record type, it is now possible to view that record type and see additional tabs for managing the data stored in those forms.
[Form Mapping](https://raw.githubusercontent.com/2pisoftware/cmfive/0-8-0-BRANCH/doc/wiki/form_tasks.png)


## Forms Module Developer Guide

The forms module has no roles defined and is only available to `admin` users.

To enable objects to be mapped to forms configuration is required.
eg 
> Config::append('form.mapping', [
> 	'Task', 'TaskGroup'
> ]);


To show forms in a tab based view, call the hooks 
> // to append tab headers for each form associated with this record type.
> $tab_headers = $w->callHook('core_template', 'tab_headers', $task);   
> // to append tabs to the layout with list/edit/delete features for each related form
> $tab_content = $w->callHook('core_template', 'tab_content', ['object' => $task, 'redirect_url' => '/task/edit/' . $task->id]); 


There are a number of objects used to represent forms and form data.

- records that describe the forms
	- Form - represents the custom form that can be attached to records
	- FormField - represents a field in a custom form
	- FormFieldMetadata - additional information that defines a form field eg decimal places for decimal field types
	- FormFieldInterface - abstract class defining methods required to implement a form field
	- FormStandardInterface - standard base implementation of FormFieldInterface providing text, decimal, date and datetime field types
	- FormMapping - represents associations between forms and record types that they are enabled for.
- and data submitted into form records is stored in
	- FormInstance - represents a form that has been saved
	- FormValue - a form value stores the actual for each field of a form instance.

The FormService class provides a range of lookup functions for Form records.

Currently neither the forms nor the data stored in the forms is searchable although this could be implemented with an appropriate addToIndex function for FormValue to enable searching the data entered into forms. Generating the links to the appropriate view/edit pages will be the hardest bit.


