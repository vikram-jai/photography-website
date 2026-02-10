<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings & Messages - RK Studio Admin</title>
    
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
            <a href="admin_page.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="admin.php"><i class="fas fa-plus-circle"></i> Add Photos</a>
            <a href="products.php"><i class="fas fa-images"></i> View Photos</a>
            <a href="ad.php" class="active"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="index.html"><i class="fas fa-globe"></i> Website</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
</header>

<!-- Main Content -->
<div class="admin-container">

<?php
// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'studio';

// Connect to database
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Get count
$count_result = $conn->query("SELECT COUNT(*) as count FROM messages");
$message_count = $count_result->fetch_assoc()['count'] ?? 0;

// Retrieve messages from database
$sql = "SELECT * FROM messages ORDER BY id DESC";
$result = $conn->query($sql);
?>

    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-calendar-check"></i> Client <span>Bookings</span></h1>
        <p>View and manage booking requests from clients</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="max-width: 400px; margin: 0 auto 2rem;">
        <div class="stat-card">
            <div class="stat-number"><?php echo $message_count; ?></div>
            <div class="stat-label">Total Inquiries</div>
        </div>
    </div>

    <!-- Booking Cards Grid -->
    <div class="booking-grid">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $initials = strtoupper(substr($row['name'], 0, 1));
        ?>
        <div class="booking-card">
            <div class="booking-card-header">
                <div class="booking-avatar"><?php echo $initials; ?></div>
                <h3 class="booking-name"><?php echo htmlspecialchars($row['name']); ?></h3>
            </div>
            <div class="booking-info">
                <div class="booking-info-item">
                    <i class="fas fa-envelope"></i>
                    <span><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></span>
                </div>
                <div class="booking-info-item">
                    <i class="fas fa-phone"></i>
                    <span><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone_number']); ?></span>
                </div>
            </div>
            <div class="booking-message">
                <div class="booking-message-label"><i class="fas fa-comment"></i> Message</div>
                <p class="booking-message-text"><?php echo htmlspecialchars($row['message']); ?></p>
            </div>
        </div>
        <?php
            }
        } else {
        ?>
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                <h3 class="empty-state-title">No Bookings Yet</h3>
                <p>When clients submit booking requests, they'll appear here.</p>
            </div>
        </div>
        <?php } ?>
    </div>

</div>

</body>
</html>