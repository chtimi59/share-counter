<p align="left">
   <img src="css/logo.png" alt="banner"/>
</p>

Share-Counter Cloud service
============================

Official sources of Share-Counter Website

Abstract
========

Yet another cloud service, to easily share various numbers.

Coming from the idea that a lot of cloud services actually redo the same thing again an again,
Share-Counter was made to do a versatile, reusable and open source service.

It's mainly based on the fact that a lot of solution actually fits in simple 4W records fields:

* Who? - Supposed to be a valid login name
* What? - A set of 2 fields:
  * WHAT_TEXT (for text)
  * WHAT_VALUE (for numbers)
* When? - An UTC date
* Where? - A GPS (Datum: WGS84) set of 3 fields:
  * WHERE_LAT (for latitude)
  * WHERE_LONG (for longitude)
  * WHERE_ALT (for altitude)

All of these fields are optionals.

History
=======

2015/07 : Jan dOrgeville - first issue

Prequists
=========

* PHP 5.3
* MySQL >5.5
* Bower >1.3.3

Note:
To install Bower (FrontEnd Package Manager), you actually probably need also npm (Node.js package manager). Check http://bower.io/ for more details about bower installation


Installation :
==============

### 1. Create a configuration file

* Unzip 'setup.zip'
* Open 'createconf.php' script in a text editor
* Change it according your needs
* Run it to generate a ".conf" file
* Be sure that your ".conf" file is in safer place (ex: /var/)

### 2. Update "conf.php" accordingly

Hash key choosen above (example)
```php
$secretHash = '1289rtpf%7+9'; 
```

Path (example)
```php
if(strncmp(PHP_OS, 'WIN', 3) === 0) {	
	$confPath = 'c:/dev/share-counter.conf'; /* On Windows */
} else {	
	$confPath = '/var/share-counter.conf'; /* On UNIX */
}
```

### 4. Install Main DataBase

run setup/setup.sql (text file) into your main database

Note: the Main Database location was previously in your configuration file.

### 5. Delete 'setup/' folder

...or at least move it in safer place !

After setup, this folder is no more needed, and it contains some very privacy datas (Email, Passwords) which should never accessible.
 
### 3. Install Frontend package

```
Bower install
```

Naming convention:
==================

* web-services ends with ".api.php"
* web-services associated to page "mypage.php" will be nammed "mypage.api.php"

Folders Layout:
===============

* server/    : server side

* client/    : website
* css/       : website styling
* lang/      : website strings per country
* header.php : website header
* index.php  : website entry point
* footer.php : website footer

* gallery/   : List of cloud clients (note that each has its 'setup.sql' to update main Database accordingly)

* api.php    : public Cloud API

License :
========

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.