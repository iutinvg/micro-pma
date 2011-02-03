micro-pma - a very small MySQL admin tool written with PHP
----------------------------------------------------------

This tool allows you to manage the database in case you have very limited access
to your host: only WEB and FTP. Sometimes responsible persons forget to provide
you with SSH access, sometimes they are too lazy or not competent to install
PhpMyAdmin for you. In this case you can use this small script for some rapid
and simple manipulations with MySQL databases.

Features
========

- Keeps SQL queries history within the current session, very useful for
  repeatable operations.
- Provides VERY SIMPLE security protection: you can setup password to avoid
  malicious access. But it worth removing this script when you are done.
- Written with basic mysql-functions. It works on PHP 5 and PHP 4.

Usage
=====

- Open pma.php in editor and write your database credentials: host, database
  name, username and password.
- Change script access password also.
- Upload the file to web-directory.
- Go to this script with proper URL in your browser.
