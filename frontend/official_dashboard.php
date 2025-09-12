<?php
// official_dashboard.php

require_once 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'official') {
    header("Location: index.php?page=login&status=unauthorized_access");
    exit();
}

$applications = [];
$error = '';

try {
    // Fetch all types of applications
    $stmt_birth = $pdo->prepare("SELECT id, child_full_name as name, status, 'birth' as type, null as attachment FROM birth_certificates ORDER BY applied_at DESC");
    $stmt_birth->execute();
    $applications['birth'] = $stmt_birth->fetchAll();

    $stmt_death = $pdo->prepare("SELECT id, deceased_name as name, status, 'death' as type, null as attachment FROM death_certificates ORDER BY applied_at DESC");
    $stmt_death->execute();
    $applications['death'] = $stmt_death->fetchAll();

    $stmt_water = $pdo->prepare("SELECT id, applicant_name as name, status, 'water' as type, property_proof_path as attachment FROM water_connections ORDER BY applied_at DESC");
    $stmt_water->execute();
    $applications['water'] = $stmt_water->fetchAll();

} catch (PDOException $e) {
    error_log("Official Dashboard PDO Error: " . $e->getMessage());
    $error = "Error fetching applications.";
}

function get_status_tag($status) {
    $class = '';
    $text = '';
    switch ($status) {
        case 'pending':
            $class = 'status-submitted';
            $text = 'Submitted';
            break;
        case 'in-progress':
            $class = 'status-review';
            $text = 'In Review';
            break;
        case 'approved':
            $class = 'status-approved';
            $text = 'Approved';
            break;
        case 'rejected':
            $class = 'status-rejected';
            $text = 'Rejected';
            break;
    }
    return '<span class="status-tag ' . $class . '">' . $text . '</span>';
}

function render_actions($app, $type) {
    if ($app['status'] === 'pending') {
        echo '<form action="backend/update_status.php" method="POST"><input type="hidden" name="app_id" value="'.$app['id'].'"><input type="hidden" name="app_type" value="'.$type.'"><input type="hidden" name="new_status" value="in-progress"><button type="submit" class="btn-action start-review">Start Review</button></form>';
    } elseif ($app['status'] === 'in-progress') {
        echo '<form action="backend/update_status.php" method="POST" style="display:inline-block;"><input type="hidden" name="app_id" value="'.$app['id'].'"><input type="hidden" name="app_type" value="'.$type.'"><input type="hidden" name="new_status" value="approved"><button type="submit" class="btn-action approve">Approve</button></form>';
        echo '<form action="backend/update_status.php" method="POST" style="display:inline-block;"><input type="hidden" name="app_id" value="'.$app['id'].'"><input type="hidden" name="app_type" value="'.$type.'"><input type="hidden" name="new_status" value="rejected"><button type="submit" class="btn-action reject">Reject</button></form>';
    }
}
?>

<section class="container mt-5">
    <h1 class="text-center mb-5">Admin Dashboard</h1>

    <div class="app-section">
        <h3>Birth Certificate Applications (<?php echo count($applications['birth']); ?>)</h3>
        <div class="app-table">
            <div class="app-table-header">
                <div class="col-app-no">APP NO.</div>
                <div class="col-name">CHILD'S NAME</div>
                <div class="col-actions">ACTIONS</div>
            </div>
            <?php foreach ($applications['birth'] as $app): ?>
                <div class="app-table-row">
                    <div class="col-app-no">SMC-BC-<?php echo $app['id']; ?></div>
                    <div class="col-name"><?php echo htmlspecialchars($app['name']); ?></div>
                    <div class="col-actions">
                        <?php echo get_status_tag($app['status']); ?>
                        <?php render_actions($app, 'birth'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="app-section">
        <h3>Death Certificate Applications (<?php echo count($applications['death']); ?>)</h3>
        <div class="app-table">
            <div class="app-table-header">
                <div class="col-app-no">APP NO.</div>
                <div class="col-name">DECEASED'S NAME</div>
                <div class="col-actions">ACTIONS</div>
            </div>
             <?php foreach ($applications['death'] as $app): ?>
                <div class="app-table-row">
                    <div class="col-app-no">SMC-DC-<?php echo $app['id']; ?></div>
                    <div class="col-name"><?php echo htmlspecialchars($app['name']); ?></div>
                    <div class="col-actions">
                        <?php echo get_status_tag($app['status']); ?>
                        <?php render_actions($app, 'death'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="app-section">
        <h3>Water Connection Applications (<?php echo count($applications['water']); ?>)</h3>
        <div class="app-table">
            <div class="app-table-header">
                <div class="col-app-no">APP NO.</div>
                <div class="col-name">APPLICANT</div>
                <div class="col-attachment">ATTACHMENTS</div>
                <div class="col-actions">ACTIONS</div>
            </div>
            <?php foreach ($applications['water'] as $app): ?>
                <div class="app-table-row">
                    <div class="col-app-no">SMC-WC-<?php echo $app['id']; ?></div>
                    <div class="col-name"><?php echo htmlspecialchars($app['name']); ?></div>
                    <div class="col-attachment">
                        <?php if(!empty($app['attachment'])): ?>
                            <a href="<?php echo htmlspecialchars($app['attachment']); ?>" target="_blank">Open Attachment</a>
                        <?php endif; ?>
                    </div>
                    <div class="col-actions">
                        <?php echo get_status_tag($app['status']); ?>
                        <?php render_actions($app, 'water'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .app-section { margin-bottom: 40px; }
    .app-section h3 { font-size: 1.5em; margin-bottom: 15px; }
    .app-table { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; }
    .app-table-header, .app-table-row { display: flex; align-items: center; padding: 15px; border-bottom: 1px solid #dee2e6; }
    .app-table-row:last-child { border-bottom: none; }
    .app-table-header { font-weight: bold; color: #6c757d; font-size: 0.9em; text-transform: uppercase; }
    .col-app-no, .col-name, .col-attachment, .col-actions { padding: 0 10px; }
    .col-app-no { flex: 0 0 15%; }
    .col-name { flex: 1; }
    .col-attachment { flex: 0 0 20%; }
    .col-actions { flex: 0 0 35%; display: flex; align-items: center; gap: 10px; }
    .status-tag { padding: 5px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; }
    .status-submitted { background-color: #e9ecef; color: #495057; }
    .status-review { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
    .btn-action { border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 0.8em; }
    .start-review { background-color: #ffc107; color: #212529; }
    .approve { background-color: #28a745; color: white; }
    .reject { background-color: #dc3545; color: white; }
</style>