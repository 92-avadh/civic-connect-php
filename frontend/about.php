<?php
// about.php

// --- Data for the Development Team ---
// Replace these with your actual details and image paths
$developers = [
    [
        'name' => 'Avadh Dhameliya',
        'photo' => 'images/dev1.jpg', 
        'github' => 'https://github.com/92-avadh',
        'email' => 'dhameliyaavadh592@gmail.com',
        'phone' => '+91 92651 77693'
    ],
    [
        'name' => 'Ravi gajera',
        'photo' => 'images/ravi.jpg', 
        'github' => 'https://github.com/ravigajera-afk',
        'email' => 'ravigajera0906@gmail.com',
        'phone' => '+91 96386 41139'
    ],
    [
        'name' => 'Smit Bhingradiya',
        'photo' => 'images/dev3.jpg', // Placeholder
        'github' => 'https://github.com/Smit1879',
        'email' => 'bhingradiyasmit485@gmail.com',
        'phone' => '+91 78743 62579'
    ],
    [
        'name' => 'Zeel katrodiya',
        'photo' => 'images/dev4.jpg', // Placeholder
        'github' => 'https://github.com/zeelkatrodiya',
        'email' => 'zeelkatrodiya21@gmail.com',
        'phone' => '+91 90816 32716'
    ]
];
?>
<section class="about-section container mt-5">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>

    <h1 class="about-title">About CivicConnect</h1>

    <div class="section-card mb-5">
        <h2 class="section-heading text-center">Meet Our Team</h2>
        <div class="row justify-content-center">
            <?php foreach ($developers as $dev): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="developer-card">
                        <img src="<?php echo htmlspecialchars($dev['photo']); ?>" alt="<?php echo htmlspecialchars($dev['name']); ?>" class="dev-photo">
                        <h4 class="dev-name"><?php echo htmlspecialchars($dev['name']); ?></h4>
                        <div class="dev-contact">
                            <a href="mailto:<?php echo htmlspecialchars($dev['email']); ?>"><i class="fas fa-envelope"></i></a>
                            <a href="<?php echo htmlspecialchars($dev['github']); ?>" target="_blank"><i class="fab fa-github"></i></a>
                            <a href="tel:<?php echo htmlspecialchars($dev['phone']); ?>"><i class="fas fa-phone"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="about-content-card">
        <div class="mission-section">
            <h2>Our Mission</h2>
            <p>To empower citizens of Surat to actively participate in improving their city by providing an easy-to-use platform for reporting and tracking civic issues. Together, we can make Surat smarter, cleaner, and more liveable for everyone.</p>
        </div>

        <div class="how-it-works-section">
            <h2>How It Works</h2>
            <ol>
                <li><b>Register as a Citizen or Official.</b></li>
                <li><b>Report civic issues</b> with photos and detailed descriptions.</li>
                <li><b>Track resolution progress</b> in real-time.</li>
                <li><b>Learn about civic sense</b> and legal rights.</li>
                <li><b>Contribute to community development</b> and transparency.</li>
            </ol>
        </div>
    </div>
</section>