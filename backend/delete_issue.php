<?php
session_start();
require_once '../db_connection.php'; // Note the '..' to go up one directory

// Security check: only officials can access this script
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'official') {
    header("Location: ../index.php?page=login&status=unauthorized");
    exit();
}

$issue_id = $_POST['issue_id'] ?? null;
$table_name = 'issues'; // The table is always 'issues' for this page

// Proceed with delete if data is valid
if ($table_name && $issue_id) {
    try {
        // Optional: First, check for an attachment and delete it from the server
        $stmt_find = $pdo->prepare("SELECT attachment_path FROM issues WHERE id = ?");
        $stmt_find->execute([$issue_id]);
        $issue = $stmt_find->fetch();

        if ($issue && !empty($issue['attachment_path'])) {
            // Check if file exists and delete it
            // The path is relative to index.php, so we need '..'
            if (file_exists('../' . $issue['attachment_path'])) {
                unlink('../' . $issue['attachment_path']);
            }
        }

        // Now, delete the issue from the database
        $stmt = $pdo->prepare("DELETE FROM `$table_name` WHERE id = ?");
        if ($stmt->execute([$issue_id])) {
            header("Location: ../index.php?page=view_issues&status=delete_success");
            exit();
        } else {
            header("Location: ../index.php?page=view_issues&status=delete_error");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Issue Delete PDO Error: " . $e->getMessage());
        header("Location: ../index.php?page=view_issues&status=delete_error");
        exit();
    }
}

// Redirect if accessed directly or with invalid data
header("Location: ../index.php?page=view_issues");
exit();
?>