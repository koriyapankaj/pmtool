<?php
require_once('./database.php');
$title = 'dashboard';
require_once('./header.php');
$date = date('Y-m-d');


//get todays log project wise
$all_sql = "SELECT PROJECTS.ID AS project_id,PROJECTS.NAME AS project_name,COALESCE(SUM((strftime('%s', TASK_LOGS.END_TIME) - strftime('%s', TASK_LOGS.START_TIME)) / 60), 0) AS total_minutes FROM PROJECTS LEFT JOIN TASKS ON PROJECTS.ID = TASKS.PROJECT_ID LEFT JOIN TASK_LOGS ON TASKS.ID = TASK_LOGS.TASK_ID WHERE DATE(TASKS.CREATED_AT) = DATE('".$date."') GROUP BY PROJECTS.ID, PROJECTS.NAME";
$all_result = $db->query($all_sql);


// Define an array of task types
$taskTypes = ['h', 'b', 'd', 't'];

// Initialize an empty array to store chart results
$chartResults = [];

// Loop through each task type and build the SQL query
foreach ($taskTypes as $taskType) {
    $sql = "SELECT COALESCE(SUM((strftime('%s', TASK_LOGS.END_TIME) - strftime('%s', TASK_LOGS.START_TIME)) / 60), 0) AS total_minutes FROM TASK_LOGS LEFT JOIN TASKS ON TASK_LOGS.TASK_ID = TASKS.ID WHERE TASKS.TASK_TYPE = '".$taskType."' AND DATE(TASKS.CREATED_AT) = DATE('".$date."')";
    $chart_result = $db->query($sql);
    $chartResults[$taskType] = $chart_result->fetchArray(SQLITE3_ASSOC)['total_minutes'] ?? 0;
}

function formatMinutesToHoursMinutes($totalMinutes) {
    if ($totalMinutes >= 60) {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d hours %02d min', $hours, $minutes);
    } else {
        return sprintf('%02d min', $totalMinutes);
    }
}


?>


<div class="container-fluid mt-5">

    <div class="row">
        <div class="col-6">
            <div class="card bg-secondary text-white">
                <div class="card-header bg-dark">
                    Task Log
                </div>
                <div class="card-body">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Project</th>
                                <th scope="col" class="text-end">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_min = 0;                            
                            while ($project = $all_result->fetchArray(SQLITE3_ASSOC)) {
                            $total_min += $project['total_minutes'] ?? 0;
                            ?>
                            <tr>
                                <td><?=$project['project_name'] ?? ""; ?></td>
                                <td class="text-end"><?=formatMinutesToHoursMinutes($project['total_minutes'] ?? 0); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td><b>Total Work</b></td>
                                <td class="text-end"><b><?=formatMinutesToHoursMinutes($total_min); ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card bg-dark border-secondary text-white">
                <div class="card-body text-white">
                <canvas id="myChart" style="width:100%;max-width:700px"></canvas>
                </div>
            </div>
        </div>
    </div>


</div>


<script src="./assets/js/Chart.js"></script>

<script>
    
const xValues = ["Task", "Help", "Discussion", "Break"];
const yValues = [
    "<?=$chartResults['t'] ?>",    
    "<?=$chartResults['h'] ?>",    
    "<?=$chartResults['d'] ?>",    
    "<?=$chartResults['b'] ?>",    
];
const barColors = [
    'rgb(255, 99, 132)',
    'rgb(32, 201, 151)',
    'rgb(255, 205, 86)',
    'rgb(54, 162, 235)',
];



new Chart("myChart", {
  type: "doughnut",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    title: {
      display: true,
      text: "Today's Time",
      fontSize: 18,
      fontColor: "#fff"
    },
    legend: {
      labels: {
        fontSize: 12, // Change font size of the labels in the legend
        fontColor: "#fff" // Change font color of the labels in the legend
      }
    }
  }
});

</script>
<?php
require_once('./footer.php');
