micro-pma is a very small MySQL admin tool written with PHP
===========================================================

This tool allows you to manage the database in case you have very limited access
to your host: only WEB and FTP. Sometimes responsible persons forget to provide
you with SSH access, sometimes they are too lazy or not competent to install
PhpMyAdmin for you. In this case you can use this small script for some rapid
and simple manipulations with MySQL databases.

Features
--------

- It keeps SQL queries history within the current session (very useful for
  repeatable operations).
- Can be used to dump small tables/databases.
- It provides VERY SIMPLE security protection: you can setup password to avoid
  malicious access. But it worth removing this script when you are done.
- Written with basic mysql-functions. It works on PHP 5 and PHP 4.

Usage
-----

- Open pma.php in editor and write your database credentials: host, database
  name, username and password.
- Also, change default script access password.
- Upload the file to web-directory.
- Go to this script with proper URL in your browser.

Thanks
------

I wasn't able to define the real author of database dump piece of code. 
Anyway, it is for him: THANK YOU, YOU ARE [AWESOME](http://www.codinghorror.com/blog/2012/05/how-to-stop-sucking-and-be-awesome-instead.html).
