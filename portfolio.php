<?php

@include 'db.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:login_form.php');
   exit();
}

$message = [];

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
mysqli_query($conn, $create_table);

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
mysqli_query($conn, $create_banner_table);

// Add banner slide
if(isset($_POST['add_banner_slide'])){
   $title = mysqli_real_escape_string($conn, $_POST['banner_title']);
   $subtitle = mysqli_real_escape_string($conn, $_POST['banner_subtitle']);
   $description = mysqli_real_escape_string($conn, $_POST['banner_description']);
   $display_order = intval($_POST['display_order']);
   $active = isset($_POST['banner_active']) ? 1 : 0;
   $image = $_FILES['banner_image']['name'];
   $image_tmp_name = $_FILES['banner_image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   if(move_uploaded_file($image_tmp_name, $image_folder)){
      $insert_query = mysqli_query($conn, "INSERT INTO banner_slides(title, subtitle, description, image, display_order, active) VALUES('$title', '$subtitle', '$description', '$image', '$display_order', '$active')");
      if($insert_query){
         header('location:portfolio.php?msg=banner_added');
         exit();
      }else{
         $message[] = ['type' => 'error', 'text' => 'Could not add banner slide.'];
      }
   }else{
      $message[] = ['type' => 'error', 'text' => 'Could not upload banner image.'];
   }
}

// Delete banner slide
if(isset($_GET['delete_banner'])){
   $delete_id = intval($_GET['delete_banner']);
   
   // Get image name to delete file
   $get_image = mysqli_query($conn, "SELECT image FROM banner_slides WHERE id = $delete_id");
   if(mysqli_num_rows($get_image) > 0){
      $row = mysqli_fetch_assoc($get_image);
      $image_path = 'uploaded_img/'.$row['image'];
      if(file_exists($image_path)){
         unlink($image_path);
      }
   }
   
   $delete_query = mysqli_query($conn, "DELETE FROM banner_slides WHERE id = $delete_id");
   if($delete_query){
      header('location:portfolio.php?msg=banner_deleted');
      exit();
   }
}

// Toggle banner slide active status
if(isset($_GET['toggle_banner'])){
   $toggle_id = intval($_GET['toggle_banner']);
   $get_status = mysqli_query($conn, "SELECT active FROM banner_slides WHERE id = $toggle_id");
   if(mysqli_num_rows($get_status) > 0){
      $row = mysqli_fetch_assoc($get_status);
      $new_status = $row['active'] ? 0 : 1;
      mysqli_query($conn, "UPDATE banner_slides SET active = $new_status WHERE id = $toggle_id");
      header('location:portfolio.php?msg=banner_toggled');
      exit();
   }
}

// Add portfolio item
if(isset($_POST['add_portfolio'])){
   $title = mysqli_real_escape_string($conn, $_POST['title']);
   $category = mysqli_real_escape_string($conn, $_POST['category']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);
   $featured = isset($_POST['featured']) ? 1 : 0;
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   if(move_uploaded_file($image_tmp_name, $image_folder)){
      $insert_query = mysqli_query($conn, "INSERT INTO portfolio(title, category, description, image, featured) VALUES('$title', '$category', '$description', '$image', '$featured')");
      if($insert_query){
         $message[] = ['type' => 'success', 'text' => 'Portfolio item added successfully!'];
      }else{
         $message[] = ['type' => 'error', 'text' => 'Could not add portfolio item.'];
      }
   }else{
      $message[] = ['type' => 'error', 'text' => 'Could not upload image.'];
   }
}

// Delete portfolio item
if(isset($_GET['delete'])){
   $delete_id = intval($_GET['delete']);
   
   // Get image name to delete file
   $get_image = mysqli_query($conn, "SELECT image FROM portfolio WHERE id = $delete_id");
   if(mysqli_num_rows($get_image) > 0){
      $row = mysqli_fetch_assoc($get_image);
      $image_path = 'uploaded_img/'.$row['image'];
      if(file_exists($image_path)){
         unlink($image_path);
      }
   }
   
   $delete_query = mysqli_query($conn, "DELETE FROM portfolio WHERE id = $delete_id");
   if($delete_query){
      header('location:portfolio.php?msg=deleted');
      exit();
   }
}

