<?php
require_once('./database.php');
require_once('./schema.php');

try {
    // Begin a transaction
    $db->exec('BEGIN');

    //create tables
    $db->exec($projects);
    $db->exec($tasks);
    $db->exec($task_logs);

    // Commit the transaction if everything was successful
    $db->exec('COMMIT');
    
    echo "Install Success";
} catch (\Throwable $th) {
    // Rollback all transactions
    $db->exec('ROLLBACK');
    echo "Install Failed";
} finally {
    // Close the database connection
    $db->close();
}
?>
