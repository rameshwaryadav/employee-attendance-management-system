<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EAMS - Professional Attendance Management</title>
    <link rel="stylesheet" href="assets/css/new_landing_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body>

    <div class="container">
        <!-- === HEADER KA HTML YAHAN THEEK KIYA GAYA HAI === -->
        <header class="main-header">
            <a href="index.php" class="logo">
              <img src="assets/images/logo.png" alt="">
            </a>
            
            <div class="header-right-side">
                <nav class="main-nav">
                    <ul>
                        <li><a href="admin/login.php">Admin Login</a></li>
                        <li><a href="employee/login.php">Employee Login</a></li>
                    </ul>
                </nav>
                <a href="#" class="btn btn-primary">Register</a>
            </div>

        </header>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-text">
                <h1>Effortless Attendance Tracking for Modern Teams</h1>
                <p>Track attendance, manage leaves, and generate reports all in one place. Save time, reduce errors, and empower your workforce.</p>
                <a href="admin/login.php" class="btn btn-primary-solid">Get Started</a>
            </div>
            <div class="hero-image">
                <img src="assets/images/home.png" alt="Dashboard Illustration">
            </div>
        </section>
    </div>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Our System?</h2>
                <p>A complete solution designed for efficiency and accuracy.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon-bg"><i class="fas fa-clock"></i></div>
                    <h3>Real-Time Tracking</h3>
                    <p>Monitor employee check-in and check-out times instantly from the admin dashboard.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg"><i class="fas fa-calendar-alt"></i></div>
                    <h3>Leave Management</h3>
                    <p>Employees can apply for leaves, and admins can approve or reject them with a single click.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg"><i class="fas fa-chart-pie"></i></div>
                    <h3>Detailed Reports</h3>
                    <p>Generate insightful monthly or daily attendance reports for individuals or entire departments.</p>
                </div>
            </div>
        </div>
    </section>

<!-- === YAHAN SE NAYA FOOTER CODE PASTE KAREIN === -->
<footer class="main-footer">
    <div class="container footer-content">
        <div class="footer-about">
            <a href="index.php" class="footer-logo"></a>
        </div>
        
        <div class="footer-connect">
            <h3>Connect with the Developers</h3>
            <div class="social-links">
                <a href="https://github.com/rameshwaryadav" target="_blank" aria-label="GitHub">
                    <i class="fab fa-github"></i>
                </a>
                <a href="https://linkedin.com/in/rameshwar01" target="_blank" aria-label="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© <?php echo date('Y'); ?> | Originally developed in 2023. Redesigned by Rameshwar & Ralsan Banjare.</p>
    </div>
</footer>
</body></html>