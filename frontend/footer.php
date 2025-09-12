<?php
// footer.php
?>
<footer class="footer">
    <div class="container">
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php?page=home">Home</a></li>
                <li><a href="index.php?page=view_issues">View Issues</a></li>
                <li><a href="index.php?page=report_an_issue">Report an Issue</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Resources</h3>
            <ul>
                <li><a href="index.php?page=civic_sense">Civic Sense</a></li>
                <li><a href="index.php?page=law_literacy">Law Literacy</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Legal</h3>
            <ul>
                <li><a href="index.php?page=privacy_policy">Privacy Policy</a></li>
                <li><a href="index.php?page=terms_of_service">Terms of Service</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact</h3>
            <p><i class="fas fa-envelope"></i> support@civicconnect.gov.in</p>
            <p><i class="fas fa-phone"></i> +91 261-2234567</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> CivicConnect Surat. All rights reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Mobile Menu Toggle ---
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.nav');
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', function() {
            nav.classList.toggle('nav-active');
        });
    }

    // --- Password Visibility Toggle ---
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');
    togglePasswordIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const passwordInput = this.previousElementSibling;
            
            if (passwordInput && (passwordInput.type === 'password' || passwordInput.type === 'text')) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            }
        });
    });
});
</script>
</body>
</html>