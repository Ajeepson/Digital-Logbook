<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Supervisor Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #004080;
        }
        .navbar-brand, .nav-link, .navbar-text {
            color: white !important;
        }
        .nav-link:hover {
            background-color: #003366;
            border-radius: 5px;
        }
        .logout-btn {
            background-color: #dc3545;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            color: white;
        }
        .logout-btn:hover {
            background-color: #bb2d3b;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="dashboard.php">SIWES Logbook</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">🏠 Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="studentview.php">👨‍🎓 View Students</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="upload_signature.php">✍️ Upload Signature</a>
        </li>
      </ul>
      <span class="navbar-text me-3">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['itsupervisor_name']); ?></strong>
      </span>
      <form action="logout.php" method="post" style="display:inline;">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
      </form>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
