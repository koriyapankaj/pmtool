<?php
date_default_timezone_set('Asia/Kolkata');
// Replace 'mydatabase.sqlite' with your database file name
$db = new SQLite3('pas.sqlite');

if(!$db) {
    echo $db->lastErrorMsg();
    exit;
}
?>
