<?php 
include "../config/database.php";
session_start();
if(isset($_POST['submit'])){
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $studentid = filter_input(INPUT_POST, 'studentid', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
    $adress = filter_input(INPUT_POST, 'adress', FILTER_SANITIZE_SPECIAL_CHARS);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_SPECIAL_CHARS);
    $gradyear = filter_input(INPUT_POST, 'gradyear', FILTER_SANITIZE_SPECIAL_CHARS);
    $industry_id = filter_input(INPUT_POST, 'industry_id', FILTER_SANITIZE_NUMBER_INT);

    // Hash the password
    $hashed_password = $password;

    // Insert into database
    $sql = "INSERT INTO students (username, password, studentid, email, phone, adress, state, gradyear, industry_id) 
            VALUES ('$name', '$hashed_password', '$studentid', '$email', '$phone', '$adress', '$state', '$gradyear', '$industry_id')";
    
    $result = mysqli_query($conn, $sql);

    if($result){
        header("Location: user.php");
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
    <title>E-logbook</title>
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
                </ul>
            </div>
        </div>
    </nav>

    <section>
        <div class="container-fluid">
            <?php
            // Fetch students with industry names from itsupervisor table
            $sql = "SELECT s.*, i.Name as industry_name 
                    FROM students s 
                    LEFT JOIN itsupervisor i ON s.industry_id = i.id";
            $result = mysqli_query($conn,$sql);
            $output = mysqli_fetch_all($result,MYSQLI_ASSOC); 
            $sum = 0;
            ?>
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add User</button> 
            
            <table class="table table-light mt-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">student</th>
                        <th scope="col">reg no</th>
                        <th scope="col">phone</th>
                        <th scope="col">course</th>
                        <th scope="col">level</th>
                        <th scope="col">graduation year</th>
                        <th scope="col">industry</th>
                        <th scope="col">action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($output as $data): ?>
                    <tr>
                        <?php $sum++ ?>
                        <th scope="row"><?php echo $sum ?></th>
                        <td><?php echo $data['username']?></td>
                        <td><?php echo $data['studentid']?></td>
                        <td><?php echo $data['phone']?></td>
                        <td><?php echo $data['adress']?></td>
                        <td><?php echo $data['state']?></td>
                        <td><?php echo $data['gradyear']?></td>
                        <td><?php echo $data['industry_name'] ?? 'Not Assigned' ?></td>
                        <td><a href="delete.php?id=<?php echo $data['uid']?>" class="btn btn-danger">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="text-center mt-5">
        Copyright &copy; 2025
    </footer>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
                        <div class="mb-3">
                            <label class="col-form-label">Full Name (Username):</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Reg Number:</label>
                            <input type="text" name="studentid" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Phone:</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Course:</label>
                            <input type="text" name="adress" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Level:</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Graduation Year:</label>
                            <input type="text" name="gradyear" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label">Industry:</label>
                            <select name="industry_id" class="form-control" required>
                                <option value="">Select Industry</option>
                                <?php
                                // Fetch industries from itsupervisor table
                                $res = mysqli_query($conn, "SELECT * FROM itsupervisor ORDER BY Name");
                                while($r = mysqli_fetch_assoc($res)){
                                    echo "<option value='{$r['id']}'>{$r['Name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <input type="submit" name="submit" class="btn btn-primary" value="Add">
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