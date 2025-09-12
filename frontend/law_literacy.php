<?php
// law_literacy.php
// This file contains the HTML structure and content for the Law Literacy Lounge.
// It is designed to be included by index.php.

// Data for Key Rights
$rights = [
    ['title' => 'Right to Information (RTI)', 'description' => 'Access information from government bodies, promoting transparency and accountability.'],
    ['title' => 'Right to Grievance Redressal', 'description' => 'File complaints or grievances against civic issues and expect a timely response and resolution.'],
    ['title' => 'Right to Clean Environment', 'description' => 'Demand a clean and healthy living environment from local authorities as a fundamental right.'],
    ['title' => 'Right to Participate', 'description' => 'Participate in local governance decisions through elections, public hearings, and civic engagement platforms like CivicConnect.']
];

// Data for Key Duties
$duties = [
    ['title' => 'Pay Taxes', 'description' => 'Fulfill your tax obligations (property tax, etc.) which are essential to fund public services and infrastructure.'],
    ['title' => 'Obey Laws', 'description' => 'Abide by local laws and regulations, including traffic rules, municipal bylaws, and other civic guidelines.'],
    ['title' => 'Civic Participation', 'description' => 'Actively report issues, provide constructive feedback, and participate in community improvement initiatives.'],
    ['title' => 'Protect Public Property', 'description' => 'Contribute to the upkeep and preservation of public infrastructure, parks, and facilities.'],
    ['title' => 'Environmental Responsibility', 'description' => 'Act responsibly towards the environment by conserving resources and encouraging sustainable practices.']
];
?>

<div class="container mt-5">
    <div class="back-to-home">
        <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    
    <div class="section-card">
        <h1 class="section-heading">Law Literacy Lounge</h1>
        <p class="section-intro">Understand your rights, duties, and the legal framework.</p>

        <h2 class="section-heading mt-4">Key Rights</h2>
        <div class="row justify-content-center">
            <?php foreach ($rights as $right): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="civic-sense-card h-100">
                        <h4><?php echo htmlspecialchars($right['title']); ?></h4>
                        <p><?php echo htmlspecialchars($right['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="section-heading mt-5">Key Duties</h2>
        <div class="row justify-content-center">
            <?php foreach ($duties as $duty): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="civic-sense-card h-100">
                        <h4><?php echo htmlspecialchars($duty['title']); ?></h4>
                        <p><?php echo htmlspecialchars($duty['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

