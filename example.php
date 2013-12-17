<?php
require_once('SessionHandler.php');

$sess_handler = new SessionHandler();

// Add DB data
$sess_handler->setDbDetails('localhost', 'username', 'password', 'database');
$sess_handler->setDbTable('session_handler_table');

session_set_save_handler(array($sess_handler, 'open'),
                         array($sess_handler, 'close'),
                         array($sess_handler, 'read'),
                         array($sess_handler, 'write'),
                         array($sess_handler, 'destroy'),
                         array($sess_handler, 'gc'));
session_start();
