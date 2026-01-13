<?php 
include "../config/database.php";
session_start();
if(isset($_POST['submit'])){
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_SPECIAL_CHARS);
    $industry_id = filter_input(INPUT_POST, 'industry_id', FILTER_SANITIZE_NUMBER_INT);

    // Hash the password
    $hashed_password = $password;

    // Insert into database
    $sql = "INSERT INTO itsupervisor (Name, username, password, email, phone, fullname, state, Industry_id, company) 
            VALUES ('$name', '$username', '$hashed_password', '$email', '$phone', '$fullname', '$state', '$industry_id', NOW())";
    
    $result = mysqli_query($conn, $sql);

    if($result){
        header("Location: supervisors.php");
        exit;
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>Error in insertion: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>E-logbook - IT Supervisors</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
      .modal-content {
        border-radius: 10px;
        border: none;
        background-color: #f9f9fb;
        color:black;
      }
      .btn-primary {
        background-color: #6f2449d1;
        border: none;
      }
      .btn-primary:hover {
        background-color: #8c3160;
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
                        <a class="nav-link" href="user.php">students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="supervisors.php">Supervisors</a>
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
                </ul>
            </div>
        </div>
    </nav>

    <section>
        <div class="container-fluid">
            <?php
            $sql = "SELECT i.*, ind.name as industry_name 
                    FROM itsupervisor i 
                    LEFT JOIN industries ind ON i.Industry_id = ind.id";
            $result = mysqli_query($conn,$sql);
            $output = mysqli_fetch_all($result,MYSQLI_ASSOC); 
            $sum = 0;
            ?>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Registered Industries</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Register New Industry</button> 
            </div>
            
            <table class="table table-light">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Industry Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Location</th>
                        <th scope="col">Industry Type</th>
                        <th scope="col">Registered Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($output as $data): ?>
                    <tr>
                        <?php $sum++ ?>
                        <th scope="row"><?php echo $sum ?></th>
                        <td><?php echo $data['Name'] ?? 'N/A' ?></td>
                        <td><?php echo $data['username'] ?? 'N/A' ?></td>
                        <td><?php echo $data['email'] ?? 'N/A' ?></td>
                        <td><?php echo $data['phone'] ?? 'N/A' ?></td>
                        <td><?php echo $data['fullname'] ?? 'N/A' ?></td>
                        <td><?php echo $data['state'] ?? 'N/A' ?></td>
                        <td><?php echo $data['industry_name'] ?? 'N/A' ?></td>
                        <td><?php echo $data['company'] ?? 'N/A' ?></td>
                        <td><a href="delete_supervisor.php?id=<?php echo $data['id']?>" class="btn btn-danger btn-sm">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="text-center mt-5">
        Copyright &copy; 2025
    </footer>

    <!-- Register New Industry Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Register New Industry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
                        <div class="mb-3">
                            <label class="col-form-label">Industry/Company Name:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Username (for login):</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Phone Number:</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Contact Person Full Name:</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Location/State:</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Industry Type:</label>
                            <select name="industry_id" class="form-control" required>
                                <option value="">Select Industry Type</option>
                                <?php
                                $res = mysqli_query($conn, "SELECT * FROM industries ORDER BY name");
                                while($r = mysqli_fetch_assoc($res)){
                                    echo "<option value='{$r['id']}'>{$r['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <input type="submit" name="submit" class="btn btn-primary" value="Register Industry">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
</body>
</html>