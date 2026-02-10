<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
 
   $select = " SELECT * FROM form WHERE email = '$email' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $row = mysqli_fetch_array($result);

      if($row['user_type'] == 'admin'){

         $_SESSION['admin_name'] = $row['name'];
         header('location:admin_page.php');

      }elseif($row['user_type'] == 'user'){


         $_SESSION['user_name'] = $row['name'];
         header('location:user_page.php');

      }
     
   }else{
      $error[] = 'Incorrect email or password!';
   }

};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RK Studio</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Theme -->
    <link rel="stylesheet" href="admin-theme.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo img {
            height: 60px;
            margin-bottom: 1rem;
        }
        .auth-logo h1 {
            font-size: 1.8rem;
            color: var(--text-white);
        }
        .auth-logo h1 span {
            color: var(--primary-gold);
        }
        .auth-title {
            text-align: center;
            color: var(--text-white);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        .auth-title span {
            color: var(--primary-gold);
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
        }
        .auth-footer a {
            color: var(--primary-gold);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }
        .error-msg {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: block;
            text-align: center;
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .form-control {
            padding-right: 50px;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 5px;
            transition: color 0.3s ease;
        }
        .password-toggle:hover {
            color: var(--primary-gold);
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="assets/images/logo10.png" alt="RK Studio" onerror="this.style.display='none'">
            <h1>RK <span>Studio</span></h1>
        </div>
        <h2 class="auth-title"><i class="fas fa-sign-in-alt"></i> Login <span>Now</span></h2>
        
        <?php
        if(isset($error)){
            foreach($error as $err){
                echo '<span class="error-msg"><i class="fas fa-exclamation-circle"></i> '.$err.'</span>';
            };
        };
        ?>
        
        <form action="" method="post">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" name="submit" class="btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login Now
            </button>
        </form>
        
        <p class="auth-footer">Don't have an account? <a href="register_form.php">Register Now</a></p>
        <p class="auth-footer" style="margin-top: 0.5rem;"><a href="index.html"><i class="fas fa-home"></i> Back to Home</a></p>
    </div>
</div>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>