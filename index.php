<?php
// index.php - Main Application Router

// Header contains session_start() and initial logic
include 'frontend/header.php';

echo '<main>'; // Start the main content wrapper

// Determine which page to load, default to 'home'
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        // --- SCRIPT TO INITIALIZE A NEW TAB ---
        echo "<script>sessionStorage.setItem('tabInitialized', 'true');</script>";

        // --- Page-specific Data ---
        $officials = [
            ['name' => 'Shri Shalini Agrawal,IAS', 'post' => 'Municipal Commissioner', 'photo' => 'images/a.jpg'],
            ['name' => 'Shri Dakshesh Mavani', 'post' => 'Mayor', 'photo' => 'images/b.jpg'],
            ['name' => 'Shri Narendra Patil', 'post' => 'Deputy Mayor', 'photo' => 'images/c.jpg'],
            ['name' => 'Shri Rajan Patel', 'post' => 'Chairman,Standing Committee', 'photo' => 'images/d.jpg']
        ];
        $monuments = [
            ['name' => 'Surat Castle', 'photo' => 'images/Surat Castle (Old Fort).jpg'],
            ['name' => 'Dutch Garden', 'photo' => 'images/Dutch Garden.jpg'],
            ['name' => 'Sardar Patel Museum', 'photo' => 'images/Sardar Patel Museum.jpg'],
            ['name' => 'Chintamani Jain Temple', 'photo' => 'images/Chintamani Jain Temple.jpg']
        ];
        
        // --- Prepare dynamic text for links ---
        $trackAppLinkText = $is_logged_in ? "Track My Applications" : "Track Application";
        ?>
        
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h1>Welcome to CivicConnect Surat</h1>
                <p>Your one-stop portal for all municipal services and civic engagement.</p>
            </div>
        </section>

        <!-- Quick Links Section -->
        <section class="quick-links container">
            <h2>Quick Links</h2>
            <div class="links-grid">
                <a href="index.php?page=pay_property_tax" class="link-item">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <p>Pay Property Tax</p>
                </a>
                <a href="index.php?page=apply_birth_certificate" class="link-item">
                    <i class="fas fa-baby"></i>
                    <p>Birth Certificate</p>
                </a>
                <a href="index.php?page=apply_death_certificate" class="link-item">
                    <i class="fas fa-file-medical"></i>
                    <p>Death Certificate</p>
                </a>
                <a href="index.php?page=apply_water_connection" class="link-item">
                    <i class="fas fa-water"></i>
                    <p>Water Connection</p>
                </a>
                <a href="index.php?page=write_feedback" class="link-item">
                    <i class="fas fa-edit"></i>
                    <p>Write Feedback</p>
                </a>
                <a href="index.php?page=track_application" class="link-item">
                    <i class="fas fa-search"></i>
                    <p><?php echo $trackAppLinkText; ?></p>
                </a>
            </div>
        </section>

        <!-- News and Learn Section -->
        <section class="two-column-section container">
            <div class="news-updates">
                <h2>News & Updates</h2>
                <div class="news-item">
                    <p class="date"><?php echo date("F d, Y", strtotime("-6 days")); ?></p>
                    <a href="#" class="title">Monsoon preparedness meeting held by Municipal Commissioner.</a>
                </div>
                <div class="news-item">
                    <p class="date"><?php echo date("F d, Y", strtotime("-7 days")); ?></p>
                    <a href="#" class="title">New public park inaugurated in the Adajan area.</a>
                </div>
                <div class="news-item">
                    <p class="date"><?php echo date("F d, Y", strtotime("-9 days")); ?></p>
                    <a href="#" class="title">Property tax deadline extended to October 31st.</a>
                </div>
            </div>
            <div class="learn-participate">
                <h2>Learn & Participate</h2>
                <div class="links-grid">
                    <a href="index.php?page=civic_sense" class="link-item">
                        <i class="fas fa-lightbulb"></i>
                        <p>Civic Sense Corner</p>
                    </a>
                    <a href="index.php?page=law_literacy" class="link-item">
                        <i class="fas fa-gavel"></i>
                        <p>Law Literacy Lounge</p>
                    </a>
                </div>
            </div>
        </section>
        
        <?php
        // Only show the "Join" section if the user is NOT logged in
        if (!$is_logged_in) :
        ?>
        <section class="join-section container">
            <h2>Join CivicConnect</h2>
            <p>Register as a citizen or an official to get started.</p>
            <div class="join-options">
                <div class="join-card">
                    <i class="fas fa-user-circle"></i>
                    <h3>Citizen</h3>
                    <p>Report issues, track progress, and help improve our city.</p>
                    <a href="index.php?page=citizen_registration" class="btn">Register as Citizen</a>
                </div>
                <div class="join-card">
                    <i class="fas fa-building government-icon"></i>
                    <h3>Government Official</h3>
                    <p>Manage issues, coordinate with departments, and serve citizens.</p>
                    <a href="index.php?page=login&type=official" class="btn">Official Login</a>
                </div>
            </div>
        </section>
        <?php
        endif; // End of check for logged-in user
        ?>
        
        <!-- Leadership Section -->
        <section class="container mt-5">
            <div class="section-card">
                <h2 class="section-heading">Meet Our Leadership</h2>
                <p class="section-intro">The team dedicated to serving the city of Surat.</p>
                <div class="row justify-content-center">
                    <?php foreach ($officials as $official): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="official-card text-center h-100">
                                <img src="<?php echo htmlspecialchars($official['photo']); ?>" alt="<?php echo htmlspecialchars($official['name']); ?>" class="official-photo mb-3 rounded-circle">
                                <h4 class="mb-1"><?php echo htmlspecialchars($official['name']); ?></h4>
                                <p class="text-muted mb-1"><?php echo htmlspecialchars($official['post']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Landmarks Section -->
        <section class="container mt-5">
            <div class="section-card">
                <h2 class="section-heading">Landmarks of Surat</h2>
                <p class="section-intro">Discover the rich heritage of our city.</p>
                <div class="row justify-content-center">
                    <?php foreach ($monuments as $monument): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="monument-card h-100">
                                <img src="<?php echo htmlspecialchars($monument['photo']); ?>" alt="<?php echo htmlspecialchars($monument['name']); ?>" class="monument-photo mb-3 rounded">
                                <h4 class="mb-1"><?php echo htmlspecialchars($monument['name']); ?></h4>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        break;

    // --- Page Routing ---
    case 'view_issues': include 'frontend/view_issues.php'; break;
    case 'report_an_issue': include 'frontend/report_an_issue.php'; break;
    case 'about': include 'frontend/about.php'; break;
    case 'contact': include 'frontend/contact.php'; break;
    case 'login': include 'frontend/login.php'; break;
    case 'profile': include 'frontend/profile.php'; break;
    case 'logout': include 'frontend/logout.php'; break;
    case 'citizen_registration': include 'frontend/citizen_registration.php'; break;
    case 'official_dashboard': include 'frontend/official_dashboard.php'; break;
    case 'edit_profile': include 'frontend/edit_profile.php'; break;
    case 'pay_property_tax': include 'frontend/pay_property_tax.php'; break;
    case 'apply_birth_certificate': include 'frontend/apply_birth_certificate.php'; break;
    case 'apply_death_certificate': include 'frontend/apply_death_certificate.php'; break;
    case 'apply_water_connection': include 'frontend/apply_water_connection.php'; break;
    case 'write_feedback': include 'frontend/write_feedback.php'; break;
    case 'track_application': include 'frontend/track_application.php'; break;
    case 'learn': include 'frontend/learn.php'; break;
    case 'civic_sense': include 'frontend/civic_sense.php'; break;
    case 'law_literacy': include 'frontend/law_literacy.php'; break;
    case 'view_feedback': include 'frontend/view_feedback.php'; break;
    case 'terms_of_service': include 'frontend/terms_of_service.php'; break;
    case 'privacy_policy': include 'frontend/privacy_policy.php'; break;
    
    // Admin dashboard placeholder
    case 'admin_dashboard':
        echo '<div class="container mt-5 text-center"><h1>Admin Dashboard - Coming Soon!</h1></div>';
        break;

    // Default 404 Page
    default:
        ?>
        <div class="container mt-5 text-center">
            <h1>404 - Page Not Found</h1>
            <p>The page you are looking for does not exist or has been moved.</p>
            <a href="index.php?page=home" class="btn btn-primary mt-3">Go to Home</a>
        </div>
        <?php
        break;
}

echo '</main>'; // End the main content wrapper

// Include the footer
include 'frontend/footer.php';
?>
