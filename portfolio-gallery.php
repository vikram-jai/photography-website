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

// Build query
if($filter == 'all') {
    $query = "SELECT * FROM portfolio ORDER BY featured DESC, id DESC";
} else {
    $filter = mysqli_real_escape_string($conn, $filter);
    $query = "SELECT * FROM portfolio WHERE category = '$filter' ORDER BY featured DESC, id DESC";
}

$portfolio_items = @mysqli_query($conn, $query);

// Get all categories for filter buttons
$categories_query = @mysqli_query($conn, "SELECT DISTINCT category FROM portfolio");
$categories = [];
if($categories_query) {
    while($cat = mysqli_fetch_assoc($categories_query)) {
        $categories[] = $cat['category'];
    }
}
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Portfolio - RK Studio | Professional Photography</title>
    <meta name="description" content="Browse our stunning portfolio of wedding, portrait, baby, and event photography.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo10.png">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/fontawesome-pro.css">
    <link rel="stylesheet" href="assets/css/vendor/remixicon.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
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
        }
        
        body {
            background: var(--darker-bg);
            color: var(--text-light);
        }
        
        .portfolio-hero {
            background: linear-gradient(135deg, rgba(26,26,26,0.95), rgba(15,15,15,0.98)), url('assets/images/bg/portfolio-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0 80px;
            text-align: center;
        }
        
        .portfolio-hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .portfolio-hero h1 span {
            color: var(--gold);
        }
        
        .portfolio-hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .portfolio-section {
            padding: 80px 0;
        }
        
        .filter-buttons {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .filter-btn {
            padding: 12px 28px;
            border: 2px solid var(--gold);
            background: transparent;
            color: var(--gold);
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--gold);
            color: #000;
        }
        
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .portfolio-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            border: 1px solid transparent;
        }
        
        .portfolio-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(212, 168, 75, 0.15);
            border-color: var(--gold);
        }
        
        .portfolio-card-image {
            position: relative;
            overflow: hidden;
            height: 280px;
        }
        
        .portfolio-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .portfolio-card:hover .portfolio-card-image img {
            transform: scale(1.1);
        }
        
        .portfolio-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: flex-end;
            padding: 1.5rem;
        }
        
        .portfolio-card:hover .portfolio-card-overlay {
            opacity: 1;
        }
        
        .portfolio-card-content {
            padding: 1.5rem;
        }
        
        .portfolio-card-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .portfolio-card-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .portfolio-card-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .category-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .category-wedding { background: linear-gradient(135deg, #ff6b9d, #c44569); color: white; }
        .category-portrait { background: linear-gradient(135deg, #6b5ce7, #4834d4); color: white; }
        .category-event { background: linear-gradient(135deg, #1dd1a1, #10ac84); color: white; }
        .category-baby { background: linear-gradient(135deg, #ffeaa7, #fdcb6e); color: #333; }
        .category-drone { background: linear-gradient(135deg, #74b9ff, #0984e3); color: white; }
        .category-other { background: linear-gradient(135deg, #dfe6e9, #b2bec3); color: #333; }
        
        .featured-star {
            color: var(--gold);
            font-size: 1.2rem;
        }
        
        .empty-portfolio {
            text-align: center;
            padding: 80px 20px;
            grid-column: 1 / -1;
        }
        
        .empty-portfolio i {
            font-size: 5rem;
            color: var(--gold);
            margin-bottom: 1.5rem;
            display: block;
        }
        
        .empty-portfolio h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .empty-portfolio p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }
        
        /* Lightbox styles */
        .lightbox-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }
        
        .lightbox-overlay.active {
            display: flex;
        }
        
        .lightbox-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
        }
        
        .lightbox-content img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 10px;
        }
        
        .lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .lightbox-close:hover {
            color: var(--gold);
        }
        
        .lightbox-caption {
            text-align: center;
            margin-top: 1rem;
            color: #fff;
        }
        
        .lightbox-caption h4 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .lightbox-caption p {
            color: var(--text-muted);
        }
        
        /* View button */
        .view-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-size: 1.5rem;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .portfolio-card:hover .view-btn {
            opacity: 1;
        }
        
        .view-btn:hover {
            transform: translate(-50%, -50%) scale(1.1);
            background: var(--gold-light);
        }
        
        @media (max-width: 768px) {
            .portfolio-hero h1 {
                font-size: 2.5rem;
            }
            
            .portfolio-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-buttons {
                gap: 0.5rem;
            }
            
            .filter-btn {
                padding: 10px 20px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body class="rs-smoother-move">

    <!-- Preloader start -->
    <div id="pre-load">
        <div id="loader" class="loader">
            <div class="loader-container">
                <div class='loader-icon'>
                    <img src="assets/images/logo10.png" alt="RK Studio" style="max-width: 80px;">
                </div>
            </div>
        </div>
    </div>
    <!-- preloader end -->

    <!-- Header area start -->
    <header>
        <div class="rs-header-area header-transparent has-theme-yellow" id="header-sticky">
            <div class="container">
                <div class="rs-header-inner">
                    <!-- Logo Section -->
                    <div class="rs-header-left">
                        <div class="rs-header-logo">
                            <a href="index.php">
                                <img src="assets/images/logo10.png" alt="RK Studio Logo">
                            </a>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <div class="rs-header-menu">
                        <nav id="mobile-menu" class="main-menu">
                            <ul class="onepage-menu">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="about.html">About</a></li>
                                <li><a href="service.html">Services</a></li>
                                <li><a href="portfolio-gallery.php" class="active">Portfolio</a></li>
                                <li><a href="Gallery.php">Gallery</a></li>
                                <li><a href="contact.html">Contact</a></li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Header Right Section -->
                    <div class="rs-header-right">
                        <!-- Schedule a Shoot Button -->
                        <div class="rs-header-btn style-one d-none d-sm-block">
                            <a class="rs-btn has-theme-yellow has-radius" href="order.html">Schedule a Shoot</a>
                        </div>
                        
                        <!-- Hamburger Menu -->
                        <div class="rs-header-hamburger">
                            <div class="sidebar-toggle">
                                <a class="bar-icon" href="javascript:void(0)">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header area end -->

    <!-- Hero Section -->
    <section class="portfolio-hero">
        <div class="container">
            <h1>Our <span>Portfolio</span></h1>
            <p>Explore our collection of stunning photography work across various categories</p>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section class="portfolio-section">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="filter-buttons">
                <a href="portfolio-gallery.php" class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">All</a>
                <a href="portfolio-gallery.php?category=wedding" class="filter-btn <?php echo $filter == 'wedding' ? 'active' : ''; ?>">Wedding</a>
                <a href="portfolio-gallery.php?category=portrait" class="filter-btn <?php echo $filter == 'portrait' ? 'active' : ''; ?>">Portrait</a>
                <a href="portfolio-gallery.php?category=event" class="filter-btn <?php echo $filter == 'event' ? 'active' : ''; ?>">Event</a>
                <a href="portfolio-gallery.php?category=baby" class="filter-btn <?php echo $filter == 'baby' ? 'active' : ''; ?>">Baby</a>
                <a href="portfolio-gallery.php?category=drone" class="filter-btn <?php echo $filter == 'drone' ? 'active' : ''; ?>">Drone</a>
            </div>

            <!-- Portfolio Grid -->
            <div class="portfolio-grid">
                <?php 
                if($portfolio_items && mysqli_num_rows($portfolio_items) > 0){
                    while($item = mysqli_fetch_assoc($portfolio_items)){
                        $category_class = 'category-' . $item['category'];
                ?>
                <div class="portfolio-card">
                    <div class="portfolio-card-image">
                        <img src="uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="view-btn" onclick="openLightbox('uploaded_img/<?php echo htmlspecialchars($item['image']); ?>', '<?php echo htmlspecialchars(addslashes($item['title'])); ?>', '<?php echo htmlspecialchars(addslashes($item['description'])); ?>')">
                            <i class="ri-eye-line"></i>
                        </div>
                    </div>
                    <div class="portfolio-card-content">
                        <h3 class="portfolio-card-title">
                            <?php echo htmlspecialchars($item['title']); ?>
                            <?php if($item['featured']): ?>
                                <i class="ri-star-fill featured-star" title="Featured"></i>
                            <?php endif; ?>
                        </h3>
                        <p class="portfolio-card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="portfolio-card-meta">
                            <span class="category-badge <?php echo $category_class; ?>"><?php echo ucfirst($item['category']); ?></span>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else { 
                ?>
                <div class="empty-portfolio">
                    <i class="ri-camera-lens-line"></i>
                    <h3>No Portfolio Items Yet</h3>
                    <p>We're working on adding our best work. Check back soon!</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox-overlay" id="lightbox">
        <div class="lightbox-content">
            <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
            <img src="" alt="" id="lightbox-image">
            <div class="lightbox-caption">
                <h4 id="lightbox-title"></h4>
                <p id="lightbox-desc"></p>
            </div>
        </div>
    </div>

    <!-- Footer area start -->
    <footer class="rs-footer-area rs-footer-one rs-bg-light-grey has-theme-yellow">
        <div class="container">
            <div class="row g-4">
                <!-- About Column -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <a href="index.php" class="d-block mb-4">
                            <img src="assets/images/logo10.png" alt="RK Studio Logo" style="max-height: 60px;">
                        </a>
                        <p style="color: var(--text-muted); margin-bottom: 20px; line-height: 1.8;">RK Studio has 15 years of experience in capturing timeless moments. We combine creativity, professionalism, and passion to deliver stunning photographs.</p>
                        <div class="footer-social">
                            <a href="https://facebook.com" target="_blank"><i class="ri-facebook-fill"></i></a>
                            <a href="https://instagram.com" target="_blank"><i class="ri-instagram-line"></i></a>
                            <a href="https://youtube.com" target="_blank"><i class="ri-youtube-fill"></i></a>
                            <a href="https://wa.me/919842047017" target="_blank"><i class="ri-whatsapp-line"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links Column -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5>Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="index.php"><i class="ri-arrow-right-s-line"></i> Home</a></li>
                            <li><a href="about.html"><i class="ri-arrow-right-s-line"></i> About Us</a></li>
                            <li><a href="service.html"><i class="ri-arrow-right-s-line"></i> Services</a></li>
                            <li><a href="portfolio-gallery.php"><i class="ri-arrow-right-s-line"></i> Portfolio</a></li>
                            <li><a href="contact.html"><i class="ri-arrow-right-s-line"></i> Contact</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Services Column -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5>Our Services</h5>
                        <ul class="footer-links">
                            <li><a href="service.html"><i class="ri-arrow-right-s-line"></i> Wedding Photography</a></li>
                            <li><a href="service.html"><i class="ri-arrow-right-s-line"></i> Pre-Wedding Shoots</a></li>
                            <li><a href="service.html"><i class="ri-arrow-right-s-line"></i> Baby Photography</a></li>
                            <li><a href="frames.html"><i class="ri-arrow-right-s-line"></i> Frames & Gifts</a></li>
                            <li><a href="service.html"><i class="ri-arrow-right-s-line"></i> Drone Photography</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact Column -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5>Contact Info</h5>
                        <div class="footer-contact-item">
                            <div class="footer-contact-icon">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div class="footer-contact-text">
                                R K Studio & Drones,<br>S Veli St, Madurai
                            </div>
                        </div>
                        <div class="footer-contact-item">
                            <div class="footer-contact-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div class="footer-contact-text">
                                <a href="tel:+919842047017">+91 98420 47017</a>
                            </div>
                        </div>
                        <div class="footer-contact-item">
                            <div class="footer-contact-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div class="footer-contact-text">
                                <a href="mailto:jaivikram5509@gmail.com">jaivikram5509@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 3rem; padding-top: 2rem; text-align: center;">
                <p style="color: var(--text-muted);">&copy; 2026 <a href="index.php" style="color: var(--gold);">RK Studio</a>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <!-- footer area end -->

    <!-- back to top -->
    <div class="backtotop-wrap cursor-pointer">
        <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    <!-- JS Files -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/plugins/waypoints.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins/meanmenu.min.js"></script>
    <script src="assets/js/plugins/swiper.min.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/vendor/magnific-popup.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Lightbox functions
        function openLightbox(src, title, desc) {
            document.getElementById('lightbox-image').src = src;
            document.getElementById('lightbox-title').textContent = title;
            document.getElementById('lightbox-desc').textContent = desc;
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                closeLightbox();
            }
        });
        
        // Close on overlay click
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if(e.target === this) {
                closeLightbox();
            }
        });
    </script>
</body>
</html>
