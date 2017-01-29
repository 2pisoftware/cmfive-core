# File Module

The file module features for the uploading, managing and accessing files.

The module has two roles that control access. 

- Users with the `file_upload` role are able to attach edit and delete files.
- Users with the `file_download` role are able to access files that have been uploaded

The module allows for different storage layers. By default only file system storage is available. Alternatives include memory storage and amazon s3 storage. Dropbox and google storage services are under development.

By default only the local storage module is enabled. To enable Amazon S3 storage you need to configure the adapter by adding configuration value.

>Config::set('file.adapters' => [
>		's3' => [
>			'active' => true,
>			'key' => '',
>			'secret' => '',
>			'bucket' => '',
>			'options' => []
>		],
>		'local' => [
>			'active' => true
>		]
>	]
>)


The file module is visible as the attachments tab for many types of records through the use of partials.

The module provides two partials that can be used to manage files in other modules. The listattachments partial lists all documents and provides view, edit and delete links for each file. The listattachmentsplain partial lists all documents without links.

The admin module contains a subaction for file which allows moving files between storage layers. [??? Is there a way to keep the file module intact but leave the menu item in admin ??]

The file module provides the following actions that can be used for file management.

- GET /file/atdel/<attachment id>/<redirect url>   - delete an attachment 
- GET /file/delete - delete an attachment   ???????? IS THIS DEPRECATED IN FAVOR OF ATDEL ?? 
- GET /file/atfile/<attachment id>  - deliver the content of an attachment
- GET /file/atthumb/<attachment id> - deliver the content of a thumbnail for the attachment, generating it if required
- GET /file/thumb - deliver the content of an attachment (without thumbnail caching)   ???????? IS THIS DEPRECATED 
- GET /file/attach/<object type>/<object id>/<redirect url>   - show form for attaching a file
- POST /file/attach -- upload a file and create an attachment record associated with an object
- GET /file/edit - show an editing form for title and description of an attachment
- POST /file/edit - save title and description for an attachment
- GET new - show a form for a new attachment
- POST new - save a new attachment
- GET /file/path/<path to file>  - deliver the content of a file based on a path
- GET /file/printview/<object type>/<object id>  - return HTML table formatted list of attachments for a given object


Thumbnail images are generated in the /cache/..... folder on demand by requests to the atthumb action.
