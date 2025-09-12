<?php
// apply_water_connection.php
// This file contains the content for the New Water Connection application form and handles its submission.

require_once 'db_connection.php';

$error = '';
$success = '';

// Check if user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&status=water_connection_required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect form data
    $userId = $_SESSION['user_id'];
    $applicantName = trim($_POST['applicantName'] ?? '');
    $propertyAddress = trim($_POST['propertyAddress'] ?? '');
    $propertyProofPath = null;

    // 2. Perform validation for required fields, including the file upload
    if (empty($applicantName) || empty($propertyAddress)) {
        $error = "Applicant Name and Property Address are required.";
    } elseif (!isset($_FILES['propertyProof']) || $_FILES['propertyProof']['error'] == UPLOAD_ERR_NO_FILE) {
        $error = "Address Proof is required. Please upload a file.";
    } else {
        // 3. Handle file upload (if validation passes)
        $fileTmpPath = $_FILES['propertyProof']['tmp_name'];
        $fileName = $_FILES['propertyProof']['name'];
        $fileSize = $_FILES['propertyProof']['size'];
        $fileType = $_FILES['propertyProof']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedFileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $maxFileSize = 10 * 1024 * 1024; // 10 MB

        if (!in_array($fileExtension, $allowedFileExtensions)) {
            $error = "Invalid file type. Only JPG, JPEG, PNG, GIF, PDF are allowed for Address Proof.";
        } elseif ($fileSize > $maxFileSize) {
            $error = "File size exceeds 10MB limit for Address Proof.";
        } else {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = 'uploads/'; // Relative path from index.php
            $destPath = $uploadFileDir . $newFileName;

            if (!is_dir($uploadFileDir)) {
                 mkdir($uploadFileDir, 0755, true);
            }

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $propertyProofPath = $destPath;
            } else {
                $error = "Error uploading property proof. Please try again.";
            }
        }
    }

    // 4. Insert application into database if there are no errors
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO water_connections (user_id, applicant_name, property_address, property_proof_path) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$userId, $applicantName, $propertyAddress, $propertyProofPath])) {
                $lastId = $pdo->lastInsertId();
                $applicationNumber = "SMC-WC-" . $lastId;
                $success = "Application submitted successfully! Your Application Number is: " . $applicationNumber;
                $applicantName = $propertyAddress = '';
            } else {
                $error = "Failed to submit application. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Water Connection Application PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred. Please try again later.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2>Apply for New Water Connection</h2>
    <?php
    if (!empty($error)) {
        echo '<div style="color: red; text-align: center; margin-bottom: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px;">' . htmlspecialchars($error) . '</div>';
    }
    if (!empty($success)) {
        echo '<div style="color: green; text-align: center; margin-bottom: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px;">' . htmlspecialchars($success) . '</div>';
    }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="applicantName">Applicant's Full Name</label>
            <input type="text" id="applicantName" name="applicantName" value="<?php echo htmlspecialchars($applicantName ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="propertyAddress">Full Property Address</label>
            <textarea id="propertyAddress" name="propertyAddress" rows="4" placeholder="Enter the full address for the new connection" required><?php echo htmlspecialchars($propertyAddress ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="propertyProof">Address Proof</label>
            <input type="file" id="propertyProof" name="propertyProof" accept=".pdf, .jpg, .jpeg, .png" required>
            <small>Accepted formats: PDF, JPG, PNG (Max 10MB)</small>
        </div>
        <button type="submit" class="btn-primary">Submit Application</button>
    </form>
</section>