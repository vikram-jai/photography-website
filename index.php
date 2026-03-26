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

// Get featured portfolio items (limit to 6 for home page)
$portfolio_query = @mysqli_query($conn, "SELECT * FROM portfolio ORDER BY featured DESC, id DESC LIMIT 6");

// Get active banner slides
$banner_query = @mysqli_query($conn, "SELECT * FROM banner_slides WHERE active = 1 ORDER BY display_order ASC, id DESC");
$banner_slides = [];
if($banner_query && mysqli_num_rows($banner_query) > 0){
    while($slide = mysqli_fetch_assoc($banner_query)){
        $banner_slides[] = $slide;
    }
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>RK Studio - Professional Photography</title>
    <meta name="description" content="RK Studio - Professional Wedding, Event & Portrait Photography">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png">
    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/vendor/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/vendor/fontawesome-pro.css">
    <link rel="stylesheet" href="assets/css/vendor/spacing.css">
    <link rel="stylesheet" href="assets/css/vendor/remixicon.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Modern Theme Enhancement -->
    <style>
        :root {
            --gold: #d4a84b;
            --gold-dark: #b8923f;
            --gold-light: #e6c477;
            --dark-bg: #0f0f0f;
            --darker-bg: #0a0a0a;
            --card-bg: #1a1a1a;
            --card-bg-hover: #252525;
            --border-color: #2a2a2a;
            --text-muted: rgba(255,255,255,0.7);
            --gradient-gold: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
        }
        
        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-dark);
        }
        
        /* Header Enhancement */
        .rs-header-area {
            background: rgba(15, 15, 15, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }
        .rs-header-area.header-sticky {
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .main-menu ul li a {
            color: #fff !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        .main-menu ul li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gold);
            transition: width 0.3s ease;
        }
        .main-menu ul li a:hover::after {
            width: 100%;
        }
        .main-menu ul li a:hover {
            color: var(--gold) !important;
        }
        .rs-btn.has-theme-yellow {
            background: var(--gradient-gold) !important;
            color: #0f0f0f !important;
            font-weight: 600;
            border-radius: 30px !important;
            padding: 12px 28px !important;
            transition: all 0.3s ease;
            border: none !important;
            position: relative;
            overflow: hidden;
        }
        .rs-btn.has-theme-yellow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        .rs-btn.has-theme-yellow:hover::before {
            left: 100%;
        }
        .rs-btn.has-theme-yellow:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 168, 75, 0.4);
        }
        .btn-equal {
            background: transparent !important;
            border: 2px solid var(--gold) !important;
            color: var(--gold) !important;
            border-radius: 25px !important;
            padding: 8px 25px !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-equal:hover {
            background: var(--gold) !important;
            color: #0f0f0f !important;
            box-shadow: 0 5px 20px rgba(212, 168, 75, 0.3);
        }
        
        /* Banner Enhancement */
        .rs-banner-area {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .rs-banner-area::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/bg/banner-bg-10.png') no-repeat center;
            background-size: cover;
            opacity: 0.3;
        }
        /* Floating Particles Effect */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--gold);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 15s infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.3; }
            90% { opacity: 0.3; }
            100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
        }
        .rs-banner-subtitle {
            color: var(--gold) !important;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 14px !important;
            font-weight: 600;
            display: inline-block;
            position: relative;
        }
        .rs-banner-subtitle::before {
            content: '';
            position: absolute;
            left: -40px;
            top: 50%;
            width: 30px;
            height: 2px;
            background: var(--gold);
        }
        .rs-banner-title {
            font-size: 3.5rem !important;
            font-weight: 700;
            line-height: 1.2;
        }
        .rs-title-slide .cd-words-wrapper b {
            color: var(--gold) !important;
        }
        .rs-banner-description p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            line-height: 1.8;
        }
        .rs-banner-thumb img {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            border: 3px solid var(--border-color);
            animation: float-img 6s ease-in-out infinite;
        }
        @keyframes float-img {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        /* Services Section */
        .services-section {
            background: var(--darker-bg);
            padding: 100px 0;
            position: relative;
        }
        .services-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .service-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s ease;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-gold);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        .service-card:hover::before {
            transform: scaleX(1);
        }
        .service-card:hover {
            transform: translateY(-10px);
            background: var(--card-bg-hover);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            color: #0f0f0f;
            transition: all 0.3s ease;
        }
        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(10deg);
        }
        .service-card h4 {
            color: #fff;
            font-size: 1.4rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .service-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }
        
        /* Statistics Section */
        .stats-section {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            padding: 80px 0;
            position: relative;
        }
        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/bg/banner-bg-10.png') no-repeat center;
            background-size: cover;
            opacity: 0.1;
        }
        .stat-item {
            text-align: center;
            padding: 30px;
            position: relative;
        }
        .stat-number {
            font-size: 4rem;
            font-weight: 700;
            color: #0f0f0f;
            line-height: 1;
            margin-bottom: 10px;
        }
        .stat-label {
            color: rgba(0,0,0,0.7);
            font-size: 1.1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Portfolio Preview Section */
        .portfolio-section {
            background: var(--dark-bg);
            padding: 100px 0;
        }
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }
        .portfolio-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            aspect-ratio: 1;
        }
        .portfolio-item.large {
            grid-column: span 2;
            grid-row: span 2;
        }
        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 25px;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .portfolio-item:hover .portfolio-overlay {
            opacity: 1;
        }
        .portfolio-item:hover img {
            transform: scale(1.1);
        }
        .portfolio-overlay h5 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        .portfolio-overlay span {
            color: var(--gold);
            font-size: 0.9rem;
        }
        
        /* Testimonials Section */
        .testimonials-section {
            background: var(--darker-bg);
            padding: 100px 0;
            position: relative;
        }
        .testimonial-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid var(--border-color);
            position: relative;
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
        }
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 100px;
            color: var(--gold);
            opacity: 0.2;
            font-family: Georgia, serif;
            line-height: 1;
        }
        .testimonial-text {
            color: var(--text-muted);
            font-size: 1.05rem;
            line-height: 1.8;
            margin-bottom: 25px;
            font-style: italic;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .testimonial-avatar {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--gold);
        }
        .testimonial-info h6 {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 3px;
        }
        .testimonial-info span {
            color: var(--gold);
            font-size: 0.85rem;
        }
        .testimonial-stars {
            color: var(--gold);
            margin-bottom: 20px;
        }
        
        /* CTA Section */
        .cta-section {
            background: var(--dark-bg);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 168, 75, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .cta-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .cta-content h2 {
            color: #fff;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .cta-content p {
            color: var(--text-muted);
            font-size: 1.2rem;
            margin-bottom: 35px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* About Section Enhancement */
        .rs-about-area {
            background: var(--dark-bg);
            padding: 100px 0;
        }
        .rs-section-subtitle {
            color: var(--gold) !important;
            font-weight: 600;
            letter-spacing: 2px;
        }
        .rs-section-subtitle img {
            filter: brightness(0) saturate(100%) invert(72%) sepia(48%) saturate(497%) hue-rotate(6deg) brightness(92%) contrast(87%);
        }
        .rs-section-title {
            color: #fff !important;
            font-weight: 700;
        }
        .rs-section-paragraph {
            color: rgba(255,255,255,0.75) !important;
            line-height: 1.8;
        }
        .rs-about-thumb img {
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            border: 3px solid var(--border-color);
        }
        .rs-about-feature-list ul li {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
        }
        .rs-about-feature-list ul li i {
            color: var(--gold) !important;
        }
        
        /* Contact Section - Premium Modern Design */
        .rs-contact-area {
            background: linear-gradient(180deg, #0a0a0a 0%, #121212 50%, #0a0a0a 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .rs-contact-area::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/bg/contact-bg-01.png') no-repeat center;
            background-size: cover;
            opacity: 0.05;
        }
        
        /* Contact Cards */
        .contact-card {
            background: linear-gradient(145deg, #1a1a1a 0%, #141414 100%);
            border: 1px solid rgba(212, 168, 75, 0.2);
            border-radius: 20px;
            padding: 35px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .contact-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--gold) 0%, var(--gold-light) 50%, var(--gold) 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        .contact-card:hover {
            transform: translateY(-8px);
            border-color: rgba(212, 168, 75, 0.5);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), 0 0 40px rgba(212, 168, 75, 0.1);
        }
        .contact-card:hover::before {
            transform: scaleX(1);
        }
        .contact-card:hover .contact-card-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 30px rgba(212, 168, 75, 0.4);
        }
        .contact-card-icon {
            width: 70px;
            height: 70px;
            min-width: 70px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s ease;
            box-shadow: 0 5px 20px rgba(212, 168, 75, 0.3);
        }
        .contact-card-icon i {
            font-size: 28px;
            color: #0a0a0a;
        }
        .contact-card-content {
            flex: 1;
        }
        .contact-card-label {
            display: block;
            color: var(--gold);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .contact-card-value {
            margin: 0;
        }
        .contact-card-value a {
            color: #fff !important;
            font-size: 1.1rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
            line-height: 1.4;
        }
        .contact-card-value a:hover {
            color: var(--gold-light) !important;
        }
        
        /* Map Container */
        .contact-map-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            border: 2px solid rgba(212, 168, 75, 0.2);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        .contact-map-wrapper iframe {
            display: block;
            filter: grayscale(100%) brightness(0.8) contrast(1.2);
            transition: filter 0.4s ease;
        }
        .contact-map-wrapper:hover iframe {
            filter: grayscale(0%) brightness(1) contrast(1);
        }
        .map-direction-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #0a0a0a !important;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(212, 168, 75, 0.4);
        }
        .map-direction-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(212, 168, 75, 0.5);
            color: #0a0a0a !important;
        }
        .map-direction-btn i {
            font-size: 18px;
        }
        
        /* Responsive Adjustments for Contact */
        @media (max-width: 991px) {
            .contact-card {
                padding: 25px 20px;
            }
            .contact-card-icon {
                width: 60px;
                height: 60px;
                min-width: 60px;
                border-radius: 14px;
            }
            .contact-card-icon i {
                font-size: 24px;
            }
        }
        @media (max-width: 767px) {
            .contact-card-value a {
                font-size: 0.95rem;
            }
            .map-direction-btn {
                bottom: 15px;
                right: 15px;
                padding: 10px 20px;
                font-size: 13px;
            }
        }
        
        /* WhatsApp Floating Button */
        .whatsapp-float {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 30px;
            box-shadow: 0 5px 25px rgba(37, 211, 102, 0.4);
            z-index: 999;
            transition: all 0.3s ease;
            animation: pulse-whatsapp 2s infinite;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            color: #fff;
        }
        @keyframes pulse-whatsapp {
            0%, 100% { box-shadow: 0 5px 25px rgba(37, 211, 102, 0.4); }
            50% { box-shadow: 0 5px 35px rgba(37, 211, 102, 0.6); }
        }
        
        /* Footer Enhancement */
        .rs-footer-area {
            background: #0a0a0a !important;
            padding: 80px 0 40px;
            border-top: 1px solid var(--border-color);
        }
        .footer-widget {
            margin-bottom: 30px;
        }
        .footer-widget h5 {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        .footer-widget h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--gold);
        }
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links li {
            margin-bottom: 12px;
        }
        .footer-links li a {
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-links li a:hover {
            color: var(--gold);
            padding-left: 5px;
        }
        .footer-links li a i {
            font-size: 12px;
            color: var(--gold);
        }
        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }
        .footer-contact-icon {
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            flex-shrink: 0;
        }
        .footer-contact-text {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .footer-contact-text a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer-contact-text a:hover {
            color: var(--gold);
        }
        .footer-social {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .footer-social a {
            width: 45px;
            height: 45px;
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        .footer-social a:hover {
            background: var(--gold);
            color: #0f0f0f;
            transform: translateY(-3px);
        }
        .footer-bottom {
            border-top: 1px solid var(--border-color);
            padding-top: 25px;
            margin-top: 40px;
            text-align: center;
        }
        .footer-bottom p {
            color: var(--text-muted);
            margin: 0;
        }
        .footer-bottom a {
            color: var(--gold);
            text-decoration: none;
        }
        .rs-footer-list li a {
            color: rgba(255,255,255,0.7) !important;
            transition: all 0.3s ease;
        }
        .rs-footer-list li a:hover {
            color: var(--gold) !important;
        }
        
        /* Section Title Styling */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-subtitle {
            color: var(--gold);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            display: inline-block;
            margin-bottom: 15px;
            position: relative;
        }
        .section-subtitle::before,
        .section-subtitle::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30px;
            height: 1px;
            background: var(--gold);
        }
        .section-subtitle::before { right: 100%; margin-right: 15px; }
        .section-subtitle::after { left: 100%; margin-left: 15px; }
        .section-heading {
            color: #fff;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .section-desc {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .portfolio-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .portfolio-item.large {
                grid-column: span 2;
                grid-row: span 1;
            }
            .section-heading {
                font-size: 2.2rem;
            }
            .stat-number {
                font-size: 3rem;
            }
        }
        @media (max-width: 767px) {
            .portfolio-grid {
                grid-template-columns: 1fr;
            }
            .portfolio-item.large {
                grid-column: span 1;
            }
            .rs-banner-title {
                font-size: 2rem !important;
            }
            .rs-banner-area {
                min-height: auto;
                padding: 120px 0 80px;
            }
            .section-heading {
                font-size: 1.8rem;
            }
            .cta-content h2 {
                font-size: 2rem;
            }
        }
        
        /* Offcanvas Enhancement */
        .offcanvas-area {
            background: var(--dark-bg) !important;
        }
        .offcanvas-title-meta {
            color: var(--gold) !important;
        }
        .offcanvas-about p,
        .offcanvas-contact-text a {
            color: rgba(255,255,255,0.8) !important;
        }
        
        /* Social Icons */
        .rs-theme-social a {
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--gold) !important;
            margin-right: 10px;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        .rs-theme-social a:hover {
            background: var(--gold);
            color: #0f0f0f !important;
        }
        
        /* Back to Top */
        .backtotop-wrap {
            background: var(--gold) !important;
        }
        .backtotop-circle path {
            stroke: var(--gold-dark);
        }
        
        /* Banner Slider Styles */
        .banner-slider {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .banner-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            z-index: 1;
        }
        .banner-slide.active {
            opacity: 1;
            z-index: 2;
        }
        .banner-slide-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .banner-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.3) 100%);
            z-index: 1;
        }
        .banner-slide-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            text-align: center;
            width: 90%;
            max-width: 900px;
            padding: 20px;
        }
        .banner-slide-subtitle {
            color: var(--gold);
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 20px;
            display: inline-block;
            position: relative;
        }
        .banner-slide-subtitle::before,
        .banner-slide-subtitle::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40px;
            height: 2px;
            background: var(--gold);
        }
        .banner-slide-subtitle::before { right: 100%; margin-right: 15px; }
        .banner-slide-subtitle::after { left: 100%; margin-left: 15px; }
        .banner-slide-title {
            color: #fff;
            font-size: 4rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
        }
        .banner-slide-title span {
            color: var(--gold);
        }
        .banner-slide-desc {
            color: rgba(255,255,255,0.85);
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 35px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .banner-slide-btn {
            display: inline-block;
            background: var(--gradient-gold);
            color: #0f0f0f;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 25px rgba(212, 168, 75, 0.3);
        }
        .banner-slide-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(212, 168, 75, 0.5);
            color: #0f0f0f;
        }
        .banner-nav {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            gap: 12px;
        }
        .banner-nav-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            border: 2px solid rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .banner-nav-dot.active {
            background: var(--gold);
            border-color: var(--gold);
            transform: scale(1.2);
        }
        .banner-nav-dot:hover {
            background: rgba(255,255,255,0.5);
            border-color: #fff;
        }
        .banner-arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            pointer-events: none;
        }
        .banner-arrow {
            width: 55px;
            height: 55px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            pointer-events: auto;
            backdrop-filter: blur(5px);
        }
        .banner-arrow:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: #0f0f0f;
            transform: scale(1.1);
        }
        .banner-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: var(--gold);
            z-index: 10;
            transition: width 0.1s linear;
        }
        
        @media (max-width: 991px) {
            .banner-slide-title {
                font-size: 2.8rem;
            }
            .banner-slide-desc {
                font-size: 1rem;
            }
            .banner-arrow {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }
        }
        @media (max-width: 767px) {
            .banner-slider {
                height: 100vh;
                min-height: 600px;
            }
            .banner-slide-title {
                font-size: 2rem;
            }
            .banner-slide-subtitle {
                font-size: 0.85rem;
                letter-spacing: 2px;
            }
            .banner-slide-subtitle::before,
            .banner-slide-subtitle::after {
                width: 20px;
            }
            .banner-slide-desc {
                font-size: 0.95rem;
                margin-bottom: 25px;
            }
            .banner-slide-btn {
                padding: 12px 30px;
                font-size: 0.9rem;
            }
            .banner-arrows {
                padding: 0 15px;
            }
            .banner-arrow {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .rs-banner-title {
                font-size: 2.5rem !important;
            }
        }
        @media (max-width: 767px) {
            .rs-banner-title {
                font-size: 2rem !important;
            }
            .rs-banner-area {
                min-height: auto;
                padding: 120px 0 80px;
            }
        }
    </style>
</head>

<body class="rs-smoother-yes rtl">

    <!-- Header area start -->
    <header>
        <div class="rs-header-area header-transparent has-theme-yellow" id="header-sticky">
            <div class="container">
                <div class="rs-header-inner">
                    <!-- Logo Section -->
                    <div class="rs-header-left">
                        <div class="rs-header-logo">
                            <a href="index.php">
                                <img src="assets/images/logo10.png" alt="logo">
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
                                <!-- <li><a href="#homeportfolio">Portfolio</a></li> -->
                                <li><a href="Gallery.php">Gallery</a></li>
                                <li><a href="gifts.html">Gifts </a></li>
                                   <li><a href="frames.html">Frames</a></li> 
                                <li><a href="offers.html">Offers</a></li>
                                <li><a href="contact.html">Contact</a></li>
                                       <!-- <li><a href="register_form.php">Admin</a></li>  -->
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
                        
                        <!-- Login Button -->
                        <div class="login-container d-none d-lg-block">
                            <a href="register_form.php" class="btn btn-outline-light btn-equal">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Header area end -->

    <!-- Offcanvas area start -->
    <div class="fix">
        <div class="offcanvas-area has-theme-yellow" data-lenis-prevent>
            <div class="offcanvas-wrapper">
                <div class="offcanvas-content">
                    <div class="offcanvas-top d-flex justify-content-between align-items-center mb-25">
                        <div class="offcanvas-logo">
                            <a href="index.php">
                                <img src="assets/images/logo1.png" alt="logo not found">
                            </a>
                        </div>
                        <div class="offcanvas-close">
                            <button class="offcanvas-close-icon animation--flip">
                                <span class="offcanvas-m-lines">
                                  <span class="offcanvas-m-line line--1"></span><span
                                      class="offcanvas-m-line line--2"></span><span
                                      class="offcanvas-m-line line--3"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="offcanvas-about mb-30 d-none d-sm-block">
                        <h4 class="offcanvas-title-meta">About RK Studio </h4>
                        <p> RK Studio has 15 years of experience, offering creative and professional services to make every project unique and memorable.</p>
                    </div>
                    <div class="mobile-menu">
                        <div class="rs-offcanvas-menu mb-25">
                            <nav></nav>
                        </div>
                    </div>
                    <div class="offcanvas-contact mb-30">
                        <h4 class="offcanvas-title-meta">Contact Info</h4>
                        <ul>
                            <li class="d-flex align-items-center gap-15">
                                <div class="offcanvas-contact-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18" viewBox="0 0 14 18" fill="none">
                                        <path d="M11.8768 9.68475C11.3059 10.835 10.5331 11.9818 9.74128 13.0109C8.95198 14.0368 8.15999 14.9249 7.5643 15.5572C7.48514 15.6412 7.40956 15.7206 7.33802 15.7951C7.26648 15.7206 7.1909 15.6412 7.11174 15.5572C6.51605 14.9249 5.72406 14.0368 4.93476 13.0109C4.14299 11.9818 3.37019 10.835 2.79925 9.68475C2.22242 8.52266 1.89032 7.43373 1.89032 6.5C1.89032 3.50846 4.32934 1.08333 7.33802 1.08333C10.3467 1.08333 12.7857 3.50846 12.7857 6.5C12.7857 7.43373 12.4536 8.52266 11.8768 9.68475ZM7.33802 17.3333C7.33802 17.3333 13.8753 11.1732 13.8753 6.5C13.8753 2.91015 10.9484 0 7.33802 0C3.7276 0 0.800781 2.91015 0.800781 6.5C0.800781 11.1732 7.33802 17.3333 7.33802 17.3333Z" fill="#6D6D6D"></path>
                                        <path d="M7.33802 8.66667C6.13455 8.66667 5.15894 7.69662 5.15894 6.5C5.15894 5.30338 6.13455 4.33333 7.33802 4.33333C8.54149 4.33333 9.5171 5.30338 9.5171 6.5C9.5171 7.69662 8.54149 8.66667 7.33802 8.66667ZM7.33802 9.75C9.14323 9.75 10.6066 8.29492 10.6066 6.5C10.6066 4.70507 9.14323 3.25 7.33802 3.25C5.53281 3.25 4.0694 4.70507 4.0694 6.5C4.0694 8.29492 5.53281 9.75 7.33802 9.75Z" fill="#6D6D6D"></path>
                                    </svg>
                                </div>
                                <div class="offcanvas-contact-text">
                                    <a href="#"> Therkuvasal,Madurai,TamilNadu,India <br>
                                       </a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center gap-15">
                                <div class="offcanvas-contact-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M3.65387 1.32849C3.40343 1.00649 2.92745 0.976861 2.639 1.26531L1.60508 2.29923C1.1216 2.78271 0.94387 3.46766 1.1551 4.06847C2.00338 6.48124 3.39215 8.74671 5.32272 10.6773C7.25329 12.6078 9.51876 13.9966 11.9315 14.8449C12.5323 15.0561 13.2173 14.8784 13.7008 14.3949L14.7347 13.361C15.0231 13.0726 14.9935 12.5966 14.6715 12.3461L12.3653 10.5524C12.2008 10.4245 11.9866 10.3793 11.7845 10.4298L9.59541 10.9771C9.00082 11.1257 8.37183 10.9515 7.93845 10.5181L5.48187 8.06155C5.04849 7.62817 4.87427 6.99919 5.02292 6.40459L5.57019 4.21553C5.62073 4.01336 5.57552 3.79918 5.44758 3.63468L3.65387 1.32849ZM1.88477 0.511076C2.62689 -0.231039 3.8515 -0.154797 4.49583 0.673634L6.28954 2.97983C6.6187 3.40304 6.73502 3.95409 6.60498 4.47423L6.05772 6.66329C5.99994 6.8944 6.06766 7.13888 6.2361 7.30732L8.69268 9.7639C8.86113 9.93235 9.1056 10.0001 9.33671 9.94229L11.5258 9.39502C12.0459 9.26499 12.597 9.3813 13.0202 9.71047L15.3264 11.5042C16.1548 12.1485 16.231 13.3731 15.4889 14.1152L14.455 15.1492C13.7153 15.8889 12.6089 16.2137 11.5778 15.8512C9.01754 14.9511 6.61438 13.4774 4.56849 11.4315C2.5226 9.38562 1.04895 6.98246 0.148838 4.42225C-0.213682 3.39112 0.11113 2.28472 0.85085 1.545L1.88477 0.511076Z" fill="#6D6D6D"></path>
                                        <path d="M11 0.5C11 0.223858 11.2239 0 11.5 0H15.5C15.7761 0 16 0.223858 16 0.5V4.5C16 4.77614 15.7761 5 15.5 5C15.2239 5 15 4.77614 15 4.5V1.70711L10.8536 5.85355C10.6583 6.04882 10.3417 6.04882 10.1464 5.85355C9.95118 5.65829 9.95118 5.34171 10.1464 5.14645L14.2929 1H11.5C11.2239 1 11 0.776142 11 0.5Z" fill="#6D6D6D"></path>
                                    </svg>
                                </div>
                                <div class="offcanvas-contact-text">
                                    <a href="tel:+91 9842047017 "> +91 9842047017 </a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center gap-15">
                                <div class="offcanvas-contact-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M2 2C0.895431 2 0 2.89543 0 4V12L2.58386e-05 12.0103C0.00555998 13.1101 0.898859 14 2 14H7.5C7.77614 14 8 13.7761 8 13.5C8 13.2239 7.77614 13 7.5 13H2C1.53715 13 1.14774 12.6855 1.03376 12.2586L6.67417 8.7876L8 9.5831L15 5.3831V8.5C15 8.77614 15.2239 9 15.5 9C15.7761 9 16 8.77614 16 8.5V4C16 2.89543 15.1046 2 14 2H2ZM5.70808 8.20794L1 11.1052V5.3831L5.70808 8.20794ZM1 4.2169V4C1 3.44772 1.44772 3 2 3H14C14.5523 3 15 3.44772 15 4V4.2169L8 8.4169L1 4.2169Z" fill="#6D6D6D"></path>
                                        <path d="M14.2467 14.2686C15.2567 14.2686 15.8339 13.4116 15.8339 12.2442V12.0344C15.8339 10.4297 14.6402 9 12.5197 9H12.4847C10.421 9 9 10.3598 9 12.4322V12.6465C9 14.8195 10.4385 16 12.3579 16H12.4016C12.9963 16 13.4204 15.9257 13.639 15.8251V15.0949C13.3941 15.2042 12.9656 15.2742 12.4585 15.2742H12.4147C11.0812 15.2742 9.84385 14.4872 9.84385 12.6202V12.4628C9.84385 10.8057 10.9019 9.73891 12.4847 9.73891H12.524C14.0587 9.73891 15.0075 10.7883 15.0075 12.065V12.183C15.0075 13.158 14.6839 13.5734 14.3691 13.5734C14.1374 13.5734 13.9582 13.4247 13.9582 13.1537V10.9631H13.0531V11.5315H13.0225C12.9394 11.2342 12.6552 10.9019 12.0693 10.9019C11.2911 10.9019 10.8101 11.4572 10.8101 12.3011V12.8301C10.8101 13.722 11.2998 14.2642 12.0693 14.2642C12.5415 14.2642 12.9656 14.0369 13.0837 13.6215H13.1274C13.2455 14.0412 13.7439 14.2686 14.2467 14.2686ZM11.7939 12.6814V12.4541C11.7939 11.9076 12.0212 11.6627 12.3666 11.6627C12.664 11.6627 12.9394 11.8551 12.9394 12.371V12.7383C12.9394 13.3111 12.6858 13.4816 12.3754 13.4816C12.0212 13.4816 11.7939 13.2673 11.7939 12.6814Z" fill="#6D6D6D"></path>
                                    </svg>
                                </div>
                                <div class="offcanvas-contact-text">
                                    <a href="mailto:jaivikram5509@gmail.com">jaivikram5509@gmail.com</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="offcanvas-social">
                        <h4 class="offcanvas-title-meta">Subscribe & Follow</h4>
                        <ul>
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        </ul>
                    </div>
                    <div>
                        <form class="d-flex" role="search">
                            <button class="btn btn-outline-light w-100" type="submit">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-overlay"></div>
    <div class="offcanvas-overlay-white"></div>
    <!-- Offcanvas area start -->

    <!-- Body main wrapper start -->
    <main>

        <!-- banner area start -->
        <section class="rs-banner-area rs-banner-twelve p-relative">
            <?php if(!empty($banner_slides)): ?>
            <!-- Dynamic Banner Slider -->
            <div class="banner-slider">
                <?php foreach($banner_slides as $index => $slide): ?>
                <div class="banner-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="uploaded_img/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>" class="banner-slide-image">
                    <div class="banner-slide-overlay"></div>
                    <div class="banner-slide-content">
                        <?php if(!empty($slide['subtitle'])): ?>
                        <span class="banner-slide-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></span>
                        <?php endif; ?>
                        <h1 class="banner-slide-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
                        <?php if(!empty($slide['description'])): ?>
                        <p class="banner-slide-desc"><?php echo htmlspecialchars($slide['description']); ?></p>
                        <?php endif; ?>
                        <a href="#homeportfolio" class="banner-slide-btn">Explore Portfolio</a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Navigation Arrows -->
                <div class="banner-arrows">
                    <div class="banner-arrow banner-prev"><i class="ri-arrow-left-line"></i></div>
                    <div class="banner-arrow banner-next"><i class="ri-arrow-right-line"></i></div>
                </div>
                
                <!-- Navigation Dots -->
                <div class="banner-nav">
                    <?php foreach($banner_slides as $index => $slide): ?>
                    <div class="banner-nav-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Progress Bar -->
                <div class="banner-progress"></div>
            </div>
            <?php else: ?>
            <!-- Default Banner (when no slides uploaded) -->
            <div class="particles">
                <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
                <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
                <div class="particle" style="left: 35%; animation-delay: 4s;"></div>
                <div class="particle" style="left: 50%; animation-delay: 1s;"></div>
                <div class="particle" style="left: 65%; animation-delay: 3s;"></div>
                <div class="particle" style="left: 75%; animation-delay: 5s;"></div>
                <div class="particle" style="left: 85%; animation-delay: 2.5s;"></div>
                <div class="particle" style="left: 95%; animation-delay: 4.5s;"></div>
            </div>
            <div class="rs-banner-bg" data-background="assets/images/bg/banner-bg-10.png"></div>
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-xxl-8 col-xl-8 col-lg-8">
                        <div class="rs-banner-content-wrapper">
                            <div class="rs-banner-content">
                                <span class="rs-banner-subtitle" data-aos="fade-right" data-aos-delay="100">Welcome to RK STUDIO</span>
                                <h1 class="rs-banner-title wow fadeInUp" data-wow-delay=".3s" data-wow-duration=".7s">Professional <span style="color: var(--gold);">Wedding Photography</span></h1>
                                <div class="rs-banner-description wow fadeInUp" data-wow-delay=".5s" data-wow-duration=".9s">
                                    <p>RK Studio has 15 years of experience, offering creative and professional services to make every project unique and memorable.</p>
                                </div>
                                <div class="rs-banner-info-btn wow fadeInUp" data-wow-delay=".7s" data-wow-duration="1.1s">
                                    <div class="rs-banner-btn">
                                        <a class="rs-btn has-theme-yellow has-radius" href="#homeportfolio">Explore Portfolio</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-xl-4 col-lg-4">
                        <div class="rs-banner-thumb wow fadeInRight" data-wow-delay=".9s" data-wow-duration="1.3s">
                            <img src="assets/images/rk3.png" alt="image">
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <!-- banner area end -->

        <!-- Services Section Start -->
        <section class="services-section" id="homeservices">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <span class="section-subtitle">What We Offer</span>
                    <h2 class="section-heading">Our Professional Services</h2>
                    <p class="section-desc">We provide comprehensive photography services tailored to capture your precious moments with creativity and expertise.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="ri-heart-line"></i>
                            </div>
                            <h4>Wedding Photography</h4>
                            <p>Capturing the magic of your special day with stunning, timeless photographs that tell your unique love story.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="ri-camera-lens-line"></i>
                            </div>
                            <h4>Pre-Wedding Shoots</h4>
                            <p>Beautiful pre-wedding photo sessions at stunning locations to celebrate your journey before the big day.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="ri-user-smile-line"></i>
                            </div>
                            <h4>Baby Photography</h4>
                            <p>Adorable and creative baby photoshoots that capture those precious early moments forever.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="ri-video-line"></i>
                            </div>
                            <h4>Candid Videography</h4>
                            <p>Professional candid videos that capture genuine emotions and authentic moments of your special events.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="ri-drone-line"></i>
                            </div>
                            <h4>Drone Photography</h4>
                            <p>Breathtaking aerial shots and training for drone photography to add a unique perspective to your events.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="ri-book-open-line"></i>
                            </div>
                            <h4>Album Designing</h4>
                            <p>Custom-designed premium photo albums that beautifully showcase your memories in artistic layouts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Services Section End -->

        <!-- Statistics Section Start -->
        <section class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                        <div class="stat-item">
                            <div class="stat-number" data-purecounter-start="0" data-purecounter-end="15" data-purecounter-duration="2" class="purecounter">15+</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                        <div class="stat-item">
                            <div class="stat-number" data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="2" class="purecounter">500+</div>
                            <div class="stat-label">Happy Couples</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                        <div class="stat-item">
                            <div class="stat-number" data-purecounter-start="0" data-purecounter-end="1000" data-purecounter-duration="2" class="purecounter">1000+</div>
                            <div class="stat-label">Projects Done</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                        <div class="stat-item">
                            <div class="stat-number" data-purecounter-start="0" data-purecounter-end="50000" data-purecounter-duration="2" class="purecounter">50K+</div>
                            <div class="stat-label">Photos Delivered</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Statistics Section End -->

        <!-- about area start -->
        <section id="homeabout" class="rs-about-area section-space rs-about-twelve">
            <div class="container">
                <div class="row g-5">
                    <div class="col-xl-6 col-lg-6">
                        <div class="rs-about-thumb wow fadeInLeft" data-wow-delay=".3s" data-wow-duration="1s">
                            <img src="assets/images/img4.jpg" alt="image">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="rs-about-content wow fadeInRight" data-wow-delay=".3s" data-wow-duration="1s">
                            <div class="rs-section-title-wrapper">
                                <span class="rs-section-subtitle justify-content-start has-theme-yellow">
                           About Me
                        </span>
                                <h2 class="rs-section-title rs-split-text-enable split-in-fade mb-20">RK Studio Photography</h2>
                                <p class="rs-section-paragraph">At [RATHINA KUMAR], RK Studio Photography has 15 years of experience in capturing timeless moments. We combine creativity, professionalism, and passion to deliver stunning photographs that preserve your most cherished memories.</p>
                            </div>
                            <div class="rs-about-feature-list">
                                <ul>
                                    <li>
                                        <i class="ri-instagram-line"></i>
                                        Wedding Photography
                                    </li>
                                    <li>
                                        <i class="ri-instagram-line"></i>
                                        Frames & Gifts 
                                    </li>
                                    <li>
                                        <i class="ri-instagram-line"></i>
                                        Training for Drone Photography
                                    </li>
                                    <li>
                                        <i class="ri-instagram-line"></i>
                                        Baby Shoots
                                    </li>
                                    <li>
                                        <i class="ri-instagram-line"></i>
                                        Album Designing
                                    <li>
                                        <i class="ri-instagram-line"></i>
                                        Candid Photography & Videography
                                    </li>

                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- about area end -->

        <!-- Portfolio Preview Section Start -->
        <section class="portfolio-section" id="homeportfolio">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <span class="section-subtitle">Our Work</span>
                    <h2 class="section-heading">Featured Portfolio</h2>
                    <p class="section-desc">Browse through our collection of recent works capturing love, joy, and precious moments.</p>
                </div>
                <div class="portfolio-grid" data-aos="fade-up" data-aos-delay="200">
                    <?php
                    if($portfolio_query && mysqli_num_rows($portfolio_query) > 0){
                        $count = 0;
                        while($portfolio_item = mysqli_fetch_assoc($portfolio_query)){
                            $count++;
                            $large_class = ($count == 1) ? 'large' : '';
                            $category_label = ucfirst($portfolio_item['category']);
                            $image_path = 'uploaded_img/' . $portfolio_item['image'];
                    ?>
                    <div class="portfolio-item <?php echo $large_class; ?>">
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($portfolio_item['title']); ?>" onerror="this.src='https://images.unsplash.com/photo-1519741497674-611481863552?w=800&q=80'">
                        <div class="portfolio-overlay">
                            <h5><?php echo htmlspecialchars($portfolio_item['title']); ?></h5>
                            <span><?php echo $category_label; ?> Photography</span>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        // Show default images if no portfolio items in database
                    ?>
                    <div class="portfolio-item large">
                        <img src="assets/images/portfolio/wedding-main.jpg" alt="Wedding Photography" onerror="this.src='https://images.unsplash.com/photo-1519741497674-611481863552?w=800&q=80'">
                        <div class="portfolio-overlay">
                            <h5>Wedding Ceremony</h5>
                            <span>Wedding Photography</span>
                        </div>
                    </div>
                    <div class="portfolio-item">
                        <img src="assets/images/portfolio/baby.jpg" alt="Baby Photography" onerror="this.src='https://images.unsplash.com/photo-1519689680058-324335c77eba?w=400&q=80'">
                        <div class="portfolio-overlay">
                            <h5>Baby Moments</h5>
                            <span>Baby Photography</span>
                        </div>
                    </div>
                    <div class="portfolio-item">
                        <img src="assets/images/portfolio/prewedding.jpg" alt="Pre-Wedding" onerror="this.src='https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400&q=80'">
                        <div class="portfolio-overlay">
                            <h5>Pre-Wedding</h5>
                            <span>Couple Shoot</span>
                        </div>
                    </div>
                    <div class="portfolio-item">
                        <img src="assets/images/portfolio/event1.jpg" alt="Event Photography" onerror="this.src='https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=400&q=80'">
                        <div class="portfolio-overlay">
                            <h5>Event Coverage</h5>
                            <span>Events</span>
                        </div>
                    </div>
                    <div class="portfolio-item">
                        <img src="assets/images/portfolio/candid.jpg" alt="Candid Photography" onerror="this.src='https://images.unsplash.com/photo-1529636798458-92182e662485?w=400&q=80'">
                        <div class="portfolio-overlay">
                            <h5>Candid Shots</h5>
                            <span>Candid Photography</span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="300">
                    <a href="Gallery.php" class="rs-btn has-theme-yellow has-radius">View Full Gallery <i class="ri-arrow-right-line"></i></a>
                </div>
            </div>
        </section>
        <!-- Portfolio Preview Section End -->

        <!-- Testimonials Section Start -->
        <section class="testimonials-section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <span class="section-subtitle">Testimonials</span>
                    <h2 class="section-heading">What Our Clients Say</h2>
                    <p class="section-desc">Read what our happy clients have to say about their experience with RK Studio.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                            <p class="testimonial-text">"RK Studio captured our wedding beautifully! Every moment was preserved perfectly. The team was professional and made us feel comfortable throughout. Highly recommend!"</p>
                            <div class="testimonial-author">
                                <img src="assets/images/user/client1.jpg" alt="Client" class="testimonial-avatar" onerror="this.src='https://randomuser.me/api/portraits/men/32.jpg'">
                                <div class="testimonial-info">
                                    <h6>Rajesh Kumar</h6>
                                    <span>Wedding Client</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                            </div>
                            <p class="testimonial-text">"The baby photoshoot was amazing! Kumar ji made our little one smile and captured the most adorable moments. The album design was also exceptional. Thank you!"</p>
                            <div class="testimonial-author">
                                <img src="assets/images/user/client2.jpg" alt="Client" class="testimonial-avatar" onerror="this.src='https://randomuser.me/api/portraits/women/44.jpg'">
                                <div class="testimonial-info">
                                    <h6>Priya Sharma</h6>
                                    <span>Baby Shoot Client</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-fill"></i>
                                <i class="ri-star-half-fill"></i>
                            </div>
                            <p class="testimonial-text">"Our pre-wedding shoot was magical! The locations chosen were perfect and the candid shots were breathtaking. RK Studio really knows how to capture emotions."</p>
                            <div class="testimonial-author">
                                <img src="assets/images/user/client3.jpg" alt="Client" class="testimonial-avatar" onerror="this.src='https://randomuser.me/api/portraits/men/65.jpg'">
                                <div class="testimonial-info">
                                    <h6>Vikram & Anita</h6>
                                    <span>Pre-Wedding Client</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonials Section End -->

        <!-- CTA Section Start -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content" data-aos="zoom-in">
                    <h2>Ready to Capture Your Special Moments?</h2>
                    <p>Let's create beautiful memories together. Book your photography session today and preserve your precious moments forever.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="order.html" class="rs-btn has-theme-yellow has-radius">Book a Session <i class="ri-calendar-line"></i></a>
                        <a href="#homecontact" class="btn-equal" style="padding: 14px 30px !important;">Contact Us <i class="ri-phone-line"></i></a>
                    </div>
                </div>
            </div>
        </section>
        <!-- CTA Section End -->

        <!-- contact area start -->
        <section id="homecontact" class="rs-contact-area rs-contact-one section-space p-relative has-theme-yellow">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-xl-8 col-lg-10 text-center">
                        <span class="rs-section-subtitle has-theme-yellow d-flex align-items-center justify-content-center gap-2 mb-3">
                            <img src="assets/images/shape/small-arrow.png" alt="image" style="width: 20px; filter: brightness(0) saturate(100%) invert(76%) sepia(47%) saturate(489%) hue-rotate(358deg) brightness(91%) contrast(87%);">
                            STAY CONNECTED
                        </span>
                        <h2 class="rs-section-title" style="color: #fff; font-size: 2.8rem; font-weight: 700;">Let's Work Together!</h2>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <!-- Phone Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div class="contact-card-content">
                                <span class="contact-card-label">Phone</span>
                                <h5 class="contact-card-value"><a href="tel:+919842047017">+91 98420 47017</a></h5>
                            </div>
                        </div>
                    </div>
                    <!-- Email Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div class="contact-card-content">
                                <span class="contact-card-label">Email</span>
                                <h5 class="contact-card-value"><a href="mailto:jaivikram5509@gmail.com">jaivikram5509@gmail.com</a></h5>
                            </div>
                        </div>
                    </div>
                    <!-- Address Card -->
                    <div class="col-lg-4 col-md-12">
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div class="contact-card-content">
                                <span class="contact-card-label">Address</span>
                                <h5 class="contact-card-value"><a href="https://www.google.com/maps/search/R+K+Studio+%26+Drones,+Gate+169,+South,+170,+S+Veli+St,+Madurai,+Tamil+Nadu+625001" target="_blank">Gate 169, S Veli St, Madurai</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Google Map -->
                <div class="row">
                    <div class="col-12">
                        <div class="contact-map-wrapper">
                            <iframe 
                                src="https://www.google.com/maps?q=R+K+Studio+%26+Drones,+Gate+169,+South,+170,+S+Veli+St,+Madurai,+Tamil+Nadu+625001&t=&z=17&ie=UTF8&iwloc=&output=embed" 
                                width="100%" 
                                height="400" 
                                style="border:0; border-radius: 20px;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                            <a href="https://www.google.com/maps/search/R+K+Studio+%26+Drones,+Gate+169,+South,+170,+S+Veli+St,+Madurai,+Tamil+Nadu+625001" target="_blank" class="map-direction-btn">
                                <i class="ri-direction-line"></i> Get Directions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- contact area end -->

    </main>
    <!-- Body main wrapper end -->

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/919842047017?text=Hi,%20I'm%20interested%20in%20your%20photography%20services" class="whatsapp-float" target="_blank" title="Chat on WhatsApp">
        <i class="ri-whatsapp-line"></i>
    </a>

    <!-- footer area start -->
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
                            <li><a href="Gallery.php"><i class="ri-arrow-right-s-line"></i> Gallery</a></li>
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
                                Therkuvasal, Madurai,<br>TamilNadu, India
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
            <div class="footer-bottom">
                <p>&copy; 2026 <a href="index.php">RK Studio</a>. All Rights Reserved. Crafted with <i class="ri-heart-fill" style="color: var(--gold);"></i> by RK Studio</p>
            </div>
        </div>
    </footer>
    <!-- footer area end -->

    <!-- back to top -->
    <!-- Backtotop start -->
    <div class="backtotop-wrap cursor-pointer">
        <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- Backtotop end -->

    <!-- JS here -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/plugins/waypoints.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins/meanmenu.min.js"></script>
    <script src="assets/js/plugins/swiper.min.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/vendor/magnific-popup.min.js"></script>
    <script src="assets/js/vendor/isotope.pkgd.min.js"></script>
    <script src="assets/js/vendor/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/plugins/nice-select.min.js"></script>
    <script src="assets/js/plugins/jarallax.min.js"></script>
    <script src="assets/js/vendor/ajax-form.js"></script>
    <script src="assets/js/plugins/easypie.js"></script>
    <script src="assets/js/plugins/headding-title.js"></script>
    <script src="assets/js/plugins/lenis.min.js"></script>
    <script src="assets/js/plugins/gsap.min.js"></script>
    <script src="assets/js/plugins/rs-anim-int.js"></script>
    <script src="assets/js/plugins/rs-scroll-trigger.min.js"></script>
    <script src="assets/js/plugins/rs-splitText.min.js"></script>
    <script src="assets/js/plugins/jquery.lettering.js"></script>
    <script src="assets/js/plugins/parallax-effect.min.js"></script>
    <script src="assets/js/vendor/purecounter.js"></script>
    <script src="assets/js/vendor/dark-light.js"></script>
    <script src="assets/js/main.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add header background on scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.rs-header-area');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Banner Slider Functionality
        (function() {
            const slides = document.querySelectorAll('.banner-slide');
            const dots = document.querySelectorAll('.banner-nav-dot');
            const prevBtn = document.querySelector('.banner-prev');
            const nextBtn = document.querySelector('.banner-next');
            const progressBar = document.querySelector('.banner-progress');
            
            if (slides.length === 0) return;
            
            let currentSlide = 0;
            let slideInterval;
            let progressInterval;
            const slideDuration = 6000; // 6 seconds per slide
            const progressStep = 50; // Update progress every 50ms
            let progressValue = 0;
            
            function updateProgress() {
                if (progressBar) {
                    progressValue += (progressStep / slideDuration) * 100;
                    progressBar.style.width = progressValue + '%';
                }
            }
            
            function goToSlide(n) {
                slides[currentSlide].classList.remove('active');
                dots[currentSlide]?.classList.remove('active');
                
                currentSlide = (n + slides.length) % slides.length;
                
                slides[currentSlide].classList.add('active');
                dots[currentSlide]?.classList.add('active');
                
                // Reset progress
                progressValue = 0;
                if (progressBar) progressBar.style.width = '0%';
            }
            
            function nextSlide() {
                goToSlide(currentSlide + 1);
            }
            
            function prevSlide() {
                goToSlide(currentSlide - 1);
            }
            
            function startAutoPlay() {
                slideInterval = setInterval(nextSlide, slideDuration);
                progressInterval = setInterval(updateProgress, progressStep);
            }
            
            function stopAutoPlay() {
                clearInterval(slideInterval);
                clearInterval(progressInterval);
                progressValue = 0;
                if (progressBar) progressBar.style.width = '0%';
            }
            
            // Event Listeners
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    stopAutoPlay();
                    prevSlide();
                    startAutoPlay();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    stopAutoPlay();
                    nextSlide();
                    startAutoPlay();
                });
            }
            
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    stopAutoPlay();
                    goToSlide(index);
                    startAutoPlay();
                });
            });
            
            // Start autoplay
            if (slides.length > 1) {
                startAutoPlay();
            }
            
            // Pause on hover
            const slider = document.querySelector('.banner-slider');
            if (slider) {
                slider.addEventListener('mouseenter', stopAutoPlay);
                slider.addEventListener('mouseleave', startAutoPlay);
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    stopAutoPlay();
                    prevSlide();
                    startAutoPlay();
                } else if (e.key === 'ArrowRight') {
                    stopAutoPlay();
                    nextSlide();
                    startAutoPlay();
                }
            });
        })();
    </script>
</body>
</html>