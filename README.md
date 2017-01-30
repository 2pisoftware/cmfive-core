# Cmfive

## The core code base for Cmfive

Cmfive is a modular PHP framework for creating robust and extensible web applications.

### Installation

First, grab the latest version of the [cmfive-boilerplate repository](https://github.com/2pisoftware/cmfive-boilerplate/archive/master.zip). This will contain the folder structure required by Cmfive. Then clone or download this repository and then create a symbolic link from the system folder in this repo into the base folder of the cmfive-boilerplate project.

Your folder layout should look like this:
 * cache
 * lib
 * log
 * modules
 * _system_
 * uploads
 * index.php, etc
 
#### Creating a symbolic link on Unix/OSX
```
~# ln -s /path/to/cmfive-core/system /path/to/cmfive-boilerplate/
```
#### Creating a symbolic link on Windows
```
> mklink /D /path/to/cmfive-boilerplate/system /path/to/cmfive-core/system
```

Once that is set up, point Apache, Nginx or IIS (others will probably work as well) at the root directory. Ensure a MySQL user/password/database has been set up, then access the installer. You will then be guided throught the rest of the install process.

Once you've finished installing Cmfive, log in to the system. Once you're in, keep in mind you will want to run any of your project modules' migrations, as the installer is unable to do so.

### A deeper look at the structure of Cmfive and the modules, and how they work

#### The modules
A typical module layout will look like this (bold entries are required):
* **actions**
* assets
* **install**
 * **migrations**
* **models**
* **templates**
* **config.php**

##### Actions
An action is a function that is executed from web(.php) as a result of matching the current URL against the modular structure of cmfive. The path of a URL consists of:
```
[HEAD|GET|POST] https://localhost/<module>/<action>
or
[HEAD|GET|POST] https://localhost/<module>-<submodule>/<action>
```

A submodule is just another folder inside the actions folder and serves primarily as a way to organise multiple actions in a module.

The function in the action follows the following naming convention:
```php
<?php

function <action name>_<verb>(Web $w) { // Where "verb" is either HEAD, GET or POST

}
```
e.g:
```php
<?php

function listsongs_GET(Web $W){
	$songs = $w->Music->getSongs();
	
	// Do something with songs
	
	$w->ctx('song_list', $songs); // ctx() exposes the $songs to the template now as the variable "$song_list"
}
```

##### Assets

A place to keep static assets, can be called anything that suits you.

##### Install

The install folder can be used to keep report code and templates, but its main purpose is to house the migrations and database seeds. Inside the install folder needs to be a folder called "migrations". To create a new migration, goto admin -> migrations -> Individual (tab). Go to your module in the list and click "Create a new migration". Enter a name for the migration and click save. See migrations in the system/modules folder for easy ways to create migrations (documentation coming soon).

##### Models

The models folder is used to store Cmfives database ORM objects called "DbObject"(s). A DbObject class name should relate to it's matching table name, but without underscores, and camel cased. E.g:

| Table | DbObject |
|-------|----------|
|user   | User     |
|task_group_member|TaskGroupMember|

Thsi conversion is done automatically by Cmfive, but you can override this by setting by adding the static propery "$\_db_table" to your DbObject and setting it to the name of the responsible table.

The models folder can also store Service classes, called "DbService". These classes provide a global interface to your module via the Web class. Generally, each module has at least one, named after the module itself, e.g. if your module was called "music":
```php
<?php

class MusicService extends DbService { // The "Service" suffix is required
	public function getSong($id) {
		return $this->getObject("Song", $id);
	}
	
	public function getSongs() {
		return $this->getObjects("Song", ['is_deleted' => 0]);
	}
}
```

Everything in the models folder gets autoloaded so all you need to do to invoke this function from anywhere there is an instance of Web, is to call:
```php
$my_song = $w->Music->getSong($the_id);
```

Any other classes that you want autoloaded, like generic interfaces, static helper classes etc., can be put in the models folder.

##### Templates

Templates act as a compliment to an action. For Cmfive to match a tempalte to an action, it should follow the same submodule layout as its action counterpart and follow this naming convention:
```
<action name>.tpl.php
```
e.g.
```html
<!-- /music/templates/listsongs.tpl.php -->
<ul>
	<?php foreach($song_list as $song): ?>
		<li><?php echo $song; ?></li>
	<?php endforeach; ?>
</ul>
```

##### config.php

The most cruical part to a module, Cmfive first looks for a config file to load the module, if this is missing then your module won't be used at all by Cmfive. The config.php file uses a static class called Config which is essentially a key value store. A module config requires three values, here is a full example to explain each one:
```php
<?php

Config::set('music', [
	'active' 	=> true,		// the active flag lets us disable modules that we don't want to use
	'path'		=> 'modules',	// tells Cmfive exactly where this module can be found (Config values are cached)
	'topmenu'	=> 'My music'	// Set to false to not show in the top menu, or set to true to infer the menu name from the name of the module (in this case "Music")
]);

```
