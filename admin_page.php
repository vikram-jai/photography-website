<?php

@include 'db.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:login_form.php');
   exit();
}

// Create portfolio table if not exists
$create_portfolio = "CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
@mysqli_query($conn, $create_portfolio);

// Get photo count from gallery database
$photo_result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM photo");
$photo_count = $photo_result ? (mysqli_fetch_assoc($photo_result)['count'] ?? 0) : 0;

// Get portfolio count
$portfolio_result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM portfolio");
$portfolio_count = $portfolio_result ? (mysqli_fetch_assoc($portfolio_result)['count'] ?? 0) : 0;

// Get message count from studio database
$studio_conn = new mysqli('localhost', 'root', '', 'studio');
$message_count = 0;
if (!$studio_conn->connect_error) {
    $msg_result = @$studio_conn->query("SELECT COUNT(*) as count FROM messages");
    if ($msg_result) {
        $message_count = $msg_result->fetch_assoc()['count'] ?? 0;
    }
    $studio_conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RK Studio</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Theme -->
    <link rel="stylesheet" href="admin-theme.css">
</head>
<body>

<!-- Admin Header -->
<header class="admin-header">
    <div class="container">
        <a href="admin_page.php" class="admin-logo">
            <img src="assets/images/logo10.png" alt="RK Studio" onerror="this.style.display='none'">
            <span class="admin-logo-text">RK <span>Studio</span></span>
        </a>
        <nav class="admin-nav">
            <a href="admin_page.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="portfolio.php"><i class="fas fa-briefcase"></i> Portfolio</a>
            <a href="admin.php"><i class="fas fa-plus-circle"></i> Add Photos</a>
            <a href="products.php"><i class="fas fa-images"></i> View Photos</a>
            <a href="ad.php"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="index.php"><i class="fas fa-globe"></i> Website</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
</header>

<!-- Main Content -->
<div class="admin-container">
    
    <!-- Welcome Section -->
    <section class="welcome-section">
        <p class="welcome-subtitle">Welcome back to</p>
        <h1 class="welcome-title">RK <span>Studio</span> Admin</h1>
        <span class="welcome-role"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        <p class="welcome-description">Manage your photography portfolio, view bookings, and keep your studio running smoothly.</p>
    </section>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $photo_count; ?></div>
            <div class="stat-label">Total Photos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $portfolio_count; ?></div>
            <div class="stat-label">Portfolio Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $message_count; ?></div>
            <div class="stat-label">Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">15+</div>
            <div class="stat-label">Years Experience</div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <a href="portfolio.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <h3 class="dashboard-card-title">Portfolio</h3>
            <p class="dashboard-card-description">Manage your portfolio showcase with categories</p>
        </a>
        
        <a href="admin.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-camera"></i>
            </div>
            <h3 class="dashboard-card-title">Add Photos</h3>
            <p class="dashboard-card-description">Upload new photos to your gallery with descriptions</p>
        </a>
        
        <a href="products.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-images"></i>
            </div>
            <h3 class="dashboard-card-title">View Gallery</h3>
            <p class="dashboard-card-description">Browse and manage your photo collection</p>
        </a>
        
        <a href="ad.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="dashboard-card-title">View Bookings</h3>
            <p class="dashboard-card-description">Check client messages and booking requests</p>
        </a>
        
        <a href="index.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-globe"></i>
            </div>
            <h3 class="dashboard-card-title">Visit Website</h3>
            <p class="dashboard-card-description">Preview your live photography website</p>
        </a>
        
        <a href="login_form.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-user"></i>
            </div>
            <h3 class="dashboard-card-title">Login Page</h3>
            <p class="dashboard-card-description">Access the admin login page</p>
        </a>
        
        <a href="logout.php" class="dashboard-card" style="border-color: #dc3545;">
            <div class="dashboard-card-icon" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="dashboard-card-title">Logout</h3>
            <p class="dashboard-card-description">Securely sign out of the admin panel</p>
        </a>
    </div>
    
</div>

</body>
</html>