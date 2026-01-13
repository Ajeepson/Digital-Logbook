<?php
include "includes/database.php";
session_start();

if (!isset($_SESSION['itsupervisor_id'])) {
    header("Location: itsupervisorlogin.php");
    exit();
}

$supervisor_id = (int) $_SESSION['itsupervisor_id'];
$message = "";

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["signature"])) {
    $targetDir = "uploads/signatures/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["signature"]["name"]);
    $fileTmp = $_FILES["signature"]["tmp_name"];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ["png", "jpg", "jpeg"];

    if (in_array($fileExt, $allowedExts)) {
        $newFileName = "signature_" . $supervisor_id . "_" . time() . "." . $fileExt;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($fileTmp, $targetFile)) {
            // Update signature path in itsupervisor table
            $safePath = mysqli_real_escape_string($conn, $targetFile);
            $updateQuery = "UPDATE itsupervisor SET signature='$safePath' WHERE id=$supervisor_id";
            if ($conn->query($updateQuery)) {
                $message = "<div class='alert alert-success text-center'>Signature uploaded successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>Database update failed: " . $conn->error . "</div>";
            }
        } else {
            $message = "<div class='alert alert-danger text-center'>Failed to upload file.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>Invalid file type. Please upload a PNG or JPG image.</div>";
    }
}

// Fetch current signature (if any)
$sig_query = "SELECT signature FROM itsupervisor WHERE id=$supervisor_id";
$sig_result = $conn->query($sig_query);
$current_signature = "";
if ($sig_result && $sig_result->num_rows > 0) {
    $row = $sig_result->fetch_assoc();
    $current_signature = $row["signature"];
}
?>

<?php include "includes/supervisor_nav.php"; ?>

<div class="container mt-5">
    <div class="container mt-4">
  <a href="dashboard.php" class="btn btn-secondary mb-3">&laquo; Back</a>
    <div class="col-md-6 offset-md-3">
        <div class="card shadow p-4">
            
            <h4 class="text-center mb-3">Upload Your Signature</h4>

            <?php echo $message; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="signature" class="form-label">Select Signature Image (PNG/JPG)</label>
                    <input type="file" name="signature" id="signature" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload Signature</button>
            </form>

            <?php if (!empty($current_signature)): ?>
                <hr>
                <div class="text-center">
                    <h6 class="mb-3">Current Signature Preview:</h6>
                    <img src="<?php echo htmlspecialchars($current_signature); ?>" alt="Signature" class="img-fluid border p-2" style="max-height:150px;">
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

