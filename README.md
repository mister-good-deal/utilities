# PHP utilities

This repository contains the followings features / classes

###IniFileManager class

* parse ini conf file
* get parameter from ini file
* set parameter to ini file
* set section comment to a ini file
* set param comment to a ini file
* regenerate ini file with pretty indent / align

*code example:*

```php
Ini::getSectionParams('my ini section'); // return an array containing all the sections params
Ini::getParam('my ini section', 'my param'); // return the param
Ini::setParam('my ini section', 'my param', 'my value'); // set the param in the ini file
Ini::setParamComment('my ini section', 'my param', 'my comment'); // set the param comment in the ini file
```

***

###File and console logger class

* to log text in a file or/and in a console with pretty output (colors etc)

***

###Entity / Manager design pattern abstract class

* simply create entities based on INI conf files
* support foreign keys set up with onDelete, onUpdate clauses
* simply manage entities with basic SELECT / DELETE / UPDATE
* simply CREATE / DROP tables based on entities INI conf files
* support multiple DB system (ORACLE, MySQL, ...)

*code example:*

```php
use \classes\entities\User as UserEntity;
use \classes\entitiesManager\UserEntityManager as UserEntityManager;

$user            = new UserEntity();
$userManager     = new UserEntityManager($user);
$user->id        = 1;
$user->firstName = 'Toto';
$user->lastName  = 'Tata';
// Will save the entity in the database with an Update if the ID already exist
$userManager->saveEntity();
```

***

###DataBase class

* singleton pattern using PDO PHP class with `__staticCall` magic method
* use database action anywhere in you code by calling `DB::PDOmethod(params);` statically without initialize or set-up anything

*code example:*

```php
use \classes\DataBase as DB;

DB::exec('DROP TABLE toto;');
DB::query('SELECT * FROM toto;')->fetch();
```

***

###ORM console class

* console mode based on the DataBase class to perform basic database manipulation with pretty console output.

***

###ImagesManager class

This class is based on Imagick class

* simply create multiple resized picture with defined width or height and conserve scalling in one cal
* simply add copyright to an image

*code example:*

```php
use \classes\ImagesManager as Images;

$image = new Images(__DIR__ . '/test.jpeg');
$image->setImageSavePath(__DIR__ . '/testResized');
// This line will create a testResized directory and add all the new scaled images with commons 16/9 resolution
$image->generateResizedImagesByWidth(Images::$WIDTHS_16_9);
// This line will create a testCopyright directory and add the image with a ©Copyright text in a Verdana 40px font in a
// top left corner, the text is white with a black smooth alpha channel
$image->setImageSavePath(__DIR__ . '/testCopyright');
$image->copyrightImage('©Copyright', 40, 'Verdana', 'top-left');
```

***

###Benchmark class (uncomplete)

* test method performance

***

All those classes respect PHP PSR-2 standard and are 100% php documented.
All the source code is inspected by Sonar to respect and control good practises.
The code uses advanced strucure like Traits to avoid code replication.

***

The [phpDoc](http://ziperrom1.github.io/utilities/) is automatically generated and updated at each commit.

***

The code is open source so feel free to use / fork it or ask for bug fix / new feature by asking me or create branches with [feature/your-feature] branch name for a feature or [hotfix/your-hotfix] branch name for a bug fix (forked from master).

***

Thanks for reading.

Romain
