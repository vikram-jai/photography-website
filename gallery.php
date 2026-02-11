<?php 
@include 'db.php'; 

// Create portfolio table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
@mysqli_query($conn, $create_table);

// Get filter category
$filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build query
$where_conditions = [];
if($filter != 'all') {
    $filter_escaped = mysqli_real_escape_string($conn, $filter);
    $where_conditions[] = "category = '$filter_escaped'";
}
if(!empty($search)) {
    $where_conditions[] = "(title LIKE '%$search%' OR description LIKE '%$search%')";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Sorting
$order_clause = "ORDER BY ";
switch($sort) {
    case 'oldest':
        $order_clause .= "created_at ASC";
        break;
    case 'title':
        $order_clause .= "title ASC";
        break;
    case 'featured':
        $order_clause .= "featured DESC, created_at DESC";
        break;
    default:
        $order_clause .= "created_at DESC";
}

$query = "SELECT * FROM portfolio $where_clause $order_clause";
$portfolio_items = @mysqli_query($conn, $query);

// Get all categories for filter buttons
$categories_query = @mysqli_query($conn, "SELECT DISTINCT category FROM portfolio ORDER BY category");
$categories = [];
if($categories_query) {
    while($cat = mysqli_fetch_assoc($categories_query)) {
        $categories[] = $cat['category'];
    }
}

// Count items
$total_count = $portfolio_items ? mysqli_num_rows($portfolio_items) : 0;
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Gallery - RK Studio | Professional Photography</title>
    <meta name="description" content="Browse our stunning gallery of wedding, portrait, baby, and event photography.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo10.png">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/fontawesome-pro.css">
    <link rel="stylesheet" href="assets/css/vendor/remixicon.css">
    <link rel="stylesheet" href="assets/css/vendor/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/main.css">
    
    <style>
        :root {
            --gold: #d4a84b;
            --gold-dark: #b8923f;
            --gold-light: #e6c477;
            --dark-bg: #1a1a1a;
            --darker-bg: #0f0f0f;
            --card-bg: #252525;
            --text-light: #e0e0e0;
            --text-muted: #9a9a9a;
            --border-color: #333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--darker-bg);
            color: var(--text-light);
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
        }
        
        /* Header Styles */
        .gallery-header {
            background: linear-gradient(135deg, rgba(26,26,26,0.98), rgba(15,15,15,0.95));
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo img {
            height: 50px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        
        .nav-menu a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gold);
            transition: width 0.3s;
        }
        
        .nav-menu a:hover::after,
        .nav-menu a.active::after {
            width: 100%;
        }
        
        .nav-menu a:hover,
        .nav-menu a.active {
            color: var(--gold);
        }
        
        .header-btn {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #000;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .header-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 168, 75, 0.4);
            color: #000;
        }
        
        /* Hero Section */
        .gallery-hero {
            background: linear-gradient(135deg, rgba(26,26,26,0.9), rgba(15,15,15,0.95)), url('assets/images/bg/gallery-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 180px 0 80px;
            text-align: center;
        }
        
        .gallery-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .gallery-hero h1 span {
            color: var(--gold);
        }
        
        .gallery-hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        
        .breadcrumb-nav {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-size: 0.95rem;
        }
        
        .breadcrumb-nav a {
            color: var(--gold);
            text-decoration: none;
        }
        
        .breadcrumb-nav span {
            color: var(--text-muted);
        }
        
        /* Gallery Controls */
        .gallery-controls {
            background: var(--dark-bg);
            padding: 30px 0;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 80px;
            z-index: 100;
        }
        
        .controls-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .controls-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        
        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            background: var(--card-bg);
            color: var(--text-light);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--gold);
            color: #000;
            border-color: var(--gold);
        }
        
        .search-sort-row {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 10px 40px 10px 15px;
            border-radius: 25px;
            font-size: 0.9rem;
            width: 250px;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--gold);
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gold);
            border: none;
            color: #000;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sort-select {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 10px 35px 10px 15px;
            border-radius: 25px;
            font-size: 0.9rem;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23d4a84b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }
        
        .sort-select:focus {
            outline: none;
            border-color: var(--gold);
        }
        
        .results-count {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .results-count span {
            color: var(--gold);
            font-weight: 600;
        }
        
        /* Gallery Grid */
        .gallery-section {
            padding: 50px 0 80px;
        }
        
        .gallery-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            background: var(--card-bg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        
        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        
        .gallery-item.featured {
            grid-column: span 2;
            grid-row: span 2;
        }
        
        @media (max-width: 768px) {
            .gallery-item.featured {
                grid-column: span 1;
                grid-row: span 1;
            }
        }
        
        .gallery-item-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .gallery-item.featured .gallery-item-image {
            height: 100%;
            min-height: 590px;
        }
        
        .gallery-item:hover .gallery-item-image {
            transform: scale(1.1);
        }
        
        .gallery-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 25px;
        }
        
        .gallery-item:hover .gallery-item-overlay {
            opacity: 1;
        }
        
        .gallery-item-content {
            transform: translateY(20px);
            transition: transform 0.4s;
        }
        
        .gallery-item:hover .gallery-item-content {
            transform: translateY(0);
        }
        
        .gallery-item-category {
            display: inline-block;
            padding: 5px 15px;
            background: var(--gold);
            color: #000;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 20px;
            margin-bottom: 10px;
        }
        
        .gallery-item-title {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .gallery-item-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .gallery-item-actions {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.4s;
        }
        
        .gallery-item:hover .gallery-item-actions {
            opacity: 1;
            transform: translateY(0);
        }
        
        .action-btn {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background: var(--gold);
            color: #000;
            border-color: var(--gold);
            transform: scale(1.1);
        }
        
        /* Gallery Item Link for Lightbox */
        .gallery-item-link {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .gallery-zoom-icon {
            width: 60px;
            height: 60px;
            background: rgba(212, 168, 75, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.5rem;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover .gallery-zoom-icon {
            opacity: 1;
            transform: scale(1);
        }
        
        .gallery-zoom-icon:hover {
            background: var(--gold);
            transform: scale(1.1);
        }
        
        /* Video Section */
        .video-section {
            padding: 80px 0;
            background: var(--dark-bg);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 15px;
        }
        
        .section-header h2 span {
            color: var(--gold);
        }
        
        .section-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .video-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            background: var(--card-bg);
            transition: all 0.4s;
        }
        
        .video-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        
        .video-thumbnail {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
        }
        
        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .video-item:hover .video-thumbnail img {
            transform: scale(1.1);
        }
        
        .video-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70px;
            height: 70px;
            background: rgba(212, 168, 75, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.8rem;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
        }
        
        .video-play-btn:hover {
            background: var(--gold);
            transform: translate(-50%, -50%) scale(1.1);
            color: #000;
        }
        
        .video-play-btn i {
            margin-left: 5px;
        }
        
        .video-info {
            padding: 20px;
        }
        
        .video-category {
            display: inline-block;
            padding: 4px 12px;
            background: var(--gold);
            color: #000;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 15px;
            margin-bottom: 10px;
        }
        
        .video-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .video-duration {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .empty-video-state {
            text-align: center;
            padding: 60px 20px;
            grid-column: 1 / -1;
        }
        
        .empty-video-state i {
            font-size: 4rem;
            color: var(--gold);
            margin-bottom: 20px;
        }
        
        .empty-video-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .empty-video-state p {
            color: var(--text-muted);
        }
        
        .featured-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--gold);
            color: #000;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 10;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            grid-column: 1 / -1;
        }
        
        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 3rem;
            color: var(--gold);
        }
        
        .empty-state h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 25px;
        }
        
        .empty-state .btn-primary {
            background: var(--gold);
            color: #000;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .empty-state .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(212, 168, 75, 0.4);
        }
        
        /* Lightbox Enhancement */
        .mfp-bg {
            background: rgba(0,0,0,0.95);
        }
        
        .mfp-image-holder .mfp-close,
        .mfp-iframe-holder .mfp-close {
            color: var(--gold);
            font-size: 35px;
        }
        
        .mfp-arrow {
            color: var(--gold);
        }
        
        .mfp-title {
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        
        /* Footer */
        .gallery-footer {
            background: var(--dark-bg);
            padding: 50px 0 30px;
            border-top: 1px solid var(--border-color);
        }
        
        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .footer-logo img {
            height: 60px;
            margin-bottom: 20px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            list-style: none;
            margin-bottom: 25px;
        }
        
        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--gold);
        }
        
        .footer-social {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .footer-social a {
            width: 45px;
            height: 45px;
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-social a:hover {
            background: var(--gold);
            color: #000;
            transform: translateY(-3px);
        }
        
        .footer-copyright {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .footer-copyright a {
            color: var(--gold);
            text-decoration: none;
        }
        
        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gold);
            color: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 999;
            text-decoration: none;
        }
        
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212, 168, 75, 0.4);
            color: #000;
        }
        
        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 991px) {
            .mobile-menu-btn {
                display: block;
            }
            
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--dark-bg);
                flex-direction: column;
                padding: 20px;
                gap: 15px;
                border-bottom: 1px solid var(--border-color);
            }
            
            .nav-menu.active {
                display: flex;
            }
            
            .header-btn {
                display: none;
            }
            
            .gallery-hero h1 {
                font-size: 2.5rem;
            }
            
            .controls-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .filter-buttons {
                justify-content: center;
            }
            
            .search-sort-row {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .search-box input {
                width: 200px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .gallery-item {
            animation: fadeInUp 0.6s ease forwards;
        }
        
        .gallery-item:nth-child(2) { animation-delay: 0.1s; }
        .gallery-item:nth-child(3) { animation-delay: 0.2s; }
        .gallery-item:nth-child(4) { animation-delay: 0.3s; }
        .gallery-item:nth-child(5) { animation-delay: 0.4s; }
        .gallery-item:nth-child(6) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="gallery-header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <img src="assets/images/logo10.png" alt="RK Studio">
            </a>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="ri-menu-line"></i>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="service.html">Services</a></li>
                <li><a href="Gallery.php" class="active">Gallery</a></li>
                <li><a href="gifts.html">Gifts</a></li>
                <li><a href="frames.html">Frames</a></li>
                <li><a href="offers.html">Offers</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
            
            <a href="order.html" class="header-btn">Schedule A Shoot</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gallery-hero">
        <h1>Our <span>Gallery</span></h1>
        <p>Explore our collection of stunning photographs capturing life's most precious moments.</p>
        <div class="breadcrumb-nav">
            <a href="index.php">Home</a>
            <span><i class="ri-arrow-right-s-line"></i></span>
            <span>Gallery</span>
        </div>
    </section>

    <!-- Gallery Controls -->
    <div class="gallery-controls">
        <div class="controls-container">
            <div class="controls-row">
                <div class="filter-buttons">
                    <a href="Gallery.php" class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">
                        <i class="ri-apps-line"></i> All
                    </a>
                    <a href="Gallery.php?category=wedding" class="filter-btn <?php echo $filter == 'wedding' ? 'active' : ''; ?>">
                        <i class="ri-heart-line"></i> Wedding
                    </a>
                    <a href="Gallery.php?category=portrait" class="filter-btn <?php echo $filter == 'portrait' ? 'active' : ''; ?>">
                        <i class="ri-user-smile-line"></i> Portrait
                    </a>
                    <a href="Gallery.php?category=event" class="filter-btn <?php echo $filter == 'event' ? 'active' : ''; ?>">
                        <i class="ri-calendar-event-line"></i> Event
                    </a>
                    <a href="Gallery.php?category=baby" class="filter-btn <?php echo $filter == 'baby' ? 'active' : ''; ?>">
                        <i class="ri-emotion-happy-line"></i> Baby
                    </a>
                    <a href="Gallery.php?category=drone" class="filter-btn <?php echo $filter == 'drone' ? 'active' : ''; ?>">
                        <i class="ri-plane-line"></i> Drone
                    </a>
                    <a href="#video-section" class="filter-btn">
                        <i class="ri-video-line"></i> Videos
                    </a>
                </div>
                
                <div class="search-sort-row">
                    <div class="results-count">
                        Showing <span><?php echo $total_count; ?></span> photos
                    </div>
                    
                    <form class="search-box" method="get" action="Gallery.php">
                        <?php if($filter != 'all'): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($filter); ?>">
                        <?php endif; ?>
                        <input type="text" name="search" placeholder="Search photos..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="ri-search-line"></i></button>
                    </form>
                    
                    <select class="sort-select" onchange="window.location.href=this.value">
                        <option value="Gallery.php?sort=newest<?php echo $filter != 'all' ? '&category='.$filter : ''; ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="Gallery.php?sort=oldest<?php echo $filter != 'all' ? '&category='.$filter : ''; ?>" <?php echo $sort == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="Gallery.php?sort=featured<?php echo $filter != 'all' ? '&category='.$filter : ''; ?>" <?php echo $sort == 'featured' ? 'selected' : ''; ?>>Featured First</option>
                        <option value="Gallery.php?sort=title<?php echo $filter != 'all' ? '&category='.$filter : ''; ?>" <?php echo $sort == 'title' ? 'selected' : ''; ?>>Title A-Z</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <section class="gallery-section">
        <div class="gallery-container">
            <div class="gallery-grid">
                <?php 
                if($portfolio_items && mysqli_num_rows($portfolio_items) > 0){
                    $index = 0;
                    while($item = mysqli_fetch_assoc($portfolio_items)){
                        $index++;
                        $image_path = 'uploaded_img/' . $item['image'];
                        $is_featured = $item['featured'] == 1 && $index <= 1;
                ?>
                <div class="gallery-item <?php echo $is_featured ? 'featured' : ''; ?>">
                    <?php if($item['featured']): ?>
                        <div class="featured-badge">
                            <i class="ri-star-fill"></i> Featured
                        </div>
                    <?php endif; ?>
                    
                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                         class="gallery-item-image"
                         onerror="this.src='assets/images/portfolio/placeholder.jpg'">
                    
                    <a href="<?php echo htmlspecialchars($image_path); ?>" class="gallery-item-link popup-image" title="Click to view full image">
                        <div class="gallery-zoom-icon">
                            <i class="ri-zoom-in-line"></i>
                        </div>
                    </a>
                    
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-content">
                            <span class="gallery-item-category"><?php echo htmlspecialchars(ucfirst($item['category'])); ?></span>
                            <h3 class="gallery-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <?php if(!empty($item['description'])): ?>
                                <p class="gallery-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else { 
                ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ri-image-line"></i>
                    </div>
                    <h3>No Photos Found</h3>
                    <?php if(!empty($search)): ?>
                        <p>No photos match your search "<?php echo htmlspecialchars($search); ?>". Try a different keyword.</p>
                        <a href="Gallery.php" class="btn-primary">
                            <i class="ri-refresh-line"></i> Clear Search
                        </a>
                    <?php elseif($filter != 'all'): ?>
                        <p>No photos in the "<?php echo htmlspecialchars(ucfirst($filter)); ?>" category yet.</p>
                        <a href="Gallery.php" class="btn-primary">
                            <i class="ri-apps-line"></i> View All Photos
                        </a>
                    <?php else: ?>
                        <p>Gallery photos will appear here once added by the admin.</p>
                        <a href="index.php" class="btn-primary">
                            <i class="ri-home-line"></i> Back to Home
                        </a>
                    <?php endif; ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="video-section" id="video-section">
        <div class="gallery-container">
            <div class="section-header">
                <h2><i class="ri-video-line"></i> Video <span>Collection</span></h2>
                <p>Watch our cinematic highlights capturing your special moments in motion.</p>
            </div>
            <div class="video-grid">
                <?php 
                // Create videos table if not exists
                $create_videos_table = "CREATE TABLE IF NOT EXISTS videos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    category VARCHAR(100) NOT NULL,
                    video_url VARCHAR(500) NOT NULL,
                    thumbnail VARCHAR(255),
                    duration VARCHAR(20),
                    featured TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                @mysqli_query($conn, $create_videos_table);
                
                // Fetch videos
                $videos_query = @mysqli_query($conn, "SELECT * FROM videos ORDER BY featured DESC, created_at DESC");
                
                if($videos_query && mysqli_num_rows($videos_query) > 0){
                    while($video = mysqli_fetch_assoc($videos_query)){
                        $thumbnail_path = !empty($video['thumbnail']) ? 'uploaded_img/' . $video['thumbnail'] : 'assets/images/portfolio/video-placeholder.jpg';
                ?>
                <div class="video-item">
                    <div class="video-thumbnail">
                        <img src="<?php echo htmlspecialchars($thumbnail_path); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" onerror="this.src='assets/images/portfolio/placeholder.jpg'">
                        <a href="<?php echo htmlspecialchars($video['video_url']); ?>" class="video-play-btn popup-video">
                            <i class="ri-play-fill"></i>
                        </a>
                    </div>
                    <div class="video-info">
                        <span class="video-category"><?php echo htmlspecialchars(ucfirst($video['category'])); ?></span>
                        <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                        <?php if(!empty($video['duration'])): ?>
                            <p class="video-duration"><i class="ri-time-line"></i> <?php echo htmlspecialchars($video['duration']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
                    }
                } else { 
                ?>
                <div class="empty-video-state">
                    <i class="ri-video-line"></i>
                    <h3>No Videos Yet</h3>
                    <p>Video collection coming soon! Check back later for cinematic highlights.</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="gallery-footer">
        <div class="footer-container">
            <div class="footer-logo">
                <a href="index.php"><img src="assets/images/logo10.png" alt="RK Studio"></a>
            </div>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="service.html">Services</a></li>
                <li><a href="Gallery.php">Gallery</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
            <div class="footer-social">
                <a href="#"><i class="ri-facebook-fill"></i></a>
                <a href="#"><i class="ri-instagram-line"></i></a>
                <a href="#"><i class="ri-youtube-line"></i></a>
                <a href="#"><i class="ri-whatsapp-line"></i></a>
            </div>
            <p class="footer-copyright">
                &copy; 2026 <a href="index.php">RK Studio</a>. All Rights Reserved.
            </p>
        </div>
    </footer>

    <!-- Back to Top -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="ri-arrow-up-line"></i>
    </a>

    <!-- JS Libraries -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
    <script src="assets/js/vendor/magnific-popup.min.js"></script>
    
    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            document.getElementById('navMenu').classList.toggle('active');
        }
        
        // Back to Top Button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        
        // Image Lightbox
        $(document).ready(function() {
            $('.popup-image').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    preload: [0, 1]
                },
                image: {
                    titleSrc: function(item) {
                        var parent = item.el.closest('.gallery-item');
                        if(parent.length) {
                            var title = parent.find('.gallery-item-title').text();
                            var category = parent.find('.gallery-item-category').text();
                            return title + ' <small>' + category + '</small>';
                        }
                        return '';
                    }
                },
                mainClass: 'mfp-fade',
                removalDelay: 300,
                callbacks: {
                    open: function() {
                        $('body').css('overflow', 'hidden');
                    },
                    close: function() {
                        $('body').css('overflow', '');
                    }
                }
            });
            
            // Video Lightbox
            $('.popup-video').magnificPopup({
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 300,
                preloader: true,
                iframe: {
                    patterns: {
                        youtube: {
                            index: 'youtube.com/',
                            id: 'v=',
                            src: 'https://www.youtube.com/embed/%id%?autoplay=1'
                        },
                        youtu_be: {
                            index: 'youtu.be/',
                            id: '/',
                            src: 'https://www.youtube.com/embed/%id%?autoplay=1'
                        },
                        vimeo: {
                            index: 'vimeo.com/',
                            id: '/',
                            src: 'https://player.vimeo.com/video/%id%?autoplay=1'
                        }
                    }
                },
                callbacks: {
                    open: function() {
                        $('body').css('overflow', 'hidden');
                    },
                    close: function() {
                        $('body').css('overflow', '');
                    }
                }
            });
        });
        
        // Smooth scroll for back to top
        document.getElementById('backToTop').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Smooth scroll for video section link
        document.querySelectorAll('a[href="#video-section"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('video-section').scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
