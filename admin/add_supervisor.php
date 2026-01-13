<?php
include "../config/database.php";
session_start();

if(isset($_POST['add_supervisor'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "INSERT INTO supervisor (username,password) VALUES ('$username', '$password')";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: user.php?message=Supervisor added successfully");
    } else {
        header("Location: user.php?error=Error adding supervisor: " . mysqli_error($conn));
    }
}
?>