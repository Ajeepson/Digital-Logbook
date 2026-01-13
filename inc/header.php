<?php include "config/database.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <title>E-logbook</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <nav class="navbar navbar-expand-sm navbar-light bg-light shadow-sm mb-4">
    <div class="container">

      <!-- Logo + Brand name -->
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="logo.PNG" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
        <span class="fw-bold text-primary">ND Electronic-Logbook</span>
      </a>

      <!-- Mobile toggle button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarNav" aria-controls="navbarNav"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar links -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="/logbook/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="userlogin.php">Student</a>
          </li>
            <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navBtn" href="#" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-haspopup="true" aria-expanded="false">
          Supervisor
        </a>
        <div class="dropdown-menu" id="dropdown" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="itsupervisor/index.php">IT Supervisor</a>
          <a class="dropdown-item" href="schoolsupervisorlogin.php">School Supervisor</a>
        </div>
      </li>
            <li class="nav-item">
              <a class="nav-link" href="adminlogin.php"
                >Admin</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="notice.php"
                >Notices</a
              >
            </li>
        </ul>
      </div>
  </div>
</nav>