// Update portfolio item
if(isset($_POST['update_portfolio'])){
   $update_id = intval($_POST['update_id']);
   $title = mysqli_real_escape_string($conn, $_POST['title']);
   $category = mysqli_real_escape_string($conn, $_POST['category']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);
   $featured = isset($_POST['featured']) ? 1 : 0;
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];

   if(!empty($image)){
      $image_folder = 'uploaded_img/'.$image;
      move_uploaded_file($image_tmp_name, $image_folder);
      $update_query = mysqli_query($conn, "UPDATE portfolio SET title='$title', category='$category', description='$description', image='$image', featured='$featured' WHERE id='$update_id'");
   } else {
      $update_query = mysqli_query($conn, "UPDATE portfolio SET title='$title', category='$category', description='$description', featured='$featured' WHERE id='$update_id'");
   }
   
   if($update_query){
      header('location:portfolio.php?msg=updated');
      exit();
   }
}

// Check for URL messages
if(isset($_GET['msg'])){
   if($_GET['msg'] == 'deleted'){
      $message[] = ['type' => 'success', 'text' => 'Portfolio item deleted successfully!'];
   }elseif($_GET['msg'] == 'updated'){
      $message[] = ['type' => 'success', 'text' => 'Portfolio item updated successfully!'];
   }elseif($_GET['msg'] == 'video_added'){
      $message[] = ['type' => 'success', 'text' => 'Video added successfully!'];
   }elseif($_GET['msg'] == 'video_deleted'){
      $message[] = ['type' => 'success', 'text' => 'Video deleted successfully!'];
   }elseif($_GET['msg'] == 'banner_added'){
      $message[] = ['type' => 'success', 'text' => 'Banner slide added successfully!'];
   }elseif($_GET['msg'] == 'banner_deleted'){
      $message[] = ['type' => 'success', 'text' => 'Banner slide deleted successfully!'];
   }elseif($_GET['msg'] == 'banner_toggled'){
      $message[] = ['type' => 'success', 'text' => 'Banner slide status updated!'];
   }
}

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
mysqli_query($conn, $create_videos_table);

// Add description column if it doesn't exist (for older DBs with schema lacking this column)
$column_check = mysqli_query($conn, "SHOW COLUMNS FROM videos LIKE 'description'");
if($column_check && mysqli_num_rows($column_check) === 0) {
    mysqli_query($conn, "ALTER TABLE videos ADD COLUMN description TEXT AFTER duration");
}

// Add video
if(isset($_POST['add_video'])){
   $title = mysqli_real_escape_string($conn, $_POST['video_title']);
   $category = mysqli_real_escape_string($conn, $_POST['video_category']);
   $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);
   $duration = mysqli_real_escape_string($conn, $_POST['duration']);
   $description = mysqli_real_escape_string($conn, $_POST['video_description']);
   $featured = isset($_POST['video_featured']) ? 1 : 0;
   
   $thumbnail = '';
   if(!empty($_FILES['video_thumbnail']['name'])){
      $thumbnail = $_FILES['video_thumbnail']['name'];
      $thumb_tmp_name = $_FILES['video_thumbnail']['tmp_name'];
      $thumb_folder = 'uploaded_img/'.$thumbnail;
      move_uploaded_file($thumb_tmp_name, $thumb_folder);
   }
   
   $insert_video = mysqli_query($conn, "INSERT INTO videos(title, category, video_url, thumbnail, duration, description, featured) VALUES('$title', '$category', '$video_url', '$thumbnail', '$duration', '$description', '$featured')");
   if($insert_video){
      header('location:portfolio.php?msg=video_added');
      exit();
   }else{
      $message[] = ['type' => 'error', 'text' => 'Could not add video.'];
   }
}

