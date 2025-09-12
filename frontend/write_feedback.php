<?php
// write_feedback.php

require_once 'db_connection.php';

$error = '';
$success = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&status=feedback_required");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $feedbackType = trim($_POST['feedbackType'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($feedbackType) || empty($message)) {
        $error = "Feedback Type and Message are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (user_id, feedback_type, message) VALUES (?, ?, ?)");
            if ($stmt->execute([$userId, $feedbackType, $message])) {
                $success = "Thank you! Your feedback has been submitted successfully.";
                $feedbackType = $message = '';
            } else {
                $error = "Failed to submit feedback. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Feedback PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred. Please try again later.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2>Write Feedback</h2>
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
            <label for="feedbackType">Feedback Type</label>
            <select id="feedbackType" name="feedbackType" required>
                <option value="">Select Type</option>
                <option value="Suggestion" <?php echo (($feedbackType ?? '') == 'Suggestion') ? 'selected' : ''; ?>>Suggestion</option>
                <option value="Complaint" <?php echo (($feedbackType ?? '') == 'Complaint') ? 'selected' : ''; ?>>Complaint</option>
                <option value="Appreciation" <?php echo (($feedbackType ?? '') == 'Appreciation') ? 'selected' : ''; ?>>Appreciation</option>
                <option value="Other" <?php echo (($feedbackType ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="8" placeholder="Please provide your detailed feedback here..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn-primary">Submit Feedback</button>
    </form>
</section>