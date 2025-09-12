<?php
// apply_death_certificate.php

require_once 'db_connection.php';

$error = '';
$success = '';
$today = date('Y-m-d'); // Get today's date for validation

// Check if user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&status=death_cert_required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect form data
    $userId = $_SESSION['user_id'];
    $deceasedName = trim($_POST['deceasedName'] ?? '');
    $dateOfDeath = $_POST['dateOfDeath'] ?? '';
    $placeOfDeath = trim($_POST['placeOfDeath'] ?? '');

    // 2. Perform validation
    if (empty($deceasedName) || empty($dateOfDeath) || empty($placeOfDeath)) {
        $error = "Deceased Name, Date of Death, and Place of Death are required.";
    } elseif (strtotime($dateOfDeath) > strtotime($today)) {
        $error = "Date of death cannot be in the future.";
    } else {
        // 3. Insert application into database
        try {
            $stmt = $pdo->prepare("INSERT INTO death_certificates (user_id, deceased_name, date_of_death, place_of_death) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$userId, $deceasedName, $dateOfDeath, $placeOfDeath])) {
                $lastId = $pdo->lastInsertId();
                $applicationNumber = "SMC-DC-" . $lastId;
                $success = "Application submitted successfully! Your Application Number is: " . $applicationNumber;
                $deceasedName = $dateOfDeath = $placeOfDeath = '';
            } else {
                $error = "Failed to submit application. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Death Certificate Application PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred. Please try again later.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2>Apply for Death Certificate</h2>
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
            <label for="deceasedName">Deceased's Full Name</label>
            <input type="text" id="deceasedName" name="deceasedName" value="<?php echo htmlspecialchars($deceasedName ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="dateOfDeath">Date of Death</label>
            <input type="date" id="dateOfDeath" name="dateOfDeath" value="<?php echo htmlspecialchars($dateOfDeath ?? ''); ?>" max="<?php echo $today; ?>" required>
        </div>
        <div class="form-group">
            <label for="placeOfDeath">Place of Death</label>
            <input type="text" id="placeOfDeath" name="placeOfDeath" value="<?php echo htmlspecialchars($placeOfDeath ?? ''); ?>" placeholder="Enter hospital or address" required>
        </div>
        <button type="submit" class="btn-primary">Submit Application</button>
    </form>
</section>