<?php
session_start();
if (!isset($_SESSION['itsupervisor_id'])) {
    header("Location: itsupervisorlogin.php");
    exit();
}

include "includes/database.php";

$student_id = $_GET['student_id'] ?? '';
$supervisor_id = $_SESSION['itsupervisor_id'];

// Fetch student details
$student = $conn->query("SELECT * FROM students WHERE id='$student_id'")->fetch_assoc();

// Fetch supervisor signature if uploaded
$sig = $conn->query("SELECT signature FROM itsupervisor WHERE id='$supervisor_id'")->fetch_assoc();
$signature = !empty($sig['signature']) ? $sig['signature'] : null;

// Fetch all progress logs for this student
$progress_query = $conn->query("SELECT * FROM progress WHERE student_id='$student_id' ORDER BY week ASC");

// Handle weekly comment submission
if (isset($_POST['comment_submit'])) {
    $week = $_POST['week'];
    $comment = $conn->real_escape_string($_POST['comment']);
    $conn->query("UPDATE progress SET supervisor_comment='$comment' WHERE student_id='$student_id' AND week='$week'");
    header("Location: view_logbook.php?student_id=$student_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Logbook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-entry {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .signature-box img {
            width: 120px;
            height: auto;
        }
        textarea {
            width: 100%;
            min-height: 80px;
        }
    </style>
</head>
<body>
<?php include "includes/supervisor_nav.php"; ?>

<div class="container mt-4">
    <h4 class="mb-3 text-center">Weekly Logbook for <?php echo $student['fullname']; ?> (<?php echo $student['matric_no']; ?>)</h4>

    <?php while ($log = $progress_query->fetch_assoc()): ?>
        <div class="log-entry">
            <h5>Week <?php echo $log['week']; ?></h5>
            <p><strong>Activity Description:</strong><br><?php echo nl2br($log['content']); ?></p>
            <?php if (!empty($log['diagram'])): ?>
                <p><strong>Diagram:</strong><br><img src="uploads/<?php echo $log['diagram']; ?>" width="250"></p>
            <?php else: ?>
                <p><em>No diagram uploaded</em></p>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="week" value="<?php echo $log['week']; ?>">
                <label><strong>Supervisor's Comment:</strong></label>
                <textarea name="comment" placeholder="Write your weekly comment here..."><?php echo $log['supervisor_comment']; ?></textarea>
                <button type="submit" name="comment_submit" class="btn btn-primary btn-sm mt-2">Save Comment</button>
            </form>

            <?php if ($signature): ?>
                <div class="signature-box mt-3">
                    <label><strong>Supervisor Signature:</strong></label><br>
                    <img src="signatures/<?php echo $signature; ?>" alt="Supervisor Signature">
                </div>
            <?php else: ?>
                <p class="text-danger mt-3"><em>No signature uploaded yet.</em></p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
