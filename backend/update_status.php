<?php
session_start();
require_once '../db_connection.php'; // Note the '..' to go up one directory

// Security check: only officials can access this script
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'official') {
    header("Location: ../index.php?page=login&status=unauthorized");
    exit();
}

$redirect_page = 'official_dashboard'; // Default redirect
$app_id = $_POST['app_id'] ?? null;
$app_type = $_POST['app_type'] ?? null;
$new_status = $_POST['new_status'] ?? null;
$issue_id = $_POST['issue_id'] ?? null; // For old issue updates

// --- Determine which type of item is being updated ---
$table_name = '';
$allowed_statuses = [];

if ($issue_id) { // --- Handle Issue Status Update ---
    $table_name = 'issues';
    $allowed_statuses = ['open', 'in-progress', 'resolved', 'rejected'];
    $app_id = $issue_id;
    $redirect_page = 'view_issues';
} elseif ($app_type) { // --- Handle Application Status Update ---
    $allowed_statuses = ['pending', 'in-progress', 'approved', 'rejected'];
    switch ($app_type) {
        case 'birth':
            $table_name = 'birth_certificates';
            break;
        case 'death':
            $table_name = 'death_certificates';
            break;
        case 'water':
            $table_name = 'water_connections';
            break;
    }
}


// --- Proceed with update if data is valid ---
if ($table_name && $app_id && in_array($new_status, $allowed_statuses)) {
    try {
        $stmt = $pdo->prepare("UPDATE `$table_name` SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $app_id])) {
            header("Location: ../index.php?page=$redirect_page&status=success");
            exit();
        } else {
            header("Location: ../index.php?page=$redirect_page&status=error");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Status Update PDO Error: " . $e->getMessage());
        header("Location: ../index.php?page=$redirect_page&status=error");
        exit();
    }
}

// Redirect if accessed directly or with invalid data
header("Location: ../index.php?page=official_dashboard");
exit();