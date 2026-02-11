<?php

@include 'db.php';

session_start();

if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
   exit();
}

// Get photo count
$photo_result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM photo");
$photo_count = $photo_result ? (mysqli_fetch_assoc($photo_result)['count'] ?? 0) : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - RK Studio</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Theme -->
    <link rel="stylesheet" href="admin-theme.css">
</head>
<body>

<!-- User Header -->
<header class="admin-header">
    <div class="container">
        <a href="index.html" class="admin-logo">
            <img src="assets/images/logo10.png" alt="RK Studio" onerror="this.style.display='none'">
            <span class="admin-logo-text">RK <span>Studio</span></span>
        </a>
        <nav class="admin-nav">
            <a href="user_page.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="order.html"><i class="fas fa-camera"></i> Book Shoot</a>
            <a href="index.html"><i class="fas fa-globe"></i> Website</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
</header>

<!-- Main Content -->
<div class="admin-container">
    
    <!-- Welcome Section -->
    <section class="welcome-section">
        <p class="welcome-subtitle">Welcome back to</p>
        <h1 class="welcome-title">RK <span>Studio</span></h1>
        <span class="welcome-role"><i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <p class="welcome-description">Explore our photography portfolio, book your session, and capture your precious moments with us.</p>
    </section>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $photo_count; ?></div>
            <div class="stat-label">Photos in Gallery</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">15+</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">500+</div>
            <div class="stat-label">Happy Clients</div>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <a href="products.php" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-images"></i>
            </div>
            <h3 class="dashboard-card-title">View Gallery</h3>
            <p class="dashboard-card-description">Browse our stunning photography portfolio</p>
        </a>
        
        <a href="order.html" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3 class="dashboard-card-title">Schedule a Shoot</h3>
            <p class="dashboard-card-description">Book your photography session with us</p>
        </a>
        
        <a href="service.html" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-camera-retro"></i>
            </div>
            <h3 class="dashboard-card-title">Our Services</h3>
            <p class="dashboard-card-description">Explore our photography services</p>
        </a>
        
        <a href="gifts.html" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-gift"></i>
            </div>
            <h3 class="dashboard-card-title">Gifts</h3>
            <p class="dashboard-card-description">Special photography gift packages</p>
        </a>
        
        <a href="frames.html" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-image"></i>
            </div>
            <h3 class="dashboard-card-title">Frames</h3>
            <p class="dashboard-card-description">Premium photo frames collection</p>
        </a>
        
        <a href="index.html" class="dashboard-card">
            <div class="dashboard-card-icon">
                <i class="fas fa-globe"></i>
            </div>
            <h3 class="dashboard-card-title">Visit Website</h3>
            <p class="dashboard-card-description">Go to RK Studio homepage</p>
        </a>
    </div>
    
</div>

</body>
</html>