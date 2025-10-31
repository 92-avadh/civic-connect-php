<?php
// track_application.php
// Assuming db_connection.php sets up the $pdo variable and starts the session
require_once 'db_connection.php'; 

// Check user login status
$is_logged_in = isset($_SESSION['user_id']);
$current_user_id = $_SESSION['user_id'] ?? null;

$error = '';
$message = '';
$searchedApplication = null; // For the 'track' tab
$applicationToEdit = null;   // For the 'edit' tab
$applicationIdInput = '';
$applicationType = '';
$view = $_GET['view'] ?? 'track';

function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'pending':
        case 'submitted':
            return 'status-open';
        case 'in-progress':
        case 'in review':
            return 'status-inprogress';
        case 'approved':
        case 'resolved':
            return 'status-resolved';
        case 'rejected': return 'status-rejected';
        default: return '';
    }
}

// --- LOGIC FOR TRACKING AN APPLICATION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['track_application'])) {
    $applicationIdInput = trim($_POST['application_id']);
    $parts = explode('-', $applicationIdInput);
    
    if (count($parts) === 3 && is_numeric($parts[2])) {
        $type = $parts[1];
        $table_name = '';
        $title_prefix = '';
        $number_column = 'application_number';

        switch ($type) {
            case 'BC': $table_name = 'birth_certificates'; $title_prefix = 'Birth Certificate'; break;
            case 'DC': $table_name = 'death_certificates'; $title_prefix = 'Death Certificate'; break;
            case 'WC': $table_name = 'water_connections'; $title_prefix = 'Water Connection'; break;
            default: $error = "Invalid application type in ID.";
        }

        if ($table_name) {
            try {
                $stmt = $pdo->prepare("SELECT *, '$title_prefix' as app_title FROM `$table_name` WHERE $number_column = ?");
                $stmt->execute([$applicationIdInput]);
                $searchedApplication = $stmt->fetch();
                if (!$searchedApplication) $message = "No application found with that ID.";
            } catch (PDOException $e) {
                $error = "Database error. Please try again.";
            }
        }
    } else {
        $error = "Invalid application number format. Please use the format SMC-XX-123.";
    }
}

// --- LOGIC FOR FINDING AN APPLICATION TO EDIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['find_to_edit'])) {
    if (!$is_logged_in) {
        $error = "You must be logged in to edit an application.";
    } else {
        $applicationIdInput = trim($_POST['application_id']);
        $parts = explode('-', $applicationIdInput);

        if (count($parts) === 3 && is_numeric($parts[2])) {
            $type = $parts[1];
            switch ($type) {
                case 'BC': $table_name = 'birth_certificates'; $applicationType = 'birth'; break;
                case 'DC': $table_name = 'death_certificates'; $applicationType = 'death'; break;
                case 'WC': $table_name = 'water_connections'; $applicationType = 'water'; break;
                default: $error = "Invalid application type.";
            }

            if (!$error) {
                $stmt = $pdo->prepare("SELECT * FROM `$table_name` WHERE application_number = ?");
                $stmt->execute([$applicationIdInput]);
                $applicationToEdit = $stmt->fetch();

                if (!$applicationToEdit) {
                    $error = "Application not found.";
                } elseif ($applicationToEdit['user_id'] != $current_user_id) {
                    $error = "You do not have permission to edit this application.";
                    $applicationToEdit = null;
                } elseif (strtolower($applicationToEdit['status']) !== 'submitted' && strtolower($applicationToEdit['status']) !== 'pending') {
                    $error = "This application is under review and cannot be edited.";
                    $applicationToEdit = null;
                }
            }
        } else {
            $error = "Invalid application number format.";
        }
    }
}

