<?php
// login.php
require_once 'db_connection.php';

$error = '';
$success = '';
$is_official_login = isset($_GET['type']) && $_GET['type'] === 'official';
$login_type = $is_official_login ? 'Official' : 'User';

if (isset($_SESSION['user_id']) || isset($_SESSION['official_id'])) {
    header("Location: index.php?page=home");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            if ($is_official_login) {
                // --- OFFICIAL LOGIN LOGIC ---
                $stmt = $pdo->prepare("SELECT id, password, full_name FROM officials WHERE email = ?");
                $stmt->execute([$email]);
                $official = $stmt->fetch();

                if ($official && password_verify($password, $official['password'])) {
                    // Set session for official
                    $_SESSION['official_id'] = $official['id'];
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $official['full_name'];
                    $_SESSION['user_role'] = 'official';
                    header("Location: index.php?page=official_dashboard");
                    exit();
                } else {
                    $error = "Invalid official credentials.";
                }

            } else {
                // --- CITIZEN LOGIN LOGIC ---
                $stmt = $pdo->prepare("SELECT id, password, first_name, last_name, role, is_active FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['is_active'] == 0) {
                        $error = "Your account has been deactivated.";
                    } else {
                        // Set session for citizen
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['user_role'] = $user['role'];
                        header("Location: index.php?page=home");
                        exit();
                    }
                } else {
                    $error = "Invalid user credentials.";
                }
            }
        } catch (PDOException $e) {
            error_log("Login PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2><?php echo $login_type; ?> Login</h2>
    <?php if (!empty($error)) { echo '<div class="error-message">' . htmlspecialchars($error) . '</div>'; } ?>
    
    <form action="" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye toggle-password"></i>
            </div>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    
    <?php if (!$is_official_login): ?>
        <p class="text-center login-link">Don't have an account? <a href="index.php?page=citizen_registration">Register here</a></p>
    <?php endif; ?>
</section>