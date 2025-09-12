<?php
// view_feedback.php (For Officials)

require_once 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'official') {
    header("Location: index.php?page=login&status=unauthorized_access");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM feedback ORDER BY filed_at DESC");
    $stmt->execute();
    $feedbacks = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching feedback.");
}
?>

<section class="container mt-5">
    <h1 class="text-center mb-5">Submitted Feedback</h1>

    <div class="feedback-container">
        <?php if (empty($feedbacks)): ?>
            <div class="empty-state-card">
                <i class="fas fa-comment-slash"></i>
                <p>No feedback has been submitted yet.</p>
            </div>
        <?php else: ?>
            <div class="issues-grid">
                <?php foreach($feedbacks as $feedback): ?>
                    <div class="issue-card">
                        <h4><?php echo htmlspecialchars($feedback['feedback_type']); ?></h4>
                        <p><?php echo htmlspecialchars($feedback['message']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .empty-state-card { background: #f8f9fa; padding: 60px; text-align: center; border-radius: 8px; color: #6c757d; }
    .empty-state-card i { font-size: 3em; margin-bottom: 15px; }

    /* The .issues-grid and .issue-card styles are already in issues.css,
       which is loaded by the header for this page, so no duplicate CSS is needed here.
       The class names have been updated in the HTML to match. */
</style>