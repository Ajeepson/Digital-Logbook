<?php
include "includes/database.php";
session_start();
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM itsupervisor WHERE username=? AND password=?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();


    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['itsupervisor_id'] = $row['id'];
        $_SESSION['itsupervisor_name'] = $row['fullname'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>IT Supervisor Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="col-md-4 offset-md-4">
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">ND I Electronic-Logbook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/logbook/">Back</a>
                    </li>
            </li>
        </ul>
      </div>
  </div>
</nav>

        <div class="card p-4">
            <h4 class="text-center mb-3">IT Supervisor Login</h4>
            <?php if (!empty($error)): ?>
                <p class="text-danger text-center"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <input type="submit" name="login" value="Login" class="btn btn-primary w-100">
            </form>
        </div>
    </div>
</div>
</body>
</html>
