<?php
// student_view.php
include "includes/database.php";
session_start();

if (!isset($_SESSION['itsupervisor_id'])) {
    header("Location: login.php");
    exit;
}
$supervisor_id = (int)$_SESSION['itsupervisor_id'];
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$msg = "";

// fetch student
$st = $conn->query("SELECT * FROM students WHERE uid = {$student_id} LIMIT 1");
if (!$st || $st->num_rows == 0) {
    echo "Student not found.";
    exit;
}
$student = $st->fetch_assoc();

// handle comment save
if (isset($_POST['save_comment'])) {
    $progress_id = (int)$_POST['progress_id'];
    $comment = $conn->real_escape_string($_POST['supervisor_comment']);
    $update = $conn->query("UPDATE progress SET supervisor_comment = '{$comment}' WHERE pid = {$progress_id}");
    if ($update) $msg = "<div class='alert alert-success'>Comment saved.</div>";
    else $msg = "<div class='alert alert-danger'>Error saving comment: " . $conn->error . "</div>";
}

// handle signing a weekly progress row
if (isset($_POST['sign_week'])) {
    $progress_id = (int)$_POST['progress_id'];

    // find latest signature uploaded by this supervisor (from itsignatures)
    $sigq = $conn->query("SELECT signature FROM itsupervisor WHERE id = {$supervisor_id} ORDER BY id DESC LIMIT 1");
    if ($sigq && $sigq->num_rows > 0) {
        $sigrow = $sigq->fetch_assoc();
        $sigpath = $conn->real_escape_string($sigrow['signature']);

        // try to update progress.itsupervisor_sign column if exists
        $upd = $conn->query("UPDATE progress SET itsupervisor_sign = '{$sigpath}' WHERE pid = {$progress_id}");
        // also insert into itsignatures table to record signature for this student (optional)
        $ins = $conn->query("INSERT INTO itsignatures (itsupervisor_id, student_uid, signature_path, signed_on) VALUES ({$supervisor_id}, {$student_id}, '{$sigpath}', NOW())");

        if ($upd || $ins) {
            $msg = "<div class='alert alert-success'>Week signed successfully.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Failed to sign: " . $conn->error . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>No uploaded signature found. Please upload your signature first.</div>";
    }
}

// fetch progress rows for the student
$pr = $conn->query("SELECT * FROM progress WHERE stud_id = {$student_id} ORDER BY week_end DESC");
$progress_rows = $pr ? $pr->fetch_all(MYSQLI_ASSOC) : [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Logbook - <?php echo htmlspecialchars($student['username']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .logbox { background:#fff; border:1px solid #ddd; padding:12px; border-radius:6px; margin-bottom:18px; }
    .log-header { font-weight:700; margin-bottom:8px; }
    .signature-img { max-height:60px; border:1px solid #aaa; padding:4px; border-radius:4px; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-4">
  <a href="dashboard.php" class="btn btn-secondary mb-3">&laquo; Back</a>
  <div class="card mb-3"><div class="card-body">
    <h5>Student: <?php echo htmlspecialchars($student['username']); ?> — <?php echo htmlspecialchars($student['studentid']); ?></h5>
    <p class="mb-0"><strong>Industry:</strong> <?php echo htmlspecialchars($student['industry'] ?? $student['industry_id']); ?></p>
  </div></div>

  <?php if ($msg) echo $msg; ?>

  <?php if (empty($progress_rows)): ?>
    <div class="alert alert-info">No progress reports yet for this student.</div>
  <?php else: ?>
    <?php foreach ($progress_rows as $row): ?>
      <div class="logbox">
        <div class="log-header">Week: <?php echo htmlspecialchars($row['week']); ?> — Week ending: <?php echo htmlspecialchars($row['week_end']); ?></div>

        <div><strong>Monday:</strong> <?php echo nl2br(htmlspecialchars($row['mon'])); ?></div>
        <div><strong>Tuesday:</strong> <?php echo nl2br(htmlspecialchars($row['tue'])); ?></div>
        <div><strong>Wednesday:</strong> <?php echo nl2br(htmlspecialchars($row['wed'])); ?></div>
        <div><strong>Thursday:</strong> <?php echo nl2br(htmlspecialchars($row['thur'])); ?></div>
        <div><strong>Friday:</strong> <?php echo nl2br(htmlspecialchars($row['fri'])); ?></div>
        <div><strong>Saturday:</strong> <?php echo nl2br(htmlspecialchars($row['sat'])); ?></div>

        <div class="mt-2"><strong>Attachment:</strong>
          <?php if (!empty($row['attachment'])): ?>
            <a href="../student/<?php echo htmlspecialchars($row['attachment']); ?>" target="_blank">Download</a>
          <?php else: ?> No attachment <?php endif; ?>
        </div>

        <hr>

        <form method="post">
          <input type="hidden" name="progress_id" value="<?php echo (int)$row['pid']; ?>">
          <div class="mb-2">
            <label>Supervisor weekly comment</label>
            <textarea name="supervisor_comment" class="form-control" rows="2"><?php echo htmlspecialchars($row['supervisor_comment'] ?? ''); ?></textarea>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <div>
              <?php if (!empty($row['itsupervisor_sign'])): ?>
                <img class="signature-img" src="../<?php echo htmlspecialchars($row['itsupervisor_sign']); ?>" alt="Signature">
              <?php else: ?>
                <span class="text-muted">Not signed yet</span>
              <?php endif; ?>
            </div>
            <div>
              <button type="submit" name="save_comment" class="btn btn-sm btn-success">Save Comment</button>
              <button type="submit" name="sign_week" class="btn btn-sm btn-primary">Sign Week</button>
            </div>
          </div>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