// Delete video
if(isset($_GET['delete_video'])){
   $delete_id = intval($_GET['delete_video']);
   
   // Get thumbnail to delete file
   $get_thumb = mysqli_query($conn, "SELECT thumbnail FROM videos WHERE id = $delete_id");
   if(mysqli_num_rows($get_thumb) > 0){
      $row = mysqli_fetch_assoc($get_thumb);
      if(!empty($row['thumbnail'])){
         $thumb_path = 'uploaded_img/'.$row['thumbnail'];
         if(file_exists($thumb_path)){
            unlink($thumb_path);
         }
      }
   }
   
   $delete_query = mysqli_query($conn, "DELETE FROM videos WHERE id = $delete_id");
   if($delete_query){
      header('location:portfolio.php?msg=video_deleted');
      exit();
   }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Management - RK Studio Admin</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Theme -->
    <link rel="stylesheet" href="admin-theme.css">
    <style>
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
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
        
        .featured-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: var(--primary-gold);
            color: #000;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--primary-gold);
        }
        
        .checkbox-wrapper label {
            margin: 0;
            cursor: pointer;
        }
        
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .portfolio-item {
            background: var(--bg-card);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }
        
        .portfolio-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
            border-color: var(--primary-gold);
        }
        
        .portfolio-item-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .portfolio-item-content {
            padding: 1.25rem;
        }
        
        .portfolio-item-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }
        
        .portfolio-item-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .portfolio-item-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .portfolio-item-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .portfolio-item-actions a {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
    </style>
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
            <a href="portfolio.php" class="active"><i class="fas fa-briefcase"></i> Portfolio</a>
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

    <!-- Alert Messages -->
    <?php if(!empty($message)): ?>
        <?php foreach($message as $msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?>">
                <span><?php echo $msg['text']; ?></span>
                <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Banner Slides Management -->
    <div class="page-title">
        <h1><i class="fas fa-images"></i> Banner <span>Slides</span></h1>
        <p>Manage the homepage banner slider with professional wedding photography images</p>
    </div>

    <!-- Add Banner Slide Form -->
    <div class="admin-form-card">
        <h2><i class="fas fa-plus-circle"></i> Add Banner <span>Slide</span></h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="banner_title"><i class="fas fa-heading"></i> Title (displayed on slide)</label>
                <input type="text" name="banner_title" id="banner_title" class="form-control" placeholder="e.g., Capturing Your Special Moments" required>
            </div>
            <div class="form-group">
                <label for="banner_subtitle"><i class="fas fa-tag"></i> Subtitle (optional)</label>
                <input type="text" name="banner_subtitle" id="banner_subtitle" class="form-control" placeholder="e.g., RK Studio">
            </div>
            <div class="form-group">
                <label for="banner_description"><i class="fas fa-pen"></i> Description (optional)</label>
                <textarea name="banner_description" id="banner_description" class="form-control" placeholder="Brief description for the slide" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="banner_image"><i class="fas fa-image"></i> Banner Image (Recommended: 1920x1080 or larger)</label>
                <input type="file" name="banner_image" id="banner_image" class="form-control" accept="image/png, image/jpg, image/jpeg, image/webp" required>
            </div>
            <div class="form-group">
                <label for="display_order"><i class="fas fa-sort-numeric-up"></i> Display Order</label>
                <input type="number" name="display_order" id="display_order" class="form-control" placeholder="Enter order number (lower shows first)" value="0">
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="banner_active" id="banner_active" value="1" checked>
                <label for="banner_active"><i class="fas fa-eye"></i> Active (show on homepage)</label>
            </div>
            <button type="submit" name="add_banner_slide" class="btn-primary btn-block" style="margin-top: 1.5rem;">
                <i class="fas fa-upload"></i> Add Banner Slide
            </button>
        </form>
    </div>

    <!-- Banner Slides Grid -->
    <div class="portfolio-grid" style="margin-top: 2rem; margin-bottom: 3rem;">
        <?php 
        $select_banners = @mysqli_query($conn, "SELECT * FROM banner_slides ORDER BY display_order ASC, id DESC");
        if($select_banners && mysqli_num_rows($select_banners) > 0){
            while($banner = mysqli_fetch_assoc($select_banners)){
        ?>
        <div class="portfolio-item" style="<?php echo !$banner['active'] ? 'opacity: 0.6;' : ''; ?>">
            <div style="position: relative;">
                <img src="uploaded_img/<?php echo htmlspecialchars($banner['image']); ?>" class="portfolio-item-image" alt="<?php echo htmlspecialchars($banner['title']); ?>" style="height: 180px;">
                <?php if(!$banner['active']): ?>
                <div style="position: absolute; top: 10px; right: 10px; background: #ff4757; color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                    <i class="fas fa-eye-slash"></i> Hidden
                </div>
                <?php else: ?>
                <div style="position: absolute; top: 10px; right: 10px; background: #1dd1a1; color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                    <i class="fas fa-eye"></i> Active
                </div>
                <?php endif; ?>
                <div style="position: absolute; bottom: 10px; left: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 5px 12px; border-radius: 15px; font-size: 0.8rem;">
                    Order: <?php echo $banner['display_order']; ?>
                </div>
            </div>
            <div class="portfolio-item-content">
                <h3 class="portfolio-item-title"><?php echo htmlspecialchars($banner['title']); ?></h3>
                <?php if(!empty($banner['subtitle'])): ?>
                <p style="color: var(--primary-gold); font-size: 0.85rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($banner['subtitle']); ?></p>
                <?php endif; ?>
                <?php if(!empty($banner['description'])): ?>
                <p class="portfolio-item-desc"><?php echo htmlspecialchars($banner['description']); ?></p>
                <?php endif; ?>
                <div class="portfolio-item-actions" style="margin-top: 1rem;">
                    <a href="portfolio.php?toggle_banner=<?php echo $banner['id']; ?>" class="<?php echo $banner['active'] ? 'btn-secondary' : 'btn-edit'; ?>">
                        <i class="fas <?php echo $banner['active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i> <?php echo $banner['active'] ? 'Hide' : 'Show'; ?>
                    </a>
                    <a href="portfolio.php?delete_banner=<?php echo $banner['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this banner slide?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
        <?php 
            }
        } else { 
        ?>
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-images"></i></div>
                <h3 class="empty-state-title">No Banner Slides Yet</h3>
                <p>Add your first professional wedding photography banner using the form above!</p>
            </div>
        </div>
        <?php } ?>
    </div>

    <hr style="border-color: var(--border-color); margin: 3rem 0;">

    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-briefcase"></i> Portfolio <span>Management</span></h1>
        <p>Showcase your best work to attract new clients</p>
    </div>

    <!-- Add Portfolio Form -->
    <div class="admin-form-card">
        <h2><i class="fas fa-plus-circle"></i> Add Portfolio <span>Item</span></h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Enter project title (e.g., Sarah & John Wedding)" required>
            </div>
            <div class="form-group">
                <label for="category"><i class="fas fa-tag"></i> Category</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="wedding">Wedding Photography</option>
                    <option value="portrait">Portrait Photography</option>
                    <option value="event">Event Photography</option>
                    <option value="baby">Baby Photography</option>
                    <option value="drone">Drone Photography</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description"><i class="fas fa-pen"></i> Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Enter a brief description of the project" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="image"><i class="fas fa-image"></i> Select Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/png, image/jpg, image/jpeg, image/webp" required>
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="featured" id="featured" value="1">
                <label for="featured"><i class="fas fa-star"></i> Mark as Featured (show on homepage)</label>
            </div>
            <button type="submit" name="add_portfolio" class="btn-primary btn-block" style="margin-top: 1.5rem;">
                <i class="fas fa-upload"></i> Add to Portfolio
            </button>
        </form>
    </div>

    <!-- Portfolio Stats -->
    <?php 
    $portfolio_result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM portfolio");
    $portfolio_count = $portfolio_result ? (mysqli_fetch_assoc($portfolio_result)['count'] ?? 0) : 0;
    $featured_result = @mysqli_query($conn, "SELECT COUNT(*) as count FROM portfolio WHERE featured = 1");
    $featured_count = $featured_result ? (mysqli_fetch_assoc($featured_result)['count'] ?? 0) : 0;
    ?>
    <div class="stats-grid" style="max-width: 500px; margin: 2rem auto;">
        <div class="stat-card">
            <div class="stat-number"><?php echo $portfolio_count; ?></div>
            <div class="stat-label">Total Portfolio Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $featured_count; ?></div>
            <div class="stat-label">Featured Items</div>
        </div>
    </div>

    <!-- Portfolio Items Grid -->
    <div class="page-title">
        <h1><i class="fas fa-th-large"></i> Your <span>Portfolio</span></h1>
        <p>Manage and organize your showcase items</p>
    </div>

    <div class="portfolio-grid">
        <?php 
        $select_portfolio = @mysqli_query($conn, "SELECT * FROM portfolio ORDER BY featured DESC, id DESC");
        if($select_portfolio && mysqli_num_rows($select_portfolio) > 0){
            while($item = mysqli_fetch_assoc($select_portfolio)){
                $category_class = 'category-' . $item['category'];
        ?>
        <div class="portfolio-item">
            <img src="uploaded_img/<?php echo htmlspecialchars($item['image']); ?>" class="portfolio-item-image" alt="<?php echo htmlspecialchars($item['title']); ?>">
            <div class="portfolio-item-content">
                <h3 class="portfolio-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                <p class="portfolio-item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                <div class="portfolio-item-meta">
                    <span class="category-badge <?php echo $category_class; ?>"><?php echo ucfirst($item['category']); ?></span>
                    <?php if($item['featured']): ?>
                        <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                    <?php endif; ?>
                </div>
                <div class="portfolio-item-actions">
                    <a href="portfolio.php?edit=<?php echo $item['id']; ?>" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="portfolio.php?delete=<?php echo $item['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
        <?php 
            }
        } else { 
        ?>
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-briefcase"></i></div>
                <h3 class="empty-state-title">No Portfolio Items Yet</h3>
                <p>Add your first portfolio item using the form above!</p>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Video Management Section -->
    <div class="page-title" style="margin-top: 3rem;">
        <h1><i class="fas fa-video"></i> Video <span>Management</span></h1>
        <p>Add and manage video content for your gallery</p>
    </div>

    <!-- Add Video Form -->
    <div class="admin-form-card">
        <h2><i class="fas fa-plus-circle"></i> Add <span>Video</span></h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="video_title"><i class="fas fa-heading"></i> Video Title</label>
                <input type="text" name="video_title" id="video_title" class="form-control" placeholder="Enter video title (e.g., Wedding Highlights)" required>
            </div>
            <div class="form-group">
                <label for="video_category"><i class="fas fa-tag"></i> Category</label>
                <select name="video_category" id="video_category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="wedding">Wedding</option>
                    <option value="portrait">Portrait</option>
                    <option value="event">Event</option>
                    <option value="baby">Baby</option>
                    <option value="drone">Drone</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="video_url"><i class="fas fa-link"></i> Video URL (YouTube/Vimeo)</label>
                <input type="url" name="video_url" id="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
            </div>
            <div class="form-group">
                <label for="duration"><i class="fas fa-clock"></i> Duration</label>
                <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g., 5:30">
            </div>
            <div class="form-group">
                <label for="video_description"><i class="fas fa-pen"></i> Description (optional)</label>
                <textarea name="video_description" id="video_description" class="form-control" placeholder="Brief description about the video" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label for="video_thumbnail"><i class="fas fa-image"></i> Thumbnail Image (optional)</label>
                <input type="file" name="video_thumbnail" id="video_thumbnail" class="form-control" accept="image/png, image/jpg, image/jpeg, image/webp">
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="video_featured" id="video_featured" value="1">
                <label for="video_featured"><i class="fas fa-star"></i> Mark as Featured</label>
            </div>
            <button type="submit" name="add_video" class="btn-primary btn-block" style="margin-top: 1.5rem;">
                <i class="fas fa-upload"></i> Add Video
            </button>
        </form>
    </div>

    <!-- Videos Grid -->
    <div class="portfolio-grid" style="margin-top: 2rem;">
        <?php 
        $select_videos = @mysqli_query($conn, "SELECT * FROM videos ORDER BY featured DESC, id DESC");
        if($select_videos && mysqli_num_rows($select_videos) > 0){
            while($video = mysqli_fetch_assoc($select_videos)){
                $category_class = 'category-' . $video['category'];
                $thumbnail_path = !empty($video['thumbnail']) ? 'uploaded_img/' . $video['thumbnail'] : 'assets/images/portfolio/placeholder.jpg';
        ?>
        <div class="portfolio-item">
            <div style="position: relative;">
                <img src="<?php echo htmlspecialchars($thumbnail_path); ?>" class="portfolio-item-image" alt="<?php echo htmlspecialchars($video['title']); ?>" onerror="this.src='assets/images/portfolio/placeholder.jpg'">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(212, 168, 75, 0.9); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-play" style="color: #000; margin-left: 3px;"></i>
                </div>
            </div>
            <div class="portfolio-item-content">
                <h3 class="portfolio-item-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                <p class="portfolio-item-desc"><?php echo !empty($video['duration']) ? 'Duration: ' . htmlspecialchars($video['duration']) : 'Video'; ?></p>
                <div class="portfolio-item-meta">
                    <span class="category-badge <?php echo $category_class; ?>"><?php echo ucfirst($video['category']); ?></span>
                    <?php if($video['featured']): ?>
                        <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                    <?php endif; ?>
                </div>
                <div class="portfolio-item-actions">
                    <a href="<?php echo htmlspecialchars($video['video_url']); ?>" target="_blank" class="btn-edit">
                        <i class="fas fa-external-link-alt"></i> View
                    </a>
                    <a href="portfolio.php?delete_video=<?php echo $video['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this video?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
        <?php 
            }
        } else { 
        ?>
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-video"></i></div>
                <h3 class="empty-state-title">No Videos Yet</h3>
                <p>Add your first video using the form above!</p>
            </div>
        </div>
        <?php } ?>
    </div>

</div>

<!-- Edit Modal -->
<?php
if(isset($_GET['edit'])){
    $edit_id = intval($_GET['edit']);
    $edit_query = mysqli_query($conn, "SELECT * FROM portfolio WHERE id = $edit_id");
    if(mysqli_num_rows($edit_query) > 0){
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<div class="edit-modal active">
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3 class="edit-modal-title"><i class="fas fa-edit"></i> Edit Portfolio Item</h3>
            <a href="portfolio.php" class="edit-modal-close">&times;</a>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <img src="uploaded_img/<?php echo htmlspecialchars($fetch_edit['image']); ?>" class="edit-preview-image" alt="Current Image">
            <input type="hidden" name="update_id" value="<?php echo $fetch_edit['id']; ?>">
            <div class="form-group">
                <label for="edit_title"><i class="fas fa-heading"></i> Title</label>
                <input type="text" name="title" id="edit_title" class="form-control" value="<?php echo htmlspecialchars($fetch_edit['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="edit_category"><i class="fas fa-tag"></i> Category</label>
                <select name="category" id="edit_category" class="form-control" required>
                    <option value="wedding" <?php echo $fetch_edit['category'] == 'wedding' ? 'selected' : ''; ?>>Wedding Photography</option>
                    <option value="portrait" <?php echo $fetch_edit['category'] == 'portrait' ? 'selected' : ''; ?>>Portrait Photography</option>
                    <option value="event" <?php echo $fetch_edit['category'] == 'event' ? 'selected' : ''; ?>>Event Photography</option>
                    <option value="baby" <?php echo $fetch_edit['category'] == 'baby' ? 'selected' : ''; ?>>Baby Photography</option>
                    <option value="drone" <?php echo $fetch_edit['category'] == 'drone' ? 'selected' : ''; ?>>Drone Photography</option>
                    <option value="other" <?php echo $fetch_edit['category'] == 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_description"><i class="fas fa-pen"></i> Description</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3"><?php echo htmlspecialchars($fetch_edit['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="edit_image"><i class="fas fa-image"></i> New Image (optional)</label>
                <input type="file" name="image" id="edit_image" class="form-control" accept="image/png, image/jpg, image/jpeg, image/webp">
            </div>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="featured" id="edit_featured" value="1" <?php echo $fetch_edit['featured'] ? 'checked' : ''; ?>>
                <label for="edit_featured"><i class="fas fa-star"></i> Mark as Featured</label>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" name="update_portfolio" class="btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="portfolio.php" class="btn-secondary" style="flex: 1; text-align: center;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php
    }
}
?>

</body>
</html>
