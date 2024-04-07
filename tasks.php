<?php
require_once('./database.php');
$title = 'tasks';
require_once('./header.php');


$id = $_GET['id'] ?? 0;
$timestamp = date('Y-m-d H:i:s');


$project_id = "";
$task_type = "";
$task_title = "";
$task_description = "";
$task_note = "";


$is_delete = $_GET['delete'] ?? 0;


//select all projects
$all_sql = "SELECT * from PROJECTS";
$all_result = $db->query($all_sql);

//select todays tasks
// ------------------------------------------
// Get today's date in the format YYYY-MM-DD
$todayDate = date('Y-m-d');

// Prepare the SQL statement with a placeholder for the date
$task_sql = $db->prepare("SELECT TASKS.*, PROJECTS.NAME AS project_name 
                     FROM TASKS 
                     INNER JOIN PROJECTS ON TASKS.PROJECT_ID = PROJECTS.ID 
                     WHERE DATE(TASKS.created_at) = :todayDate");

// Bind the parameter
$task_sql->bindParam(':todayDate', $todayDate, SQLITE3_TEXT);

// Execute the statement
$todays_tasks = $task_sql->execute();
// --------------------------------------------



//code for edit form
if ((!$is_delete) && $id) {
    $sql = "SELECT * from TASKS WHERE ID = " . $id;
    $result = $db->query($sql);
    $task_array = $result->fetchArray(SQLITE3_ASSOC);
    $project_id = $task_array['PROJECT_ID'] ?? "";
    $task_type = $task_array['TASK_TYPE'] ?? "";
    $task_title = $task_array['TITLE'] ?? "";
    $task_description = $task_array['DESCRIPTION'] ?? "";
    $task_note = $task_array['NOTE'] ?? "";
}

//delete task
if ($is_delete && $id) {

    // Prepare the SQL statement with a placeholder
    $del_task_sql = $db->prepare("DELETE FROM TASKS WHERE ID = :taskId");

    // Bind the parameter
    $del_task_sql->bindParam(':taskId', $id, SQLITE3_INTEGER);

    // Execute the statement
    $del_task_sql->execute();
    header('location:tasks.php');
}


//save task
if (isset($_POST['submit'])) {

    $sql = "INSERT INTO TASKS ( PROJECT_ID, TASK_TYPE, TITLE, DESCRIPTION, NOTE, STATUS, TOTAL_TIME, CREATED_AT )VALUES ( '" . $_POST['project_id'] . "', '" . $_POST['task_type'] . "', '" . htmlspecialchars($_POST['title']) . "', '" . htmlspecialchars($_POST['description']) . "', '" . htmlspecialchars($_POST['notes']) . "' ,'0', '0', '".$timestamp."' )";

    if ($_POST['task_id']) {
        //update
        $sql = "UPDATE TASKS SET 
        PROJECT_ID = '" . $_POST['project_id'] . "',
        TASK_TYPE = '" . $_POST['task_type'] . "',
        TITLE = '" . htmlspecialchars($_POST['title']) . "',
        DESCRIPTION = '" . htmlspecialchars($_POST['description']) . "',
        NOTE = '" . htmlspecialchars($_POST['notes']) . "'        
        WHERE ID = '" . $_POST['task_id'] . "'";
    }

    if (!$db->exec($sql)) {
        echo $db->lastErrorMsg();
        exit;
    }

    header('location:tasks.php');
}

?>

<div class="container-fluid mt-5">

    <div class="card bg-secondary text-white">
        <div class="card-body">
                
            <form class="row g-3" method="post">
                <input type="hidden" name="task_id" value="<?= $id ?>">
                <div class="col-2">
                    <select class="form-select" name="project_id" required>
                        <option >Select Project</option>
                        <?php while ($project = $all_result->fetchArray(SQLITE3_ASSOC)) : ?>
                        <option value="<?= $project['ID'] ?>" <?= ($project_id == $project['ID']) ? "selected" : ""; ?> ><?= $project['NAME'] ?></option>
                        <?php endwhile ?>
                    </select>
                </div>
                <div class="col-2">
                    <select class="form-select" name="task_type" required>
                        <option value="t" <?= ($task_type == 't') ? "selected" : ""; ?>>Task</option>
                        <option value="b" <?= ($task_type == 'b') ? "selected" : ""; ?>>Break</option>
                        <option value="d" <?= ($task_type == 'd') ? "selected" : ""; ?>>Discussion</option>
                        <option value="h" <?= ($task_type == 'h') ? "selected" : ""; ?>>Help</option>
                    </select>
                </div>
                <div class="col-7">
                    <input type="text" name="title" id="title" value="<?=$task_title?>" class="form-control" placeholder="Task Title" required>
                </div>
                <div class="col-1">
                    <button type="submit" name="submit" class="btn btn-primary mb-3">Save</button>
                </div>
                <div class="col-6 pb-3">
                    <textarea name="description" id="description" cols="30" rows="2" class="form-control" placeholder="Task Description"><?=$task_description?></textarea>
                </div>
                <div class="col-6 pb-3">
                    <textarea name="notes" id="notes" cols="30" rows="2" class="form-control" placeholder="Task Note"><?=$task_note?></textarea>
                </div>
            </form>

            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th scope="col">Project</th>
                        <th scope="col">Task</th>
                        <th scope="col">Time</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $todays_tasks->fetchArray(SQLITE3_ASSOC)) : ?>
                        <tr>
                            <td><?= $task['project_name'] ?></td>
                            <td><?= $task['TITLE'] ?></td>
                            <td><?= $task['TOTAL_TIME'] ?></td>
                            <td><a class="btn btn-primary" href="./tasks.php?id=<?= $task['ID'] ?>"><i class="fa-solid fa-pen-to-square"></i></a></td>
                            <td><a class="btn btn-danger" href="./tasks.php?delete=1&id=<?= $task['ID'] ?>"><i class="fa-solid fa-trash"></i></a></td>
                            <td>
                                <?php if($task['STATUS'] == '0') : ?>
                                    <span class="btn btn-success play-btn" data-id="<?= $task['ID'] ?>"><i class="fa-solid fa-play"></i></span>
                                <?php else: ?>
                                    <span class="btn btn-warning pause-btn" data-id="<?= $task['ID'] ?>"><i class="fa-solid fa-pause"></i></span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endwhile ?>
                </tbody>

            </table>
        </div>
    </div>
</div>



<!-- pause model -->
<!-- Modal -->
<div class="modal fade" id="pauseModal" tabindex="-1" aria-labelledby="pauseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="pauseModalLabel">Pause Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="./play.php" id="pauseForm" method="post">
            <input type="hidden" name="task_id" id="task_id">
            <textarea name="notes" id="pause_notes" cols="30" rows="4" class="form-control" placeholder="Task Note"></textarea>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" form="pauseForm" class="btn btn-primary">Pause</button>
      </div>
    </div>
  </div>
</div>


<?php require_once('./footer.php'); ?>
<script src="./assets/js/jquery.min.js"></script>

<script>
    $('.pause-btn').on('click', function(){
        let task_id = $(this).data('id');
        $('#task_id').val(task_id);
        $('#pause_notes').val('');
        $('#pauseModal').modal('show');
    });

    $('.play-btn').on('click', function(){
        let task_id = $(this).data('id');
        window.location.href = './play.php?task_id='+ task_id ;
    });
</script>

