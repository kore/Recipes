==============
Recipe Manager
==============

This is a simple PHP 5.3 and CouchDB based application to manage recipes. I use
it internally for my family to create, share and distribute recipes.

Features
========

- CRUD for recipes
  - Attaching images
- Tagging of recipes
- Searching of recipes
- Export of recipes
  - ODT
  - PDF
  - HTML
  - ReStructured Text
  - Basic Docbook
- All actions except export require prior authentification

Requirements
============

The Recipe Manager requires PHP 5.3 and CouchDB 0.10 or later. All required
libraries willb e installed using Composer, as described below.

Installation
============

Execute the following commands to install all dependencies::

    wget http://getcomposer.org/composer.phar
    php composer.phar install

There is currently no dedicated configuration file. If you want to configure
something checkout the Dependency Injection Container at
``src/main/Recipes/DIC/Base.php``.


..
   Local Variables:
   mode: rst
   fill-column: 79
   End: 
   vim: et syn=rst tw=79
