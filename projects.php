<?php
require_once('./database.php');
$title = 'projects';
require_once('./header.php');

$id = $_GET['id'] ?? 0;
$project_name = "";
$is_delete = $_GET['delete'] ?? 0;


$all_sql = "SELECT * from PROJECTS ORDER BY ID DESC";
$all_result = $db->query($all_sql);


if ((!$is_delete) && $id) {
    $sql = "SELECT `NAME` from PROJECTS WHERE ID = " . $id . " LIMIT 1";
    $result = $db->query($sql);
    $project_name = $result->fetchArray(SQLITE3_ASSOC)['NAME'] ?? "";
}

if ($is_delete && $id) {

    //delete all tasks from this project
    // Prepare the SQL statement with a placeholder
    $del_task_sql = $db->prepare("DELETE FROM TASKS WHERE PROJECT_ID = :projectId");

    // Bind the parameter
    $del_task_sql->bindParam(':projectId', $id, SQLITE3_INTEGER);

    // Execute the statement
    $del_task_sql->execute();

    // Prepare the SQL statement with a placeholder
    $delsql = $db->prepare("DELETE FROM PROJECTS WHERE ID = :projectId");

    // Bind the parameter
    $delsql->bindParam(':projectId', $id, SQLITE3_INTEGER);

    // Execute the statement
    $result = $delsql->execute();
    header('location:projects.php');
}


if (isset($_POST['submit'])) {

    $sql = "INSERT INTO PROJECTS ( NAME ,STATUS )VALUES ( '" . htmlspecialchars($_POST['name']) . "', '1' )";

    if ($_POST['project_id']) {
        //update
        $sql = "UPDATE PROJECTS SET NAME = '" . htmlspecialchars($_POST['name']) . "' WHERE id = '" . $_POST['project_id'] . "'";
    }

    if (!$db->exec($sql)) {
        echo $db->lastErrorMsg();
        exit;
    }

    header('location:projects.php');
}

?>


<div class="container-fluid mt-5">

    <div class="card bg-secondary text-white">
        <div class="card-body">

            <form class="row g-3" method="post">
                <div class="col-auto">
                    <input type="hidden" name="project_id" value="<?= $id ?>">
                    <input type="text" name="name" required class="form-control" value="<?= $project_name ?>" placeholder="Project Name">
                </div>
                <div class="col-auto">
                    <button type="submit" name="submit" class="btn btn-primary mb-3">Create Project</button>
                </div>
            </form>

            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th scope="col">Project</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($project = $all_result->fetchArray(SQLITE3_ASSOC)) : ?>
                        <tr>
                            <td><?= $project['NAME'] ?></td>
                            <td><a class="btn btn-primary" href="./projects.php?id=<?= $project['ID'] ?>"><i class="fa-solid fa-pen-to-square"></i></a></td>
                            <td><a class="btn btn-danger" href="./projects.php?delete=1&id=<?= $project['ID'] ?>"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endwhile ?>
                </tbody>

            </table>
        </div>
    </div>

</div>



<?php
require_once('./footer.php');
