<?php
require_once('./database.php');

$timestamp = date('Y-m-d H:i:s');
$task_id = $_REQUEST['task_id'] ?? 0;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $task_id) {
    //pause code
    try {

        //pause note
        $note = (isset($_REQUEST['notes'])) ? htmlspecialchars($_REQUEST['notes']) : "";


        //calculate total time
        $get_task_log_sql = "SELECT * FROM TASK_LOGS WHERE TASK_ID = '".$task_id."' ORDER BY ID DESC LIMIT 1";

        $result = $db->query($get_task_log_sql);
        $task_log = $result->fetchArray(SQLITE3_ASSOC) ?? "";
        
        $time = calculateTimeDifferenceInMinutes($task_log['START_TIME']); 

        $task_sql = "UPDATE TASKS SET STATUS = '0', TOTAL_TIME = (TOTAL_TIME + '".$time."') WHERE ID = '" . $task_id . "'";
        $task_log_sql = "UPDATE TASK_LOGS SET END_TIME = '".$timestamp."', NOTE = '".$note."' WHERE ID = '" . $task_log['ID'] . "'";
        
        // Begin a transaction
        $db->exec('BEGIN');
    
        //change status in tasks table
        $db->exec($task_sql);
        $db->exec($task_log_sql);
    
        // Commit the transaction if everything was successful
        $db->exec('COMMIT');
        
    } catch (\Throwable $th) {
        // Rollback all transactions
        $db->exec('ROLLBACK');
        echo "Failed";
        exit;
    } finally {
        // Close the database connection
        $db->close();
        header('location:tasks.php');
    }
    

}




if ($_SERVER['REQUEST_METHOD'] == 'GET' && $task_id) {
    //play code

    try {

        $task_sql = "UPDATE TASKS SET STATUS = '1' WHERE ID = '" . $task_id . "'";
        $task_log_sql = "INSERT INTO TASK_LOGS ( TASK_ID, START_TIME )VALUES ( '" . $task_id . "', '". $timestamp ."' )";
        // Begin a transaction
        $db->exec('BEGIN');
    
        //change status in tasks table
        $db->exec($task_sql);
        $db->exec($task_log_sql);
    
        // Commit the transaction if everything was successful
        $db->exec('COMMIT');
        
    } catch (\Throwable $th) {
        // Rollback all transactions
        $db->exec('ROLLBACK');
        echo "Failed";
        exit;
    } finally {
        // Close the database connection
        $db->close();
        header('location:tasks.php');
    }
    

}



function calculateTimeDifferenceInMinutes($timestampString) {
    // Create a DateTime object from the timestamp string
    $timestamp = new DateTime($timestampString);

    // Get the current time as a DateTime object
    $currentTime = new DateTime();

    // Calculate the difference between the two timestamps
    $interval = $currentTime->diff($timestamp);

    // Calculate the total minutes difference
    $totalMinutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

    return $totalMinutes;
}

