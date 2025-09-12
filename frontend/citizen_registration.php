<?php
// citizen_registration.php
require_once 'db_connection.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect form data
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;

    // 2. Perform validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!$terms) {
        $error = "You must agree to the Terms and Conditions.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "This email is already registered. Please use a different email or login.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, 'citizen')");

                if ($stmt->execute([$firstName, $lastName, $email, $phone, $hashedPassword])) {
                    $success = "Registration successful! You can now login.";
                    $firstName = $lastName = $email = $phone = $password = $confirmPassword = '';
                    $terms = false;
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            error_log("Registration PDO Error: " . $e->getMessage());
            $error = "An unexpected database error occurred. Please try again later.";
        }
    }
}
?>

<section class="form-container">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    <h2>Citizen Registration</h2>
    <?php
    if (!empty($error)) {
        echo '<div style="color: red; text-align: center; margin-bottom: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px;">' . htmlspecialchars($error) . '</div>';
    }
    if (!empty($success)) {
        echo '<div style="color: green; text-align: center; margin-bottom: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px;">' . htmlspecialchars($success) . '</div>';
    }
    ?>
    <form action="" method="POST">
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="e.g., your@example.com" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" pattern="[0-9]{10}" placeholder="e.g., 9876543210" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye toggle-password"></i>
            </div>
        </div>
        <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <div class="password-wrapper">
                <input type="password" id="confirmPassword" name="confirmPassword" required>
                <i class="fas fa-eye toggle-password"></i>
            </div>
        </div>
        <div class="form-group">
            <input type="checkbox" id="terms" name="terms" <?php echo ($terms ?? false) ? 'checked' : ''; ?> required>
            <label for="terms" class="checkbox-label">I agree to the <a href="#">Terms and Conditions</a></label>
        </div>
        <button type="submit" class="btn-primary">Complete Registration</button>
    </form>
    <p class="text-center login-link">Already have an account? <a href="index.php?page=login">Login here</a></p>
</section>
