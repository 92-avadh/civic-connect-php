<?php
// apply_birth_certificate.php

require_once 'db_connection.php';

$error = '';
$success = '';
$today = date('Y-m-d'); // Get today's date for validation

// Check if user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&status=birth_cert_required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect form data
    $userId = $_SESSION['user_id'];
    $childFullName = trim($_POST['childFullName'] ?? '');
    $childDOB = $_POST['childDOB'] ?? '';
    $hospitalName = trim($_POST['hospitalName'] ?? '');
    $motherName = trim($_POST['motherName'] ?? '');

    // 2. Perform validation
    if (empty($childFullName) || empty($childDOB) || empty($hospitalName) || empty($motherName)) {
        $error = "All fields are required.";
    } elseif (strtotime($childDOB) > strtotime($today)) {
        $error = "Date of birth cannot be in the future.";
    } else {
        // 3. Insert application into database
        try {
            $stmt = $pdo->prepare("INSERT INTO birth_certificates (user_id, child_full_name, child_dob, hospital_name, mother_name) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$userId, $childFullName, $childDOB, $hospitalName, $motherName])) {
                $lastId = $pdo->lastInsertId();
                $applicationNumber = "SMC-BC-" . $lastId;
                $success = "Application submitted successfully! Your Application Number is: " . $applicationNumber;
                $childFullName = $childDOB = $hospitalName = $motherName = '';
            } else {
                $error = "Failed to submit application. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Birth Certificate Application PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred. Please try again later.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2>Apply for Birth Certificate</h2>
    <?php
    if (!empty($error)) {
        echo '<div style="color: red; text-align: center; margin-bottom: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px;">' . htmlspecialchars($error) . '</div>';
    }
    if (!empty($success)) {
        echo '<div style="color: green; text-align: center; margin-bottom: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px;">' . htmlspecialchars($success) . '</div>';
    }
    ?>
    <form action="" method="POST">
        <div class="form-group">
            <label for="childFullName">Child's Full Name</label>
            <input type="text" id="childFullName" name="childFullName" value="<?php echo htmlspecialchars($childFullName ?? ''); ?>" placeholder="Enter child's full name" required>
        </div>
        <div class="form-group">
            <label for="childDOB">Child's Date of Birth</label>
            <input type="date" id="childDOB" name="childDOB" value="<?php echo htmlspecialchars($childDOB ?? ''); ?>" max="<?php echo $today; ?>" required>
        </div>
        <div class="form-group">
            <label for="hospitalName">Hospital of Birth</label>
            <input type="text" id="hospitalName" name="hospitalName" value="<?php echo htmlspecialchars($hospitalName ?? ''); ?>" placeholder="Enter hospital name" required>
        </div>
        <div class="form-group">
            <label for="motherName">Mother's Full Name</label>
            <input type="text" id="motherName" name="motherName" value="<?php echo htmlspecialchars($motherName ?? ''); ?>" placeholder="Enter mother's full name" required>
        </div>
        <button type="submit" class="btn-primary">Submit Application</button>
    </form>
</section>