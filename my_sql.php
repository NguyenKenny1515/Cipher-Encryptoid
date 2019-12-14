<?php
    require_once "login.php";
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error)
        die (mysql_fatal_error());

    // Creates the database
    $query = "CREATE DATABASE IF NOT EXISTS projectdb";
    $result = $conn->query($query);
    if (!$result) die(mysql_fatal_error());
    
    $query = "USE projectdb";
    $result = $conn->query($query);
    if (!$result) die(mysql_fatal_error());
    
    // Creates the table to store user information
    $query = "CREATE TABLE IF NOT EXISTS users("
            . "username VARCHAR(25) NOT NULL,"
            . "email VARCHAR(35) NOT NULL,"
            . "password VARCHAR(50),"
            . "salt1 VARCHAR(128),"
            . "salt2 VARCHAR(128))";
    $result = $conn->query($query);
    if (!$result) die(mysql_fatal_error());
    
    // Creates the table to store encryption/decryption history
    $query = "CREATE TABLE IF NOT EXISTS history("
            . "text VARCHAR(128),"
            . "cipher VARCHAR(20),"
            . "timestamp VARCHAR(25))";
    $result = $conn->query($query);
    if (!$result) die(mysql_fatal_error());
    
    function mysql_fatal_error() {
        echo <<<_END
    Sorry, there has been an error and we were unable to process your requested
    task! Please try again.<br>
_END;
    }
?>

