PHP MySQL Session Handler
========================

Installation
----------------------------

First you need to create a table in your database:

    CREATE TABLE `Sessions` (
        `id` varchar(255) NOT NULL,
        `data` text NOT NULL,
        `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


Then have a look at example.php
Easy!