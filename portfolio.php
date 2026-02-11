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
            <a href="index.html"><i class="fas fa-globe"></i> Website</a>
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
