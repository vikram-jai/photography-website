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

$success = false;
$error = '';
$customer_name = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $message = mysqli_real_escape_string($conn, $_POST['message']);
  $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
  $customer_name = $name;

  // Insert message into database
  $sql = "INSERT INTO messages (name, email, message, phone_number) VALUES ('$name', '$email', '$message', '$phone_number')";
  if ($conn->query($sql) === TRUE) {
    $success = true;
  } else {
    $error = 'Error sending message: ' . $conn->error;
  }
}

// Close database connection
$conn->close();

// WhatsApp number
$whatsapp_number = '919894596141';
$whatsapp_message = urlencode("Hi RK Studio! I just submitted a booking request. My name is " . $customer_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Booking Confirmed' : 'Booking Error'; ?> - RK Studio</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Theme -->
    <link rel="stylesheet" href="admin-theme.css">
    <style>
        .response-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .response-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 550px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            text-align: center;
        }
        .response-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
        }
        .response-icon.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .response-icon.error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        .response-title {
            font-size: 2rem;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }
        .response-title span {
            color: var(--primary-gold);
        }
        .response-message {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .whatsapp-section {
            background: var(--bg-darker);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid var(--border-color);
        }
        .whatsapp-title {
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            color: white;
            padding: 14px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .whatsapp-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.3);
        }
        .whatsapp-btn i {
            font-size: 1.3rem;
        }
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 1.5rem;
        }
        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--text-muted);
        }
        .contact-item i {
            color: var(--primary-gold);
        }
        .contact-item a {
            color: var(--text-light);
            text-decoration: none;
        }
        .contact-item a:hover {
            color: var(--primary-gold);
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: var(--text-muted);
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }
    </style>
</head>
<body>

<div class="response-container">
    <div class="response-card">
        <?php if($success): ?>
            <!-- Success State -->
            <div class="response-icon success">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="response-title">Booking <span>Confirmed!</span></h1>
            <p class="response-message">
                Thank you, <strong><?php echo htmlspecialchars($customer_name); ?></strong>! 
                Your booking request has been received successfully. 
                We'll get back to you shortly.
            </p>
            
            <!-- WhatsApp Section -->
            <div class="whatsapp-section">
                <p class="whatsapp-title"><i class="fas fa-bolt"></i> Want a faster response?</p>
                <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $whatsapp_message; ?>" 
                   target="_blank" 
                   class="whatsapp-btn">
                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                </a>
            </div>
            
            <div class="divider">or</div>
            
            <!-- Contact Info -->
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <a href="tel:+919894596141">+91 98945 96141</a>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:jaivikram5509@gmail.com">jaivikram5509@gmail.com</a>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Therkuvasal, Madurai, Tamil Nadu</span>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Error State -->
            <div class="response-icon error">
                <i class="fas fa-times"></i>
            </div>
            <h1 class="response-title">Something went <span>wrong</span></h1>
            <p class="response-message">
                <?php echo $error ? $error : 'There was an error processing your request. Please try again.'; ?>
            </p>
            
            <!-- WhatsApp Section -->
            <div class="whatsapp-section">
                <p class="whatsapp-title"><i class="fas fa-headset"></i> Need help? Contact us directly</p>
                <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=Hi%20RK%20Studio!%20I%20had%20trouble%20with%20booking.%20Can%20you%20help?" 
                   target="_blank" 
                   class="whatsapp-btn">
                    <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                </a>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="order.html" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Book Again
            </a>
            <a href="index.php" class="btn-primary">
                <i class="fas fa-home"></i> Go Home
            </a>
        </div>
    </div>
</div>

</body>
</html>