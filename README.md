ZucchiBootstrap
===============

Module to provide files and helpers for use with ZF2 Modules

Installation
------------

From the root of your ZF2 Skeleton Application run

    ./composer.phar require zucchi/bootstrap
    
This module will require your vhost to use an AliasMatch

    AliasMatch /_([^/]+)/(.+)/([^/]+) /path/to/vendor/$2/public/$1/$3
    
You can now include the following in your layout to make use of Twitters Bootstrap

    <?=$this->headLink()->appendStylesheet($this->basePath() . '/_css/zucchi/bootstrap/bootstrap.min.css')
                        ->appendStylesheet($this->basePath() . '/_css/zucchi/bootstrap/bootstrap-responsive.min.css')?>
    <?=$this->headScript()->appendFile($this->basePath() . '/_js/zucchi/bootstrap/bootstrap.min.js') ?>
    
Available Features
------------------

*   Form View Helpers
    *    Bootstrap Form - Render a complete form
    *    Bootstrap Collection - Render a collection Element
    *    Bootstrap Row = Render an element
*   Navigation View Helpers (navbar only)
*   Alert View Helpers

Roadmap
-------

*    Image/Thumbnail view Helper
*    Dropdown Helpers
*    Navigation (tabs, pills, stackable, list) Helpers
*    Breadcrumb Helpers
*    Pagination Helpers
