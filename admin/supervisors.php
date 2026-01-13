<?php 
include "../config/database.php";
session_start();
if(isset($_SESSION['userid'])){
    $user_id = $_SESSION['userid'];
  $sql = "SELECT * FROM admin WHERE username = '$user_id'";
  $result = mysqli_query($conn,$sql);
  $output = mysqli_fetch_all($result,MYSQLI_ASSOC); 
  if($output){
    $user_details = $output[0];
    $user_id = $user_details['id'];
    $user_name = $user_details['username'];
  }else{
    header("Location:/logbook/" );
  }
 
}
else{
    header("Location:/logbook/" );
};
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>E-logbook</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
      .btn-primary {
  background-color: #6f2449d1;
  border: none;
}
.btn-primary:hover {
  background-color: #8c3160;
}
.form-select, .form-control {
  border-radius: 10px;
}
.add-supervisor-btn {
  margin-bottom: 20px;
}
    </style>
</head>

<body style="background-color: #6f2449d1; color: white;">
    <nav class="navbar navbar-expand-sm navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Electronic-Logbook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                        <a class="nav-link" href="./">home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="supervisors.php">supervisor</a>
                    </li>
             <li class="nav-item">
                        <a class="nav-link" href="industry.php">industries</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="progress.php">progress chart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">logout</a>
                    </li>
            </li>
        </ul>
      </div>
  </div>
</nav>
<section>
<div class="container">
<?php
// Fetch all supervisors
$sql = "SELECT * FROM supervisor";
$result = mysqli_query($conn, $sql);
$supervisors = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch industries from itsupervisor table using industry_id
$sqlIndustries = "SELECT id, name FROM itsupervisor ORDER BY name ASC";
$resultIndustries = mysqli_query($conn, $sqlIndustries);
$industries = mysqli_fetch_all($resultIndustries, MYSQLI_ASSOC);

$message = "";

if (isset($_POST['assign'])) {
    $supervisor_id = $_POST['supervisor'];
    $industry_id = $_POST['industry_id'];

    if (!empty($supervisor_id) && !empty($industry_id)) {
        $assignSQL = "INSERT INTO assignments (supervisor_id, industry_id) VALUES ('$supervisor_id', '$industry_id')";
        if (mysqli_query($conn, $assignSQL)) {
            // Get industry name for success message
            $industryNameSQL = "SELECT name FROM itsupervisor WHERE id = '$industry_id'";
            $industryResult = mysqli_query($conn, $industryNameSQL);
            $industryData = mysqli_fetch_assoc($industryResult);
            $industry_name = $industryData['name'];
            
            $message = "<div class='alert alert-success text-center mt-3'>Supervisor successfully assigned to $industry_name industry.</div>";
        } else {
            $message = "<div class='alert alert-danger text-center mt-3'>Error assigning supervisor: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center mt-3'>Please select both a supervisor and an industry.</div>";
    }
}
?>

<!-- Add Supervisor Button -->
<div class="text-end mb-3">
    <button type="button" class="btn btn-primary add-supervisor-btn" data-bs-toggle="modal" data-bs-target="#addSupervisorModal">
        Add Supervisor
    </button>
</div>

<h3 class="mb-4 text-center">Assign Supervisor to Industry</h3>
<?php echo $message; ?>

<form method="post" action="">
  <div class="row mb-3">
    <div class="col-md-6">
      <label for="supervisor" class="form-label">Select Supervisor</label>
      <select name="supervisor" id="supervisor" class="form-select" required>
        <option value="">-- Select Supervisor --</option>
        <?php foreach ($supervisors as $s): ?>
          <option value="<?php echo $s['id']; ?>"><?php echo ucfirst($s['username']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="industry_id" class="form-label">Select Industry</label>
      <select name="industry_id" id="industry_id" class="form-select" required>
        <option value="">-- Select Industry --</option>
        <?php foreach ($industries as $i): ?>
          <option value="<?php echo $i['id']; ?>"><?php echo ucfirst($i['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="text-center">
    <button type="submit" name="assign" class="btn btn-primary px-4">Assign</button>
  </div>
</form>

<hr class="my-4">

<h4 class="text-center mt-4">Current Assignments</h4>
<table class="table table-light table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>Supervisor</th>
      <th>Industry</th>
      <th>Assigned On</th>
    </tr>
  </thead>
  <tbody>
  <?php
    // Updated query to join with itsupervisor table
    $assignments = mysqli_query($conn, "SELECT a.*, s.username, i.name as industry_name 
      FROM assignments a 
      JOIN supervisor s ON s.id = a.supervisor_id 
      JOIN itsupervisor i ON i.id = a.industry_id 
      ORDER BY a.id DESC");
    $count = 1;
    while ($row = mysqli_fetch_assoc($assignments)) {
        echo "<tr>
            <td>{$count}</td>
            <td>{$row['username']}</td>
            <td>{$row['industry_name']}</td>
            <td>{$row['assigned_on']}</td>
          </tr>";
        $count++;
    }
  ?>
  </tbody>
</table>
</div>
</section>

<footer class="text-center mt-5">
  Copyright &copy; 2025
</footer>

<!-- Add Supervisor Modal -->
<div class="modal fade" id="addSupervisorModal" tabindex="-1" aria-labelledby="addSupervisorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSupervisorModalLabel">Add New Supervisor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="add_supervisor.php">
          <div class="mb-3">
            <label for="supervisor-name" class="col-form-label" style="color:black">Username:</label>
            <input type="text" name="username" class="form-control" id="supervisor-name" required>
          </div>
          <div class="mb-3">
            <label for="supervisor-password" class="col-form-label" style="color:black">Password:</label>
            <input type="password" name="password" class="form-control" id="supervisor-password" required>
          </div>
          <div class="text-center">
            <button type="submit" name="add_supervisor" class="btn btn-primary">Add Supervisor</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Existing Admin Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Assign Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
          <div class="mb-3">
            <label for="recipient-name" class="col-form-label">Username:</label>
            <input type="text" name="name" class="form-control" id="recipient-name">
          </div>
          <div class="mb-3">
            <label for="recipient-name" class="col-form-label">Password:</label>
            <input type="text" name="password" class="form-control" name password id="recipient-name">
          </div>
          <input type="submit" name="submit" class="btn btn-primary" value="assign">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
<script>
    var navBtn = document.getElementById("navBtn")
    var dropdownToggle = document.getElementById("dropdown")
    navBtn.onclick = ()=>{
        dropdownToggle.classList.toggle("show")
    }
</script>
</body>
</html>