<?php

@include 'db.php';

$message = [];

if(isset($_POST['add_photo'])){
   $p_description = $_POST['p_description'];
   $p_image = $_FILES['p_image']['name'];
   $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
   $p_image_folder = 'uploaded_img/'.$p_image;

   $insert_query = mysqli_query($conn, "INSERT INTO photo(description, image) VALUES('$p_description', '$p_image')") or die('query failed');

   if($insert_query){
      move_uploaded_file($p_image_tmp_name, $p_image_folder);
      $message[] = ['type' => 'success', 'text' => 'Photo added successfully!'];
   }else{
      $message[] = ['type' => 'error', 'text' => 'Could not add the photo.'];
   }
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_query = mysqli_query($conn, "DELETE FROM photo WHERE id = $delete_id ") or die('query failed');
   if($delete_query){
      header('location:admin.php');
   }
};

if(isset($_POST['update_photo'])){
   $update_p_id = $_POST['update_p_id'];
   $update_p_description = $_POST['update_p_description'];
   $update_p_image = $_FILES['update_p_image']['name'];
   $update_p_image_tmp_name = $_FILES['update_p_image']['tmp_name'];
   $update_p_image_folder = 'uploaded_img/'.$update_p_image;

   if(!empty($update_p_image)){
      $update_query = mysqli_query($conn, "UPDATE photo SET description = '$update_p_description', image = '$update_p_image' WHERE id = '$update_p_id'");
      if($update_query){
         move_uploaded_file($update_p_image_tmp_name, $update_p_image_folder);
      }
   } else {
      $update_query = mysqli_query($conn, "UPDATE photo SET description = '$update_p_description' WHERE id = '$update_p_id'");
   }
   header('location:admin.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Photos - RK Studio Admin</title>
    
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
            <a href="portfolio.php"><i class="fas fa-briefcase"></i> Portfolio</a>
            <a href="admin.php" class="active"><i class="fas fa-plus-circle"></i> Add Photos</a>
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

    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-camera"></i> Add <span>Photos</span></h1>
        <p>Upload and manage your photography portfolio</p>
    </div>

    <!-- Add Photo Form -->
    <div class="admin-form-card">
        <h2><i class="fas fa-plus-circle"></i> Add New <span>Photo</span></h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="p_description"><i class="fas fa-pen"></i> Photo Description</label>
                <input type="text" name="p_description" id="p_description" class="form-control" placeholder="Enter photo description (e.g., Wedding, Portrait)" required>
            </div>
            <div class="form-group">
                <label for="p_image"><i class="fas fa-image"></i> Select Image</label>
                <input type="file" name="p_image" id="p_image" class="form-control" accept="image/png, image/jpg, image/jpeg" required>
            </div>
            <button type="submit" name="add_photo" class="btn-primary btn-block">
                <i class="fas fa-upload"></i> Add Photo
            </button>
        </form>
    </div>

    <!-- Photos Table -->
    <div class="page-title">
        <h1><i class="fas fa-images"></i> Manage <span>Photos</span></h1>
        <p>Edit or delete existing photos from your gallery</p>
    </div>

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th><i class="fas fa-image"></i> Preview</th>
                    <th><i class="fas fa-info-circle"></i> Description</th>
                    <th><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_photo = mysqli_query($conn, "SELECT * FROM photo ORDER BY id DESC");
                if(mysqli_num_rows($select_photo) > 0){
                    while($row = mysqli_fetch_assoc($select_photo)){
                ?>
                <tr>
                    <td>
                        <img src="uploaded_img/<?php echo $row['image']; ?>" class="photo-preview" alt="Photo">
                    </td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this photo?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                            <a href="admin.php?edit=<?php echo $row['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="3">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-camera"></i></div>
                            <h3 class="empty-state-title">No Photos Added Yet</h3>
                            <p>Upload your first photo using the form above!</p>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Edit Modal -->
<?php
if(isset($_GET['edit'])){
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM photo WHERE id = $edit_id");
    if(mysqli_num_rows($edit_query) > 0){
        $fetch_edit = mysqli_fetch_assoc($edit_query);
?>
<div class="edit-modal active">
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3 class="edit-modal-title"><i class="fas fa-edit"></i> Edit Photo</h3>
            <a href="admin.php" class="edit-modal-close">&times;</a>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <img src="uploaded_img/<?php echo $fetch_edit['image']; ?>" class="edit-preview-image" alt="Current Photo">
            <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
            <div class="form-group">
                <label for="update_p_description"><i class="fas fa-pen"></i> Description</label>
                <input type="text" name="update_p_description" id="update_p_description" class="form-control" value="<?php echo htmlspecialchars($fetch_edit['description']); ?>" required>
            </div>
            <div class="form-group">
                <label for="update_p_image"><i class="fas fa-image"></i> New Image (optional)</label>
                <input type="file" name="update_p_image" id="update_p_image" class="form-control" accept="image/png, image/jpg, image/jpeg">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="submit" name="update_photo" class="btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="admin.php" class="btn-secondary" style="flex: 1; text-align: center;">
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
