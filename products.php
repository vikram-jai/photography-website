<?php @include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>products</title>
    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- custom css file link -->
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <div class="container">
        <section class="products">
            <h1 class="heading">latest photos</h1>
            <div class="cake-container">
                <?php 
                $select_photo = mysqli_query($conn, "SELECT * FROM photo");
                if(mysqli_num_rows($select_photo) > 0){
                    while($fetch_photo = mysqli_fetch_assoc($select_photo)){
                ?>
                <div class="box">
                    <img src="uploaded_img/<?php echo $fetch_photo['image']; ?>" alt="">
                    <h3><?php echo $fetch_photo['description']; ?></h3>

                </div>
                <?php }; }; ?>
            </div>
        </section>
    </div>
    <!-- custom js file link -->
    <script src="script1.js"></script>
</body>
</html>