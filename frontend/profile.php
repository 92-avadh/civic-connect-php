<?php
// profile.php

require_once 'db_connection.php';

// Determine who is logged in
$is_official = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'official';
$is_citizen = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'citizen';

$profile_data = null;

// Redirect if nobody is logged in
if (!$is_official && !$is_citizen) {
    header("Location: index.php?page=login");
    exit();
}

try {
    if ($is_official) {
        // --- FETCH OFFICIAL PROFILE ---
        $stmt = $pdo->prepare("SELECT full_name, email, employee_id, department_id FROM officials WHERE id = ?");
        $stmt->execute([$_SESSION['official_id']]);
        $profile_data = $stmt->fetch();
        // Manually add the full_name to a consistent key if it's not already there
        if($profile_data) {
            $profile_data['full_name'] = $profile_data['full_name'];
        }
    } elseif ($is_citizen) {
        // --- FETCH CITIZEN PROFILE ---
        $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, created_at, is_active FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile_data = $stmt->fetch();
        if($profile_data) {
             $profile_data['full_name'] = $profile_data['first_name'] . ' ' . $profile_data['last_name'];
        }
    }

} catch (PDOException $e) {
    error_log("Profile PDO Error: " . $e->getMessage());
    die("Error fetching profile data.");
}

?>

<section class="profile-card container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2>Your Profile</h2>

    <?php if ($profile_data): ?>
        <div class="profile-info">
            <p><strong>Full Name:</strong> 
                <?php echo htmlspecialchars($profile_data['full_name']); ?>
                <a href="index.php?page=edit_profile" style="font-size: 0.8em; margin-left: 10px;"><i class="fas fa-pencil-alt"></i></a>
            </p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($profile_data['email']); ?></p>

            <?php if ($is_official): ?>
                <p><strong>Phone:</strong> Not Applicable</p>
                <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($profile_data['employee_id']); ?></p>
                <p><strong>Department ID:</strong> <?php echo htmlspecialchars($profile_data['department_id']); ?></p>
            <?php elseif ($is_citizen): ?>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile_data['phone']); ?></p>
                <p><strong>Registration Date:</strong> <?php echo date("Y-m-d", strtotime($profile_data['created_at'])); ?></p>
                <p><strong>Account Status:</strong> <?php echo ($profile_data['is_active'] == 1) ? '<span style="color: green; font-weight: bold;">Active</span>' : '<span style="color: red; font-weight: bold;">Deactivated</span>'; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</section>