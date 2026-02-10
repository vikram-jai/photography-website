<?php 
@include 'db.php'; 
session_start();

// Determine if admin or user
$is_admin = isset($_SESSION['admin_name']);
$is_user = isset($_SESSION['user_name']);
$user_display_name = $is_admin ? $_SESSION['admin_name'] : ($is_user ? $_SESSION['user_name'] : 'Guest');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - RK Studio</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Theme -->
    <link rel="stylesheet" href="admin-theme.css">
</head>
<body>

<!-- Header -->
<header class="admin-header">
    <div class="container">
        <a href="<?php echo $is_admin ? 'admin_page.php' : ($is_user ? 'user_page.php' : 'index.html'); ?>" class="admin-logo">
            <img src="assets/images/logo10.png" alt="RK Studio" onerror="this.style.display='none'">
            <span class="admin-logo-text">RK <span>Studio</span></span>
        </a>
        <nav class="admin-nav">
            <?php if($is_admin): ?>
                <!-- Admin Navigation -->
                <a href="admin_page.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="admin.php"><i class="fas fa-plus-circle"></i> Add Photos</a>
                <a href="products.php" class="active"><i class="fas fa-images"></i> View Photos</a>
                <a href="ad.php"><i class="fas fa-calendar-check"></i> Bookings</a>
                <a href="index.html"><i class="fas fa-globe"></i> Website</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php elseif($is_user): ?>
                <!-- User Navigation -->
                <a href="user_page.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php" class="active"><i class="fas fa-images"></i> Gallery</a>
                <a href="order.html"><i class="fas fa-camera"></i> Book Shoot</a>
                <a href="index.html"><i class="fas fa-globe"></i> Website</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <!-- Guest Navigation -->
                <a href="index.html"><i class="fas fa-home"></i> Home</a>
                <a href="products.php" class="active"><i class="fas fa-images"></i> Gallery</a>
                <a href="order.html"><i class="fas fa-camera"></i> Book Shoot</a>
                <a href="login_form.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- Main Content -->
<div class="admin-container">
    
    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-images"></i> Photo <span>Gallery</span></h1>
        <p>Browse our stunning photography portfolio</p>
    </div>

    <!-- Stats -->
    <?php 
    $photo_result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM photo");
    $photo_count = $photo_result ? (mysqli_fetch_assoc($photo_result)['count'] ?? 0) : 0;
    ?>
    <div class="stats-grid" style="max-width: 400px; margin: 0 auto 2rem;">
        <div class="stat-card">
            <div class="stat-number"><?php echo $photo_count; ?></div>
            <div class="stat-label">Total Photos</div>
        </div>
    </div>

    <!-- Photo Grid -->
    <div class="photo-grid">
        <?php 
        $select_photo = @mysqli_query($conn, "SELECT * FROM photo ORDER BY id DESC");
        if($select_photo && mysqli_num_rows($select_photo) > 0){
            while($fetch_photo = mysqli_fetch_assoc($select_photo)){
        ?>
        <div class="photo-card">
            <img src="uploaded_img/<?php echo $fetch_photo['image']; ?>" class="photo-card-image" alt="<?php echo htmlspecialchars($fetch_photo['description']); ?>">
            <div class="photo-card-content">
                <h3 class="photo-card-title"><?php echo htmlspecialchars($fetch_photo['description']); ?></h3>
            </div>
        </div>
        <?php 
            }
        } else { 
        ?>
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-images"></i></div>
                <h3 class="empty-state-title">No Photos Yet</h3>
                <p>The gallery is empty. Check back soon for amazing photos!</p>
                <?php if($is_admin): ?>
                <a href="admin.php" class="btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Add Photos
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php } ?>
    </div>

</div>

</body>
</html>
