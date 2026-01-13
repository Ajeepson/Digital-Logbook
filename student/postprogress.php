<?php
include "../config/database.php";
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Get full student record using username
    $sql = "SELECT * FROM students WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $output = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($output) {
        $user_details = $output[0];
        $stud_id = $user_details['uid']; // student UID
        $user_name = $user_details['username'];

        if (isset($_POST['submit'])) {
            // Collect and sanitize inputs
            $week = filter_input(INPUT_POST, 'week', FILTER_SANITIZE_SPECIAL_CHARS);
            $week_end = filter_input(INPUT_POST, 'week_end', FILTER_SANITIZE_SPECIAL_CHARS);
            $mon = filter_input(INPUT_POST, 'mon', FILTER_SANITIZE_SPECIAL_CHARS);
            $tue = filter_input(INPUT_POST, 'tue', FILTER_SANITIZE_SPECIAL_CHARS);
            $wed = filter_input(INPUT_POST, 'wed', FILTER_SANITIZE_SPECIAL_CHARS);
            $thur = filter_input(INPUT_POST, 'thur', FILTER_SANITIZE_SPECIAL_CHARS);
            $fri = filter_input(INPUT_POST, 'fri', FILTER_SANITIZE_SPECIAL_CHARS);
            $sat = filter_input(INPUT_POST, 'sat', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = "pending";

            // File upload handling
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $file_name = $_FILES['upload']['name'];
            $file_size = $_FILES['upload']['size'];
            $file_temp = $_FILES['upload']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Upload directory (relative to THIS file)
            $target_dir = __DIR__ . "/uploads/";
            $target_file = $target_dir . basename($file_name);

            // Ensure upload directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (in_array($file_ext, $allowed_ext)) {
                if ($file_size <= 1000000) {
                    if (move_uploaded_file($file_temp, $target_file)) {
                        // Save relative path for database (not full local path)
                        $relative_path = "uploads/" . basename($file_name);

                        // ✅ Insert correct student ID (uid)
                        $sql = "INSERT INTO progress (stud_id, week, week_end, mon, tue, wed, thur, fri, sat, attachment, status)
                                VALUES ('$stud_id', '$week', '$week_end', '$mon', '$tue', '$wed', '$thur', '$fri', '$sat', '$relative_path', '$status')";

                        if (mysqli_query($conn, $sql)) {
                            header("Location: index.php");
                            exit();
                        } else {
                            echo "<p style='color:red;'>Database insert failed: " . mysqli_error($conn) . "</p>";
                        }
                    } else {
                        echo "<p style='color:red;'>Failed to upload file.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>File size is too large (max 1MB).</p>";
                }
            } else {
                echo "<p style='color:red;'>Invalid file format. Only JPG, PNG, and GIF allowed.</p>";
            }
        }
    } else {
        header("Location: /logbook/userlogin.php");
        exit();
    }
} else {
    header("Location: /logbook/userlogin.php");
    exit();
}
?>