// --- LOGIC FOR UPDATING THE APPLICATION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_application'])) {
    if (!$is_logged_in) {
        $error = "Your session expired. Please log in again.";
    } else {
        $app_id = $_POST['app_id'];
        $app_type = $_POST['app_type'];

        try {
            switch ($app_type) {
                case 'birth':
                    $stmt = $pdo->prepare("UPDATE birth_certificates SET child_full_name = ?, date_of_birth = ?, place_of_birth = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$_POST['child_full_name'], $_POST['date_of_birth'], $_POST['place_of_birth'], $app_id, $current_user_id]);
                    break;
                case 'death':
                    $stmt = $pdo->prepare("UPDATE death_certificates SET deceased_full_name = ?, date_of_death = ?, place_of_death = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$_POST['deceased_full_name'], $_POST['date_of_death'], $_POST['place_of_death'], $app_id, $current_user_id]);
                    break;
                case 'water':
                    $stmt = $pdo->prepare("UPDATE water_connections SET applicant_name = ?, property_address = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$_POST['applicant_name'], $_POST['property_address'], $app_id, $current_user_id]);
                    break;
            }
            if ($stmt->rowCount() > 0) {
                $message = "Application updated successfully!";
            } else {
                $error = "Update failed. The application might be locked or you don't have permission.";
            }
        } catch (PDOException $e) {
            $error = "Database error during update.";
        }
    }
}
?>

<link rel="stylesheet" href="path/to/your/main.css"> 
<style>
    .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
    .text-center { text-align: center; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mb-5 { margin-bottom: 3rem; }
    .mt-5 { margin-top: 3rem; }
    .form-container { background: #f9f9f9; padding: 2rem; border-radius: 8px; border: 1px solid #ddd; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input[type="text"], .form-group input[type="date"], .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; }
    .btn-primary { background-color: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; display: block; width: 100%; font-size: 1rem; }
    .btn-primary:hover { background-color: #0056b3; }
    .btn-primary:disabled { background-color: #ccc; cursor: not-allowed; }
    .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .view-navigation { text-align: center; margin-bottom: 2rem; border-bottom: 1px solid #ddd; }
    .view-navigation a { display: inline-block; padding: 0.8rem 1.5rem; text-decoration: none; font-weight: 500; color: #555; border-bottom: 3px solid transparent; margin-bottom: -1px; }
    .view-navigation a.active { color: #0056b3; border-bottom-color: #0056b3; }
    .issue-card { border: 1px solid #ddd; padding: 1.5rem; border-radius: 5px; margin-top: 1.5rem; }
    .status-open { border-left: 5px solid #ffc107; }
    .status-inprogress { border-left: 5px solid #007bff; }
    .status-resolved { border-left: 5px solid #28a745; }
    .status-rejected { border-left: 5px solid #dc3545; }
    .note { text-align: center; margin-top: 1rem; color: #666; font-size: 0.9em; }
</style>

<section class="container mt-5">
    <h2 class="text-center mb-4">About Your Application</h2>

    <div class="view-navigation">
        <a href="?page=track_application&view=track" class="<?php echo $view === 'track' ? 'active' : ''; ?>">Track Application</a>
        <a href="?page=track_application&view=edit" class="<?php echo $view === 'edit' ? 'active' : ''; ?>">Edit Application</a>
    </div>

    <?php if (!empty($error)) { echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>'; } ?>
    <?php if (!empty($message)) { echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>'; } ?>

    <?php if ($view === 'track'): ?>
        <div class="form-container mb-5">
            <h3 class="text-center mb-3">Search by Application ID</h3>
            <form action="?page=track_application&view=track" method="POST">
                <input type="hidden" name="track_application" value="1">
                <div class="form-group">
                    <label for="application_id">Enter Application Number</label>
                    <input type="text" id="application_id" name="application_id" placeholder="e.g., SMC-BC-1" value="<?php echo htmlspecialchars($applicationIdInput); ?>" required>
                </div>
                <button type="submit" class="btn-primary">Track Application</button>
            </form>
        </div>

        <?php if ($searchedApplication): ?>
            <div class="issue-card <?php echo getStatusClass($searchedApplication['status']); ?>">
                <h3><?php echo htmlspecialchars($searchedApplication['app_title']); ?></h3>
                <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($searchedApplication['status'])); ?></p>
                <p><strong>Submitted on:</strong> <?php echo date("F j, Y", strtotime($searchedApplication['applied_at'] ?? $searchedApplication['created_at'])); ?></p>
            </div>
        <?php endif; ?>

    <?php elseif ($view === 'edit'): ?>
        <div class="form-container mb-5">
            <h3 class="text-center mb-3">Edit Your Application</h3>
            
            <?php if (!$is_logged_in): ?>
                <div class="alert alert-danger">You must be logged in to edit an application.</div>
            <?php else: ?>
                <form action="?page=track_application&view=edit" method="POST" class="mb-5">
                    <input type="hidden" name="find_to_edit" value="1">
                    <div class="form-group">
                        <label for="application_id_edit">Enter Application Number</label>
                        <input type="text" id="application_id_edit" name="application_id" placeholder="e.g., SMC-BC-1" value="<?php echo htmlspecialchars($applicationIdInput); ?>" required>
                    </div>
                    <button type="submit" class="btn-primary">Find Application</button>
                </form>

                <?php if ($applicationToEdit): ?>
                    <hr>
                    <h4 class="text-center" style="margin-top: 2rem;">Editing Application: <?php echo htmlspecialchars($applicationToEdit['application_number']); ?></h4>
                    <form action="?page=track_application&view=edit" method="POST">
                        <input type="hidden" name="update_application" value="1">
                        <input type="hidden" name="app_id" value="<?php echo $applicationToEdit['id']; ?>">
                        <input type="hidden" name="app_type" value="<?php echo $applicationType; ?>">

                        <?php if ($applicationType === 'birth'): ?>
                            <div class="form-group">
                                <label for="child_full_name">Child's Full Name</label>
                                <input type="text" id="child_full_name" name="child_full_name" value="<?php echo htmlspecialchars($applicationToEdit['child_full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($applicationToEdit['date_of_birth']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="place_of_birth">Place of Birth</label>
                                <input type="text" id="place_of_birth" name="place_of_birth" value="<?php echo htmlspecialchars($applicationToEdit['place_of_birth']); ?>" required>
                            </div>
                        <?php elseif ($applicationType === 'death'): ?>
                            <div class="form-group">
                                <label for="deceased_full_name">Deceased's Full Name</label>
                                <input type="text" id="deceased_full_name" name="deceased_full_name" value="<?php echo htmlspecialchars($applicationToEdit['deceased_full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="date_of_death">Date of Death</label>
                                <input type="date" id="date_of_death" name="date_of_death" value="<?php echo htmlspecialchars($applicationToEdit['date_of_death']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="place_of_death">Place of Death</label>
                                <input type="text" id="place_of_death" name="place_of_death" value="<?php echo htmlspecialchars($applicationToEdit['place_of_death']); ?>" required>
                            </div>
                        <?php elseif ($applicationType === 'water'): ?>
                            <div class="form-group">
                                <label for="applicant_name">Applicant Name</label>
                                <input type="text" id="applicant_name" name="applicant_name" value="<?php echo htmlspecialchars($applicationToEdit['applicant_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="property_address">Property Address</label>
                                <textarea id="property_address" name="property_address" required><?php echo htmlspecialchars($applicationToEdit['property_address']); ?></textarea>
                            </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </form>
                <?php endif; ?>
                <p class="note">Note: You can only edit applications that have a "Pending" or "Submitted" status.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>