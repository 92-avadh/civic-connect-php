<?php
// header.php
session_start();

// --- Define Core Variables ---
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['official_id']);
$is_official = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'official';
$current_page = $_GET['page'] ?? 'home';

// --- Server-Side Redirect Logic ---
if ($is_official && $current_page === 'home') {
    header("Location: index.php?page=official_dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CivicConnect Surat</title>
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/layout.css?v=1.4">
    
    <!-- Page-Specific Stylesheets -->
    <?php
    $page_to_css_map = [
      'home' => ['home.css', 'about_surat.css'],
      'login' => ['forms.css'],
      'citizen_registration' => ['forms.css'],
      'edit_profile' => ['forms.css', 'profile.css'],
      'write_feedback' => ['forms.css'],
      'report_an_issue' => ['forms.css'],
      'apply_birth_certificate' => ['forms.css'],
      'apply_death_certificate' => ['forms.css'],
      'apply_water_connection' => ['forms.css'],
      'pay_property_tax' => ['forms.css'],
      'track_application' => ['forms.css', 'issues.css'],
      'profile' => ['profile.css'],
      'view_issues' => ['issues.css'],
      'official_dashboard' => ['issues.css'],
      'about' => ['about.css'],
      'contact' => ['forms.css', 'contact.css'],
      'civic_sense' => ['civic_sense.css'],
      'law_literacy' => ['law_literacy.css'],
      'learn' => ['civic_sense.css'],
      'view_feedback' => ['issues.css'],
      'terms_of_service' => ['legal.css'],
      'privacy_policy' => ['legal.css'],
    ];

    if (isset($page_to_css_map[$current_page])) {
        foreach ($page_to_css_map[$current_page] as $stylesheet) {
            echo '<link rel="stylesheet" href="css/' . $stylesheet . '?v=1.4">';
        }
    }
    ?>
</head>
<body>
    <script>
        // This script ensures that opening a new tab starts the user at the home page.
        (function() {
            if (sessionStorage.getItem('tabInitialized') === null) {
                const currentPage = new URL(window.location.href).searchParams.get("page") || "home";
                if (currentPage !== "home" && currentPage !== "login") {
                    window.location.href = 'index.php?page=home';
                }
            }
        })();
    </script>
    <header class="header">
        <div class="container">
            <a href="index.php?page=home" class="logo"> <span class="logo-cc">CC</span>
                <span class="logo-name">CIVICCONNECT</span>
            </a>
            
            <nav class="nav">
                <ul>
                    <?php if ($is_official): ?>
                        <li><a href="index.php?page=official_dashboard">Dashboard</a></li>
                        <li><a href="index.php?page=view_issues">All Issues</a></li>
                        <li><a href="index.php?page=view_feedback">View Feedback</a></li>
                        <li><a href="index.php?page=profile">Profile</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=home">Home</a></li>
                        <li><a href="index.php?page=view_issues">View Issues</a></li>
                        <li><a href="index.php?page=report_an_issue">Report an Issue</a></li>
                        <li><a href="index.php?page=about">About</a></li>
                        <li><a href="index.php?page=contact">Contact</a></li>
                    <?php endif; ?>
                     <!-- Login/Logout button for mobile -->
                    <li class="mobile-auth-link">
                         <?php if ($is_logged_in): ?>
                            <a href="index.php?page=logout" class="logout-btn">Logout</a>
                        <?php else: ?>
                            <a href="index.php?page=login" class="login-btn-mobile">Login</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>

            <div class="auth-buttons-desktop">
                <?php if ($is_logged_in): ?>
                    <a href="index.php?page=logout" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
                <?php endif; ?>
            </div>

            <!-- Hamburger Icon -->
            <div class="menu-toggle">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </header>

