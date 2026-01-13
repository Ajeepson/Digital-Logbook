<?php
// itsupervisor/dashboard.php
include "includes/database.php";
include "includes/supervisor_nav.php";

if (!isset($_SESSION['itsupervisor_id'])) {
    header("Location: login.php");
    exit;
}

$supervisor_id = (int)$_SESSION['itsupervisor_id'];

// get supervisor
$sq = $conn->query("SELECT * FROM itsupervisor WHERE id = {$supervisor_id} LIMIT 1");
$supervisor = $sq->fetch_assoc();

// fetch assigned students with their latest week number from progress table
$students = [];

$query = "SELECT 
            s.*, 
            MAX(p.week) as current_week,
            i.Name as industry_name
          FROM students s 
          INNER JOIN itsupervisor i ON s.industry_id = i.id 
          LEFT JOIN progress p ON s.uid = p.stud_id 
          WHERE i.id = {$supervisor_id}
          GROUP BY s.uid 
          ORDER BY s.username ASC";

$rs = $conn->query($query);
if ($rs && $rs->num_rows > 0) {
    $students = $rs->fetch_all(MYSQLI_ASSOC);
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>IT Supervisor Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!--a class="navbar-brand" href="dashboard.php">E-Logbook (IT Supervisor)</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <!--ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="upload_signature.php">Upload Signature</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul-->
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="card mb-3">
    <div class="card-body">
      <h5>Welcome, <?php echo htmlspecialchars($supervisor['fullname'] ?? $supervisor['username']); ?></h5>
      <p class="mb-0"><strong>Company / Industry Name:</strong> <?php echo htmlspecialchars($supervisor['Name'] ?? ''); ?></p>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Students in your industry</h5>
      <?php if (empty($students)): ?>
        <p class="text-muted">No students found for your assigned industry.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Matric No</th>
                <th>Industry</th>
                <th>Current Week</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=0; foreach ($students as $s): $i++; ?>
                <tr>
                  <td><?php echo $i; ?></td>
                  <td><?php echo htmlspecialchars($s['username']); ?></td>
                  <td><?php echo htmlspecialchars($s['studentid'] ?? $s['student_id'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($s['industry_name'] ?? $s['industry'] ?? ''); ?></td>
                  <td>
                    <?php if (!empty($s['current_week'])): ?>
                      <span class="badge bg-primary">Week <?php echo $s['current_week']; ?></span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Not Started</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a class="btn btn-sm btn-primary" href="studentview.php?student_id=<?php echo (int)$s['uid']; ?>">View Logbook</a>
                    <a class="btn btn-sm btn-outline-success" href="studentview.php?student_id=<?php echo (int)$s['uid']; ?>#signature">Sign</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>