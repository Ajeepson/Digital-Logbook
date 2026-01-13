<?php
include "../config/database.php";
session_start();

if(!isset($_SESSION['itsupervisor_id'])){
    header("Location: login.php");
    exit();
}

$sup_id = $_SESSION['itsupervisor_id'];

// Get supervisor info
$sql = "SELECT itsupervisors.*, industries.name AS industry_name 
        FROM itsupervisors 
        LEFT JOIN industries ON itsupervisors.industry_id = industries.id 
        WHERE itsupervisors.id = '$sup_id'";
$result = mysqli_query($conn, $sql);
$supervisor = mysqli_fetch_assoc($result);

// Fetch students in same industry
$industry_id = $supervisor['industry_id'];
$students = mysqli_query($conn, "SELECT * FROM students WHERE industry_id='$industry_id'");
?>
<!DOCTYPE html>
<html>
<head>
<title>Supervisor Dashboard</title>
<link href="../bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Welcome, <?php echo $supervisor['fullname']; ?></h3>
    <p>Industry: <?php echo $supervisor['industry_name']; ?></p>
    <hr>
    <h4>Students Under Your Industry</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Matric No</th>
                <th>Name</th>
                <th>Email</th>
                <th>View Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php while($s = mysqli_fetch_assoc($students)): ?>
            <tr>
                <td><?php echo $s['studentid']; ?></td>
                <td><?php echo $s['username']; ?></td>
                <td><?php echo $s['email']; ?></td>
                <td><a href="viewstudent.php?id=<?php echo $s['uid']; ?>" class="btn btn-primary btn-sm">View Logbook</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
