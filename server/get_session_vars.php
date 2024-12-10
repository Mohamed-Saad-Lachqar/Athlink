<?php
// Start the session
session_start();

// Set the Content-Type header for JSON response
header('Content-Type: application/json');

// Check if the 'userid' exists in the session
if (isset($_SESSION['user_id'])) {
    // Retrieve the 'userid' from session
    $userid = $_SESSION['user_id'];

    // Send the 'userid' to the front end
    echo json_encode([
        'status' => 'success',
        'userid' => $userid
    ]);
} else {
    // Redirect to the login page if 'userid' is not found
    header('Location: login.php');
    exit();
}
?>
