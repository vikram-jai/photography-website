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

// Create banner_slides table if not exists
$create_banner_table = "CREATE TABLE IF NOT EXISTS banner_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description TEXT,
    image VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
@mysqli_query($conn, $create_banner_table);

// Create videos table if not exists
$create_videos_table = "CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    video_url VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(255),
    duration VARCHAR(20),
    description TEXT,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
@mysqli_query($conn, $create_videos_table);

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

// Get banner slides
$banner_slides = @mysqli_query($conn, "SELECT * FROM banner_slides WHERE active = 1 ORDER BY display_order ASC, id DESC");

// Get videos
$videos_query = @mysqli_query($conn, "SELECT * FROM videos ORDER BY featured DESC, created_at DESC");

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
    <link rel="stylesheet" href="assets/css/plugins/swiper.min.css">
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

        /* ===== HERO BANNER SLIDER ===== */
        .gallery-hero-slider {
            padding-top: 80px;
            background: var(--darker-bg);
            position: relative;
            overflow: hidden;
        }

        .hero-slider-wrapper {
            position: relative;
        }

        .hero-swiper {
            width: 100%;
            height: 70vh;
            min-height: 500px;
        }

        .hero-slide {
            position: relative;
            overflow: hidden;
        }

        .hero-slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 6s ease;
        }

        .hero-swiper .swiper-slide-active .hero-slide-image {
            transform: scale(1.1);
        }

        .hero-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 60px;
        }

        .hero-slide-content {
            max-width: 600px;
        }

        .hero-slide-subtitle {
            color: var(--gold);
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 15px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease 0.3s;
        }

        .hero-slide-title {
            color: #fff;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 15px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease 0.5s;
        }

        .hero-slide-desc {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease 0.7s;
        }

        .hero-swiper .swiper-slide-active .hero-slide-subtitle,
        .hero-swiper .swiper-slide-active .hero-slide-title,
        .hero-swiper .swiper-slide-active .hero-slide-desc {
            opacity: 1;
            transform: translateY(0);
        }

        .hero-slider-nav {
            position: absolute;
            bottom: 30px;
            right: 60px;
            display: flex;
            gap: 15px;
            z-index: 10;
        }

        .hero-nav-btn {
            width: 50px;
            height: 50px;
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .hero-nav-btn:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: #000;
        }

        .hero-pagination {
            position: absolute;
            bottom: 30px;
            left: 60px;
            z-index: 10;
        }

        .hero-pagination .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: rgba(255,255,255,0.3);
            opacity: 1;
            margin: 0 6px;
        }

        .hero-pagination .swiper-pagination-bullet-active {
            background: var(--gold);
            width: 30px;
            border-radius: 6px;
        }

        /* Breadcrumb */
        .gallery-breadcrumb {
            background: var(--dark-bg);
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .breadcrumb-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .breadcrumb-nav a {
            color: var(--gold);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb-nav a:hover {
            color: var(--gold-light);
        }

        .breadcrumb-nav span {
            color: var(--text-muted);
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.8rem;
            color: #fff;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .section-title h2 span {
            color: var(--gold);
        }

        .section-title p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ===== PHOTOS SECTION ===== */
        .photos-section {
            padding: 80px 0;
            background: var(--darker-bg);
        }

        .section-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Filter Controls */
        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .filter-btn {
            padding: 12px 25px;
            background: var(--card-bg);
            color: var(--text-light);
            border: 1px solid var(--border-color);
            border-radius: 30px;
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

        .filter-btn i {
            margin-right: 6px;
        }

        /* Masonry Gallery Grid */
        .masonry-gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .gallery-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        /* Masonry sizing */
        .gallery-item.tall {
            grid-row: span 2;
        }

        .gallery-item.wide {
            grid-column: span 2;
        }

        .gallery-item-image {
            width: 100%;
            height: 100%;
            min-height: 280px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item.tall .gallery-item-image {
            min-height: 580px;
        }

        .gallery-item:hover .gallery-item-image {
            transform: scale(1.08);
        }

        .gallery-item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
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
            transition: transform 0.4s ease;
        }

        .gallery-item:hover .gallery-item-content {
            transform: translateY(0);
        }

        .gallery-item-category {
            display: inline-block;
            padding: 5px 15px;
            background: var(--gold);
            color: #000;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .gallery-item-title {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .gallery-item-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .gallery-zoom {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
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
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .gallery-item:hover .gallery-zoom {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .gallery-zoom:hover {
            background: var(--gold);
            transform: translate(-50%, -50%) scale(1.1);
            color: #000;
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
            color: #fff;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        /* ===== VIDEOS SECTION ===== */
        .videos-section {
            padding: 80px 0;
            background: var(--dark-bg);
        }

        .videos-slider-wrapper {
            position: relative;
            padding: 0 50px;
        }

        .videos-swiper {
            overflow: visible;
        }

        .video-slide {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .video-slide:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
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
            transition: transform 0.5s ease;
        }

        .video-slide:hover .video-thumbnail img {
            transform: scale(1.08);
        }

        .video-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70px;
            height: 70px;
            background: rgba(212, 168, 75, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.8rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .video-play-btn i {
            margin-left: 4px;
        }

        .video-play-btn:hover {
            background: var(--gold);
            transform: translate(-50%, -50%) scale(1.1);
            color: #000;
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
            letter-spacing: 0.5px;
            border-radius: 15px;
            margin-bottom: 10px;
        }

        .video-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .video-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .video-duration {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .videos-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 45px;
            height: 45px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
        }

        .videos-nav:hover {
            background: var(--gold-light);
            transform: translateY(-50%) scale(1.1);
        }

        .videos-prev {
            left: 0;
        }

        .videos-next {
            right: 0;
        }

        .videos-pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .videos-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(255,255,255,0.3);
            opacity: 1;
            margin: 0 5px;
        }

        .videos-pagination .swiper-pagination-bullet-active {
            background: var(--gold);
        }

        .empty-video-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 15px;
        }

        .empty-video-state i {
            font-size: 4rem;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .empty-video-state h3 {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 10px;
        }

        .empty-video-state p {
            color: var(--text-muted);
        }

        /* ===== FOOTER ===== */
        .gallery-footer {
            background: var(--darker-bg);
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
            padding: 0;
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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .masonry-gallery {
                grid-template-columns: repeat(2, 1fr);
            }
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

            .hero-swiper {
                height: 50vh;
                min-height: 400px;
            }

            .hero-slide-title {
                font-size: 2.5rem;
            }

            .hero-slide-overlay {
                padding: 40px;
            }

            .hero-slider-nav {
                right: 40px;
            }

            .hero-pagination {
                left: 40px;
            }

            .section-title h2 {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 768px) {
            .masonry-gallery {
                grid-template-columns: 1fr;
            }

            .gallery-item.tall,
            .gallery-item.wide {
                grid-row: span 1;
                grid-column: span 1;
            }

            .gallery-item.tall .gallery-item-image {
                min-height: 280px;
            }

            .hero-swiper {
                height: 60vh;
            }

            .hero-slide-title {
                font-size: 2rem;
            }

            .hero-slide-overlay {
                padding: 30px;
            }

            .hero-slider-nav {
                right: 30px;
                bottom: 20px;
            }

            .hero-pagination {
                left: 30px;
                bottom: 20px;
            }

            .hero-nav-btn {
                width: 40px;
                height: 40px;
            }

            .filter-controls {
                gap: 8px;
            }

            .filter-btn {
                padding: 10px 18px;
                font-size: 0.85rem;
            }

            .videos-slider-wrapper {
                padding: 0 20px;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .hero-slide-subtitle {
                font-size: 0.85rem;
            }

            .hero-slide-title {
                font-size: 1.6rem;
            }

            .hero-slide-desc {
                font-size: 0.95rem;
            }

            .filter-btn {
                padding: 8px 14px;
                font-size: 0.8rem;
            }

            .filter-btn i {
                display: none;
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

    <!-- Hero Banner Slider -->
    <section class="gallery-hero-slider">
        <div class="hero-slider-wrapper">
            <?php if($banner_slides && mysqli_num_rows($banner_slides) > 0): ?>
            <div class="swiper hero-swiper">
                <div class="swiper-wrapper">
                    <?php while($slide = mysqli_fetch_assoc($banner_slides)): ?>
                    <div class="swiper-slide hero-slide">
                        <img src="uploaded_img/<?php echo htmlspecialchars($slide['image']); ?>" 
                             alt="<?php echo htmlspecialchars($slide['title']); ?>" 
                             class="hero-slide-image"
                             onerror="this.src='assets/images/banner/banner-default.jpg'">
                        <div class="hero-slide-overlay">
                            <div class="hero-slide-content">
                                <?php if(!empty($slide['subtitle'])): ?>
                                <span class="hero-slide-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></span>
                                <?php endif; ?>
                                <h2 class="hero-slide-title"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                <?php if(!empty($slide['description'])): ?>
                                <p class="hero-slide-desc"><?php echo htmlspecialchars($slide['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="hero-pagination"></div>
            <div class="hero-slider-nav">
                <div class="hero-nav-btn hero-prev"><i class="ri-arrow-left-line"></i></div>
                <div class="hero-nav-btn hero-next"><i class="ri-arrow-right-line"></i></div>
            </div>
            <?php else: ?>
            <!-- Default Hero if no slides -->
            <div class="swiper hero-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide hero-slide">
                        <img src="assets/images/banner/gallery-hero.jpg" alt="Gallery" class="hero-slide-image" onerror="this.style.background='linear-gradient(135deg, #1a1a1a, #2a2a2a)'">
                        <div class="hero-slide-overlay">
                            <div class="hero-slide-content">
                                <span class="hero-slide-subtitle">RK Studio</span>
                                <h2 class="hero-slide-title">Our Photo Gallery</h2>
                                <p class="hero-slide-desc">Explore our collection of stunning photographs capturing life's most precious moments.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Breadcrumb -->
    <section class="gallery-breadcrumb">
        <div class="breadcrumb-container">
            <div class="breadcrumb-nav">
                <a href="index.php">Home</a>
                <span><i class="ri-arrow-right-s-line"></i></span>
                <span>Gallery</span>
            </div>
        </div>
    </section>

    <!-- Photos Section -->
    <section class="photos-section" id="photos-section">
        <div class="section-container">
            <div class="section-title">
                <h2>Photo <span>Gallery</span></h2>
                <p>Browse through our stunning collection of professional photography capturing special moments.</p>
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls">
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
            </div>

            <!-- Masonry Gallery Grid -->
            <div class="masonry-gallery">
                <?php 
                if($portfolio_items && mysqli_num_rows($portfolio_items) > 0){
                    $index = 0;
                    while($item = mysqli_fetch_assoc($portfolio_items)){
                        $index++;
                        $image_path = 'uploaded_img/' . $item['image'];
                        
                        // Determine size class for masonry effect
                        $size_class = '';
                        if($item['featured'] == 1 && $index <= 2) {
                            $size_class = 'tall';
                        } elseif($index % 5 == 0) {
                            $size_class = 'wide';
                        } elseif($index % 7 == 0) {
                            $size_class = 'tall';
                        }
                ?>
                <div class="gallery-item <?php echo $size_class; ?>">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                         class="gallery-item-image"
                         onerror="this.src='assets/images/portfolio/placeholder.jpg'">
                    
                    <a href="<?php echo htmlspecialchars($image_path); ?>" class="gallery-zoom popup-image">
                        <i class="ri-zoom-in-line"></i>
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
                        <p>No photos match your search. Try a different keyword.</p>
                        <a href="Gallery.php" class="filter-btn active" style="display: inline-block; margin-top: 15px;">
                            <i class="ri-refresh-line"></i> Clear Search
                        </a>
                    <?php elseif($filter != 'all'): ?>
                        <p>No photos in the "<?php echo htmlspecialchars(ucfirst($filter)); ?>" category yet.</p>
                        <a href="Gallery.php" class="filter-btn active" style="display: inline-block; margin-top: 15px;">
                            <i class="ri-apps-line"></i> View All Photos
                        </a>
                    <?php else: ?>
                        <p>Gallery photos will appear here once added by the admin.</p>
                    <?php endif; ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Videos Section -->
    <section class="videos-section" id="videos-section">
        <div class="section-container">
            <div class="section-title">
                <h2>Video <span>Collection</span></h2>
                <p>Watch our cinematic highlights capturing your special moments in motion.</p>
            </div>

            <?php if($videos_query && mysqli_num_rows($videos_query) > 0): ?>
            <div class="videos-slider-wrapper">
                <div class="swiper videos-swiper">
                    <div class="swiper-wrapper">
                        <?php while($video = mysqli_fetch_assoc($videos_query)): 
                            $thumbnail_path = !empty($video['thumbnail']) ? 'uploaded_img/' . $video['thumbnail'] : 'assets/images/portfolio/video-placeholder.jpg';
                        ?>
                        <div class="swiper-slide video-slide">
                            <div class="video-thumbnail">
                                <img src="<?php echo htmlspecialchars($thumbnail_path); ?>" 
                                     alt="<?php echo htmlspecialchars($video['title']); ?>"
                                     onerror="this.src='assets/images/portfolio/placeholder.jpg'">
                                <a href="<?php echo htmlspecialchars($video['video_url']); ?>" class="video-play-btn popup-video">
                                    <i class="ri-play-fill"></i>
                                </a>
                            </div>
                            <div class="video-info">
                                <span class="video-category"><?php echo htmlspecialchars(ucfirst($video['category'])); ?></span>
                                <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                <?php if(!empty($video['description'])): ?>
                                <p class="video-desc"><?php echo htmlspecialchars($video['description']); ?></p>
                                <?php endif; ?>
                                <?php if(!empty($video['duration'])): ?>
                                <p class="video-duration"><i class="ri-time-line"></i> <?php echo htmlspecialchars($video['duration']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="videos-nav videos-prev"><i class="ri-arrow-left-line"></i></div>
                <div class="videos-nav videos-next"><i class="ri-arrow-right-line"></i></div>
                <div class="videos-pagination"></div>
            </div>
            <?php else: ?>
            <div class="empty-video-state">
                <i class="ri-video-line"></i>
                <h3>No Videos Yet</h3>
                <p>Video collection coming soon! Check back later for cinematic highlights.</p>
            </div>
            <?php endif; ?>
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
    <script src="assets/js/plugins/swiper.min.js"></script>
    
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
        
        document.getElementById('backToTop').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Hero Banner Swiper
        const heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            speed: 1000,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            pagination: {
                el: '.hero-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.hero-next',
                prevEl: '.hero-prev',
            },
        });

        // Videos Swiper
        const videosSwiper = new Swiper('.videos-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            speed: 600,
            pagination: {
                el: '.videos-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.videos-next',
                prevEl: '.videos-prev',
            },
            breakpoints: {
                576: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
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
            });
        });
    </script>
</body>
</html>
