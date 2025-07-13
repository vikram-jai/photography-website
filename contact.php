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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $message = $_POST['message'];
  $phone_number = $_POST['phone_number'];

  // Insert message into database
  $sql = "INSERT INTO messages (name, email, message, phone_number) VALUES ('$name', '$email', '$message', '$phone_number')";
  if ($conn->query($sql) === TRUE) {
    echo 'Message sent successfully!';

  } else {
    echo 'Error sending message: ' . $conn->error;
  }
}

// Close database connection
$conn->close();
?>