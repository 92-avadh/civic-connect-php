<?php
// edit_profile.php
require_once 'db_connection.php';

$error = '';
$success = '';
$is_official = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'official';
$is_citizen = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'citizen';
$user_data = null;

if (!$is_official && !$is_citizen) {
    header("Location: index.php?page=login");
    exit();
}

try {
    if ($is_official) {
        $stmt = $pdo->prepare("SELECT full_name FROM officials WHERE id = ?");
        $stmt->execute([$_SESSION['official_id']]);
        $user_data = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_data = $stmt->fetch();
        if ($user_data) {
            $user_data['full_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
        }
    }
} catch (PDOException $e) {
    $error = "Error loading profile data.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName'] ?? '');

    if (empty($fullName)) {
        $error = "Full name is required.";
    } else {
        try {
            if ($is_official) {
                $stmt = $pdo->prepare("UPDATE officials SET full_name = ? WHERE id = ?");
                $stmt->execute([$fullName, $_SESSION['official_id']]);
                $success = "Name updated successfully!";
                $user_data['full_name'] = $fullName;
            } else {
                // For citizens, split the full name into first and last
                $nameParts = explode(' ', $fullName, 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?");
                $stmt->execute([$firstName, $lastName, $_SESSION['user_id']]);
                $success = "Name updated successfully!";
                $user_data['full_name'] = $fullName;
            }
        } catch (PDOException $e) {
            $error = "Failed to update name.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=profile"><i class="fas fa-arrow-left"></i> Back to Profile</a>
    </div>
    <h2>Edit Name</h2>

    <?php if (!empty($error)) { echo '<div class="error-message">' . htmlspecialchars($error) . '</div>'; } ?>
    <?php if (!empty($success)) { echo '<div class="success-message">' . htmlspecialchars($success) . '</div>'; } ?>

    <?php if ($user_data): ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
            </div>
            <button type="submit" class="btn-primary">Update Name</button>
        </form>
    <?php else: ?>
        <p class="text-center text-danger">Unable to load profile data.</p>
    <?php endif; ?>
</section>