<?php
require_once('./database.php');
$title = 'logs';
require_once('./header.php');
$date = date('Y-m-d');

$id = $_GET['id'] ?? 0;
$is_delete = $_GET['delete'] ?? 0;

$all_sql = "SELECT TASKS.TITLE, TASK_LOGS.ID, TASK_LOGS.NOTE, ((strftime('%s', TASK_LOGS.END_TIME) - strftime('%s', TASK_LOGS.START_TIME)) / 60) AS total_minutes FROM TASK_LOGS LEFT JOIN TASKS ON TASK_LOGS.TASK_ID = TASKS.ID WHERE DATE(TASKS.CREATED_AT) = DATE('".$date."') ORDER BY TASK_LOGS.ID DESC";
$all_result = $db->query($all_sql);

if ($is_delete && $id) {

    //code for minus from total hours
    $task_data_sql = "SELECT TASK_ID, ((strftime('%s', END_TIME) - strftime('%s', START_TIME)) / 60) AS total_minutes FROM TASK_LOGS WHERE ID = '".$id."' LIMIT 1";
    $result = $db->query($task_data_sql);
    $data = $result->fetchArray(SQLITE3_ASSOC);


    if (isset($data['TASK_ID']) && $data['TASK_ID'] !== "") {
        try {
            // Start the transaction
            $db->exec('BEGIN TRANSACTION');
    
            // Minus time
            $sub_time = $data['total_minutes'] ?? 0;
    
            // Prepare the update SQL statement using a prepared statement
            $task_sql = $db->prepare("UPDATE TASKS SET TOTAL_TIME = (TOTAL_TIME - :sub_time) WHERE ID = :task_id");
            
            // Bind parameters
            $task_sql->bindParam(':sub_time', $sub_time, SQLITE3_INTEGER);
            $task_sql->bindParam(':task_id', $data['TASK_ID'], SQLITE3_INTEGER);
    
            // Execute the update statement
            $task_sql->execute();
    
            // Prepare the SQL statement for deleting task log
            $del_log_sql = $db->prepare("DELETE FROM TASK_LOGS WHERE ID = :taskLogId");
            
            // Bind the parameter for deletion
            $del_log_sql->bindParam(':taskLogId', $id, SQLITE3_INTEGER);
    
            // Execute the deletion statement
            $del_log_sql->execute();
    
            // Commit the transaction
            $db->exec('COMMIT');
            
            echo "Transaction successful";
        } catch (\Throwable $th) {
            // Rollback the transaction in case of any errors
            $db->exec('ROLLBACK');
            echo "Transaction failed: " . $th->getMessage();
        } finally {
            // Close the database connection
            $db->close();
        }
    }
    


    header('location:logs.php');
}

?>


<div class="container-fluid mt-5">

    <div class="card bg-secondary text-white">
        <div class="card-body">

            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th scope="col">Task</th>
                        <th scope="col">Note</th>
                        <th scope="col">Time</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $all_result->fetchArray(SQLITE3_ASSOC)) : ?>
                        <tr>
                            <td><?= $task['TITLE'] ?></td>
                            <td><?= $task['NOTE'] ?></td>
                            <td><?= $task['total_minutes'] ?></td>
                            <td><a class="btn btn-danger" href="./logs.php?delete=1&id=<?= $task['ID'] ?>"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>

            </table>
        </div>
    </div>

</div>



<?php
require_once('./footer.php');
