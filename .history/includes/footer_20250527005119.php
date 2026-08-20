<style>
footer {
    background-color: white;
    padding: 20px 0;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    margin-top: 30px;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    text-align: center;
}

.footer-logo {
    margin-bottom: 15px;
}

.footer-links {
    margin: 15px 0;
}

.footer-links a {
    color: var(--primary-color);
    text-decoration: none;
    margin: 0 15px;
    font-weight: 500;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: var(--secondary-color);
}

.footer-social {
    margin: 15px 0;
}

.footer-social a {
    display: inline-block;
    width: 36px;
    height: 36px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    margin: 0 8px;
    line-height: 36px;
    transition: all 0.3s;
}

.footer-social a:hover {
    background-color: var(--secondary-color);
    transform: translateY(-3px);
}

.copyright {
    font-size: 0.9rem;
    color: var(--text-color);
    margin-top: 15px;
}
</style>
<footer>
    <div class="footer-content">
        <div class="footer-logo">
            <img src="<?php echo $root_path; ?>images/logo.png" alt="The Obscured Index Logo" height="40">
        </div>
        <div class="footer-links">
            <a href="<?php echo $root_path; ?>IN/home.php">Home</a>
            <a href="<?php echo $root_path; ?>IN/library.php">Library</a>
            <a href="<?php echo $root_path; ?>IN/add_manhwa.php">Add New</a>
            <a href="<?php echo $root_path; ?>IN/profile.php">Profile</a>
        </div>
        <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
        </div>
        <p class="copyright">&copy; <?php echo date('Y'); ?> - The Obscured Index. All rights reserved.</p>
    </div>
</footer>