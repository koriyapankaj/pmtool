<?php 

error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);

$runner_sql = "SELECT TASK_LOGS.START_TIME from TASK_LOGS LEFT JOIN TASKS ON TASK_LOGS.TASK_ID = TASKS.ID WHERE TASKS.STATUS = 1 ORDER BY TASK_LOGS.ID DESC LIMIT 1";
$runner_result = $db->query($runner_sql);
$runner_data_time = $runner_result->fetchArray(SQLITE3_ASSOC)['START_TIME'] ?? 0;

$START_TIME = ($runner_data_time) ? strtotime($runner_data_time) : ""; // Sample PHP START_TIME
// echo "<pre>"; var_dump($runner_data_time); exit;
?>