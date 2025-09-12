<?php
// track_application.php
require_once 'db_connection.php';

$error = '';
$message = '';
$searchedApplication = null;
$applicationIdInput = '';

function getStatusClass($status) {
    switch ($status) {
        case 'pending': return 'status-open';
        case 'in-progress': return 'status-inprogress';
        case 'approved': return 'status-resolved';
        case 'rejected': return 'status-rejected';
        default: return '';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'])) {
    $applicationIdInput = trim($_POST['application_id']);
    
    // Parse the application ID
    $parts = explode('-', $applicationIdInput);
    
    if (count($parts) === 3 && is_numeric($parts[2])) {
        $prefix = $parts[0]; // SMC
        $type = $parts[1];   // BC, DC, WC
        $id = (int)$parts[2];
        
        $table_name = '';
        $title_prefix = '';

        switch ($type) {
            case 'BC':
                $table_name = 'birth_certificates';
                $title_prefix = 'Birth Certificate';
                break;
            case 'DC':
                $table_name = 'death_certificates';
                $title_prefix = 'Death Certificate';
                break;
            case 'WC':
                $table_name = 'water_connections';
                $title_prefix = 'Water Connection';
                break;
            default:
                $error = "Invalid application number format.";
        }

        if ($table_name) {
            try {
                $stmt = $pdo->prepare("SELECT *, '$title_prefix' as app_title FROM `$table_name` WHERE id = ?");
                $stmt->execute([$id]);
                $searchedApplication = $stmt->fetch();
                if (!$searchedApplication) {
                    $message = "No application found with that ID.";
                }
            } catch (PDOException $e) {
                $error = "Database error. Please try again.";
            }
        }
    } else {
        $error = "Invalid application number format. Please use the format SMC-XX-123.";
    }
}
?>

<section class="container mt-5">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2 class="text-center mb-4">Track Your Application</h2>

    <div class="form-container mb-5">
        <h3 class="text-center mb-3">Search by Application ID</h3>
        <?php if (!empty($error)) { echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>'; } ?>
        <?php if (!empty($message)) { echo '<div class="alert alert-info">' . htmlspecialchars($message) . '</div>'; } ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="application_id">Enter Application Number</label>
                <input type="text" id="application_id" name="application_id" placeholder="e.g., SMC-BC-1" value="<?php echo htmlspecialchars($applicationIdInput); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Track Application</button>
        </form>
    </div>

    <?php if ($searchedApplication): ?>
        <h3 class="text-center mb-3">Search Result</h3>
        <div class="issues-grid">
            <div class="issue-card <?php echo getStatusClass($searchedApplication['status']); ?>">
                <h3><?php echo htmlspecialchars($searchedApplication['app_title']); ?></h3>
                <p class="status <?php echo htmlspecialchars($searchedApplication['status']); ?>">
                    Status: <?php echo htmlspecialchars(ucfirst($searchedApplication['status'])); ?>
                </p>
                <p class="filed-at">Submitted on: <?php echo date("Y-m-d H:i", strtotime($searchedApplication['applied_at'])); ?></p>
            </div>
        </div>
    <?php endif; ?>
</section>