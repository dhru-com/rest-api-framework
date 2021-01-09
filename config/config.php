<?php
defined("DHRU_ACCESS") or die (header('Status: ' . 401, TRUE, 401) . '401 Unauthorized');

$DBCONFIG['server'] = 'mysql';
$DBCONFIG['host'] = '<DB_HOST>';
$DBCONFIG['database'] = '<DB_DATABSE>';
$DBCONFIG['user'] = '<DB_USER>';
$DBCONFIG['password'] = '<DB_PASSWORD>';

