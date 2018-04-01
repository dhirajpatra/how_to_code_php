# This will generate a csv file from sql query

### Requirements

*  PHP version 5.6 or newer
*  PHP extension php_zip enabled
*  PHP extension php_xml enabled
*  PHP extension php_gd2 enabled

### How to install

* run `composer install`
* copy settings.php-example to settings.php
* change `settings.php` as per your credentials

### How to run

* got to root at application and run from command line: php index.php 20140303 20140304 template1
* or for current date
* php index.php template1

* csv/xls files will be created into /reports/ folder.

### Standard to follow

* Application need to create to call different reports [sql and template]
* Need email [swift mailer] system to send mails from the application in different points
* many small try - catch block for specific process part.
* Only specific no [eg.2] of parameters/argument will send from command line. So no need looping for STDIN arguments.
* Check arguments with regex, eg. date and also need to validate its format
* need to validate as start date is < end date or created date etc if any
* db execution try catch block for pdoexception
* for other process part need general exceptions and throw. So that it can make chain response
* error need to write in log file
* all part should be modular and small functions
* SOLID - oops structure should follow
* exit if any should be with msg
* report template/csv can be different and can be called from command prompt
* report template will be based on sql. count of sql col and report template col should be same but header of template may be different than sql col

### Extentions
* To create more template class need to extend Template abstract class
* We can create several template class with specifc csv headers and sql
