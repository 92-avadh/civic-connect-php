<?php
session_start();
require_once '../db_connection.php'; // Note the '..' to go up one directory

// Security check: only officials can access this script
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'official') {
    header("Location: ../index.php?page=login&status=unauthorized");
    exit();
}

$app_id = $_POST['app_id'] ?? null;
$app_type = $_POST['app_type'] ?? null;
$table_name = '';

// Determine the table name from the application type
if ($app_type) {
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
        default:
            // Invalid type, redirect with error
            header("Location: ../index.php?page=official_dashboard&status=delete_error");
            exit();
    }
}

// Proceed with delete if data is valid
if ($table_name && $app_id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM `$table_name` WHERE id = ?");
        if ($stmt->execute([$app_id])) {
            header("Location: ../index.php?page=official_dashboard&status=delete_success");
            exit();
        } else {
            header("Location: ../index.php?page=official_dashboard&status=delete_error");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Application Delete PDO Error: " . $e->getMessage());
        header("Location: ../index.php?page=official_dashboard&status=delete_error");
        exit();
    }
}

// Redirect if accessed directly or with invalid data
header("Location: ../index.php?page=official_dashboard");
exit();
?>