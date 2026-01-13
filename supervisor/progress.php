<?php
include "../config/database.php";
session_start();

// ✅ Ensure supervisor is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: /logbook/");
    exit;
}

$sess_user = $_SESSION['userid'];
$supervisor_id = null;
$user_name = "";

// ✅ Determine numeric supervisor ID
if (is_int($sess_user) || ctype_digit((string)$sess_user)) {
    $supervisor_id = (int)$sess_user;
} else {
    // Session might contain username instead of ID
    $safe_username = mysqli_real_escape_string($conn, $sess_user);
    $q = "SELECT id, username FROM supervisor WHERE username = '$safe_username' LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r && mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_assoc($r);
        $supervisor_id = (int)$row['id'];
        $user_name = $row['username'];
    } else {
        header("Location: /logbook/");
        exit;
    }
}

// ✅ Get supervisor's assigned industry_id from assignments table
$assignment_q = "SELECT industry_id FROM assignments WHERE supervisor_id = {$supervisor_id} LIMIT 1";
$assignment_r = mysqli_query($conn, $assignment_q);
$assigned_industry_id = "";
$assigned_industry_name = "";

if ($assignment_r && mysqli_num_rows($assignment_r) > 0) {
    $assign_row = mysqli_fetch_assoc($assignment_r);
    $assigned_industry_id = $assign_row['industry_id'];
    
    // Get industry name from itsupervisor table
    $industry_q = "SELECT Name FROM itsupervisor WHERE id = {$assigned_industry_id}";
    $industry_r = mysqli_query($conn, $industry_q);
    if ($industry_r && mysqli_num_rows($industry_r) > 0) {
        $industry_row = mysqli_fetch_assoc($industry_r);
        $assigned_industry_name = $industry_row['Name'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>E-Logbook | Supervisor Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: #6f2449d1; color: white; text-align:center;">

<nav class="navbar navbar-expand-sm navbar-light bg-light mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Electronic-Logbook</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="./">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="progress.php">Progress Chart</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<section>
<div class="container-fluid">

  <!-- ✅ Supervisor Info Header -->
  <div class="alert alert-secondary text-dark">
      <strong>Welcome, <?php echo htmlspecialchars($user_name ?: 'Supervisor'); ?></strong><br>
      Assigned Industry: <strong><?php echo htmlspecialchars($assigned_industry_name ?: 'Not Assigned'); ?></strong>
  </div>

<?php
// Fetch all students assigned to supervisor's industry and their progress
$supervisor_id_int = (int)$supervisor_id;

$sql = "
    SELECT 
        students.uid,
        students.username, 
        students.studentid, 
        students.imgsrc, 
        students.industry_id,
        itsupervisor.Name as industry_name,
        progress.week,
        progress.week_end,
        progress.mon,
        progress.tue,
        progress.wed,
        progress.thur,
        progress.fri,
        progress.sat,
        progress.status,
        progress.attachment,
        progress.stud_id
    FROM students
    INNER JOIN itsupervisor ON students.industry_id = itsupervisor.id
    LEFT JOIN progress ON students.uid = progress.stud_id
    WHERE students.industry_id = {$assigned_industry_id}
    ORDER BY students.username ASC, progress.week DESC
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    echo '<div class="alert alert-danger">Database error: ' . mysqli_error($conn) . '</div>';
    exit;
}

$output = mysqli_fetch_all($result, MYSQLI_ASSOC);
$sum = 0;

// Group students and their progress
$students_with_progress = [];
foreach ($output as $row) {
    $student_id = $row['uid'];
    if (!isset($students_with_progress[$student_id])) {
        $students_with_progress[$student_id] = [
            'student_info' => [
                'uid' => $row['uid'],
                'username' => $row['username'],
                'studentid' => $row['studentid'],
                'imgsrc' => $row['imgsrc'],
                'industry_name' => $row['industry_name']
            ],
            'progress' => []
        ];
    }
    
    if (!empty($row['week'])) {
        $students_with_progress[$student_id]['progress'][] = [
            'week' => $row['week'],
            'week_end' => $row['week_end'],
            'mon' => $row['mon'],
            'tue' => $row['tue'],
            'wed' => $row['wed'],
            'thur' => $row['thur'],
            'fri' => $row['fri'],
            'sat' => $row['sat'],
            'status' => $row['status'],
            'attachment' => $row['attachment'],
            'stud_id' => $row['stud_id']
        ];
    }
}
?>

<!-- Student Progress Table -->
<table class="table table-light table-striped table-bordered text-dark">
  <thead class="table-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Student</th>
      <th scope="col">Matric No</th>
      <th scope="col">Industry</th>
      <th scope="col">Week</th>
      <th scope="col">Week Ending</th>
      <th scope="col">Mon</th>
      <th scope="col">Tue</th>
      <th scope="col">Wed</th>
      <th scope="col">Thu</th>
      <th scope="col">Fri</th>
      <th scope="col">Sat</th>
      <th scope="col">Status</th>
      <th scope="col">Attachment</th>
      <th scope="col">Approve</th>
      <th scope="col">Decline</th>
    </tr>
  </thead>
  <tbody>
  <?php if (count($students_with_progress) > 0): ?>
    <?php foreach($students_with_progress as $student_id => $student_data): 
        $student_info = $student_data['student_info'];
        $progress_entries = $student_data['progress'];
        
        if (count($progress_entries) > 0): 
            foreach($progress_entries as $progress): $sum++; ?>
                <tr>
                    <th scope="row"><?php echo $sum ?></th>
                    <td><?php echo htmlspecialchars($student_info['username']) ?></td>
                    <td><?php echo htmlspecialchars($student_info['studentid']) ?></td>
                    <td><?php echo htmlspecialchars($student_info['industry_name']) ?></td>
                    <td><?php echo htmlspecialchars($progress['week']) ?></td>
                    <td><?php echo htmlspecialchars($progress['week_end']) ?></td>
                    <td><?php echo htmlspecialchars($progress['mon']) ?></td>
                    <td><?php echo htmlspecialchars($progress['tue']) ?></td>
                    <td><?php echo htmlspecialchars($progress['wed']) ?></td>
                    <td><?php echo htmlspecialchars($progress['thur']) ?></td>
                    <td><?php echo htmlspecialchars($progress['fri']) ?></td>
                    <td><?php echo htmlspecialchars($progress['sat']) ?></td>
                    <td><?php echo htmlspecialchars($progress['status']) ?></td>
                    <td>
                        <?php
                        $attachmentFile = htmlspecialchars($progress['attachment']);
                        $attachmentPath = "../student/uploads/" . $attachmentFile;
                        $attachmentURL  = "http://localhost/logbook/student/uploads/" . $attachmentFile;

                        if (!empty($attachmentFile) && file_exists($attachmentPath)) {
                            echo '<a href="'.$attachmentURL.'" class="btn btn-info btn-sm" download>Download</a>';
                        } else {
                            echo '<span class="text-muted">No attachment</span>';
                        }
                        ?>
                    </td>
                    <td><a href="approve.php?id=<?php echo htmlspecialchars($progress['stud_id']) ?>&week=<?php echo htmlspecialchars($progress['week']) ?>" class="btn btn-success btn-sm">Approve</a></td>
                    <td><a href="decline.php?id=<?php echo htmlspecialchars($progress['stud_id']) ?>&week=<?php echo htmlspecialchars($progress['week']) ?>" class="btn btn-danger btn-sm">Decline</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <th scope="row"><?php echo ++$sum ?></th>
                <td><?php echo htmlspecialchars($student_info['username']) ?></td>
                <td><?php echo htmlspecialchars($student_info['studentid']) ?></td>
                <td><?php echo htmlspecialchars($student_info['industry_name']) ?></td>
                <td colspan="10" class="text-center text-muted">No progress reports submitted yet</td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr><td colspan="16" class="text-center text-muted">No students found in your assigned industry.</td></tr>
  <?php endif; ?>
  </tbody>
</table>

</div>
</section>

<footer class="text-center mt-5">
  Copyright &copy; 2025
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js"></script>
</body>
</html>