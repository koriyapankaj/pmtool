<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/all.min.js"></script>

<script>
function updateTimer() {
    var startTime = <?php echo $START_TIME; ?> * 1000; // Convert PHP timestamp to milliseconds
    var currentTime = new Date().getTime();
    var elapsedTime = currentTime - startTime;

    var seconds = Math.floor(elapsedTime / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);

    document.getElementById('timer').innerHTML = (hours % 24) + "h "
    + (minutes % 60) + "m " + (seconds % 60) + "s ";

    setTimeout(updateTimer, 1000); // Update timer every second
}


window.onload = function() {
    updateTimer(); // Start the timer when the page loads
};
</script>

</body>
</html>