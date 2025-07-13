<html>
    <head>
        <title>Messages</title>
        <link rel="stylesheet" href="style3.css">
</head>
<body>
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

// Retrieve messages from database
$sql = "SELECT * FROM messages";
$result = $conn->query($sql);

// Display messages
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      echo '<div class="message">';
      echo '<p class="message-name">Name: ' . $row['name'] . '</p>';
      echo '<p class="message-email">Email: ' . $row['email'] . '</p>';
      echo '<p class="message-phone_number">Phone Number: ' . $row['phone_number'] . '</p>';
      echo '<p class="message-text">Message: ' . $row['message'] . '</p>';
      echo '</div>';
    }
  } else {
    echo '<p class="no-messages">No messages found.</p>';
  }
?>
</body>
</html>