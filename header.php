<?php include_once('./runner.php'); ?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/all.min.css">

    <title><?=$title?></title>
  </head>
  <body class="bg-dark text-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="./index.php">
        <img src="./assets/img/skull.svg" alt="PAS" style="width: 40px;">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?= ($title === 'dashboard') ? 'active' : '' ?>" href="./index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($title == 'projects') ? 'active' : '' ?>" href="./projects.php">Projects</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($title === 'tasks') ? 'active' : '' ?>" href="./tasks.php">Tasks</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($title === 'logs') ? 'active' : '' ?>" href="./logs.php">Logs</a>
        </li>
      </ul>
    </div>
    <div class="d-flex">
      <p id="timer" class="pe-4 mb-0"></p>
    </div>
  </div>
</nav>