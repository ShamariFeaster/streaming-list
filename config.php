<?php
date_default_timezone_set('America/New_York');
//if using docker
$host = 'mysql_db';
//if using locally hosted
//$host = '127.0.0.1';
$db   = 'streaming_list';
//if using docker, below should match what's in docker_compose.yaml
$user = 'user';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     echo $e->getMessage();
     exit('Database connection failed.');
}
