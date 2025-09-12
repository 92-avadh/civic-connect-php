<?php
// view_issues.php

require_once 'db_connection.php';

$issues = [];
$error = '';
$is_official = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'official';
$is_citizen = isset($_SESSION['user_id']) && !$is_official;

try {
    if ($is_official) {
        // --- OFFICIALS: Fetch all issues ---
        $stmt = $pdo->prepare("SELECT i.*, u.first_name, u.last_name FROM issues i LEFT JOIN users u ON i.user_id = u.id ORDER BY i.filed_at DESC");
        $stmt->execute();
        $issues = $stmt->fetchAll();
    } elseif ($is_citizen) {
        // --- LOGGED-IN CITIZENS: Fetch their own issues ---
        $stmt = $pdo->prepare("SELECT * FROM issues WHERE user_id = ? ORDER BY filed_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $issues = $stmt->fetchAll();
    }

} catch (PDOException $e) {
    error_log("View Issues PDO Error: " . $e->getMessage());
    $error = "Error fetching issues. Please try again later.";
}

function getStatusClass($status) {
    switch ($status) {
        case 'open': return 'status-open';
        case 'in-progress': return 'status-inprogress';
        case 'resolved': return 'status-resolved';
        case 'rejected': return 'status-rejected';
        default: return '';
    }
}
?>

<section class="container mt-5">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>

    <h2 class="text-center mb-4"><?php echo $is_official ? 'All Reported Issues' : 'Your Reported Issues'; ?></h2>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div id="status-alert" class="alert alert-success text-center">Status updated successfully!</div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
        <div id="status-alert" class="alert alert-danger text-center">Failed to update status.</div>
    <?php endif; ?>
    
    <?php if (empty($issues)): ?>
        <div class="text-center alert alert-info">
            No issues found.
        </div>
    <?php else: ?>
        <div class="issues-grid">
            <?php foreach ($issues as $issue): ?>
                <div class="issue-card <?php echo htmlspecialchars(getStatusClass($issue['status'])); ?>">
                    <h3><?php echo htmlspecialchars($issue['category']); ?></h3>
                    <?php if ($is_official): ?>
                        <p class="text-muted small">
                            Filed by: <?php echo htmlspecialchars($issue['first_name'] . ' ' . $issue['last_name']); ?>
                        </p>
                    <?php endif; ?>
                    <p class="status <?php echo htmlspecialchars(strtolower($issue['status'])); ?>">
                        <?php echo htmlspecialchars(ucfirst($issue['status'])); ?>
                    </p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($issue['location']); ?></p>
                    <p><strong>Details:</strong> <?php echo nl2br(htmlspecialchars($issue['description'])); ?></p>
                    <p class="filed-at">Filed on: <?php echo date("Y-m-d H:i", strtotime($issue['filed_at'])); ?></p>

                    <?php if (!empty($issue['attachment_path'])): ?>
                        <p class="mt-3">
                            <strong>Attachment:</strong> <a href="<?php echo htmlspecialchars($issue['attachment_path']); ?>" target="_blank">View Attachment</a>
                        </p>
                    <?php endif; ?>

                    <?php if ($is_official): ?>
                        <hr>
                        <div class="status-actions">
                            <?php
                            $next_status = '';
                            $button_text = '';
                            $button_class = 'btn-primary';
                            $is_disabled = false;

                            if ($issue['status'] == 'open') {
                                $next_status = 'in-progress';
                                $button_text = 'Mark as In-Progress';
                            } elseif ($issue['status'] == 'in-progress') {
                                $next_status = 'resolved';
                                $button_text = 'Mark as Resolved';
                                $button_class = 'btn-success';
                            } else {
                                $is_disabled = true;
                                $button_text = 'Action Complete';
                            }
                            ?>
                            <form action="backend/update_status.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                <input type="hidden" name="new_status" value="<?php echo $next_status; ?>">
                                <button type="submit" class="btn <?php echo $button_class; ?> btn-sm" <?php if($is_disabled) echo 'disabled'; ?>>
                                    <?php echo $button_text; ?>
                                </button>
                            </form>
                            
                            <?php if ($issue['status'] != 'rejected' && !$is_disabled): ?>
                                <form action="backend/update_status.php" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>">
                                    <input type="hidden" name="new_status" value="rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<style>
    .status-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusAlert = document.getElementById('status-alert');
    
    // If the status message exists, hide it after 2 seconds
    if (statusAlert) {
        setTimeout(function() {
            statusAlert.style.transition = 'opacity 0.5s ease';
            statusAlert.style.opacity = '0';
            setTimeout(function() {
                statusAlert.style.display = 'none';
            }, 500); // Wait for the fade out to finish
        }, 2000); // 2000 milliseconds = 2 seconds
    }

    // Clean the URL to prevent the message from showing on refresh
    if (window.history.replaceState) {
        const url = new URL(window.location.href);
        if (url.searchParams.has('status')) {
            url.searchParams.delete('status');
            window.history.replaceState({path: url.href}, '', url.href);
        }
    }
});
</script>