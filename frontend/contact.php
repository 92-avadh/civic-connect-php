<?php
// contact.php
// This file contains only the content for the Contact Us page.
// It is designed to be included by index.php.
// No <head>, <body>, <header>, <footer> tags are needed here.

// IMPORTANT: If you have any PHP processing logic for form submission (e.g., sending emails),
// it should go BEFORE the HTML output, ideally at the very top of this file.
/*
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $fullName = $_POST['fullName'];
    $emailAddress = $_POST['emailAddress'];
    $message = $_POST['message'];

    // Example: Sending an email (requires proper mail server setup)
    // $to = "support@civicconnect.gov.in";
    // $subject = "Contact Form Submission from " . $fullName;
    // $headers = "From: " . $emailAddress . "\r\n" .
    //            "Reply-To: " . $emailAddress . "\r\n" .
    //            "Content-type: text/plain; charset=UTF-8";
    //
    // if (mail($to, $subject, $message, $headers)) {
    //     $success = "Your message has been sent successfully!";
    // } else {
    //     $error = "Failed to send message. Please try again later.";
    // }
}
*/
?>

<section class="contact-section">
    <div class="container">
        <div class="back-to-home">
            <a href="index.php?page=home"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
        <h1 class="contact-title">Contact Us</h1>
        <p class="contact-intro">We welcome your feedback and inquiries. Please use the form below to get in touch with us.</p>

        <div class="contact-grid">
            <div class="contact-form-card">
                <h2>Send us a Message</h2>
                <form action="" method="POST"> <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" required>
                    </div>
                    <div class="form-group">
                        <label for="emailAddress">Email Address</label>
                        <input type="email" id="emailAddress" name="emailAddress" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Submit Feedback</button>
                </form>
            </div>

            <div class="contact-info-card">
                <h2>Our Office</h2>
                <address>
                    <p>Surat Municipal Corporation</p>
                    <p>Muglisara, Main Road</p>
                    <p>Surat, Gujarat 395003</p>
                    <p>India</p>
                </address>
                <p>Phone: +91 261-2234567</p>
                <p>Email: <a href="mailto:support@civicconnect.gov.in">support@civicconnect.gov.in</a></p>

                <h2 class="map-heading">Our Location</h2>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.577934275037!2d72.82761921493501!3d21.19593998592233!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e595df178c1%3A0x743285e68b31a5b!2sSurat%20Municipal%20Corporation!5e0!3m2!1sen!2sin!4v1662402749717!5m2!1sen!2sin"
                        width="100%"
                        height="300"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>