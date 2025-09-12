<?php
// report_an_issue.php

require_once 'db_connection.php';

$error = '';
$success = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&status=report_issue_required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $attachmentPath = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = 'uploads/';
        $destPath = $uploadFileDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $attachmentPath = $destPath;
        } else {
            $error = "Error uploading photo.";
        }
    }

    if (empty($category) || empty($location) || empty($description)) {
        $error = "Category, Location, and Description are required.";
    }

    if (empty($error)) {
        try {
            // Ensure this saves to the 'issues' table
            $stmt = $pdo->prepare("INSERT INTO issues (user_id, category, location, description, attachment_path, status) VALUES (?, ?, ?, ?, ?, 'open')");
            if ($stmt->execute([$userId, $category, $location, $description, $attachmentPath])) {
                $success = "Issue Reported Succesfully!";
            } else {
                $error = "Failed to report issue.";
            }
        } catch (PDOException $e) {
            error_log("Report Issue PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred.";
        }
    }
}
?>

<?php if (!empty($success)): ?>
    <section class="container mt-5">
        <div style="text-align: center; padding: 60px; background-color: #fff; border-radius: 8px;">
            <h2 style="color: #28a745; font-size: 2em;"><?php echo htmlspecialchars($success); ?></h2>
            <p style="color: #6c757d; margin-top: 15px;">You will be redirected shortly...</p>
        </div>
    </section>

    <script>
        setTimeout(function() {
            window.location.href = 'index.php?page=view_issues';
        }, 2000); // 2000 milliseconds = 2 seconds
    </script>
<?php else: ?>
    <section class="form-container">
        <div class="back-to-home">
            <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
        <h2>Report an Issue</h2>
        <?php if(!empty($error)): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., Near City Plus, Adajan" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">Select a Category</option>
                    <option value="Infrastructure">Infrastructure</option>
                    <option value="Sanitation">Sanitation</option>
                    <option value="Traffic">Traffic</option>
                    <option value="Public Safety">Public Safety</option>
                    <option value="Environment">Environment</option>
                    <option value="Drainage">Drainage</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Detailed Description</label>
                <textarea id="description" name="description" rows="6" required></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Upload a Photo (Optional)</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>
            <button type="submit" class="btn-primary">Submit Issue</button>
        </form>
    </section>
<?php endif; ?>