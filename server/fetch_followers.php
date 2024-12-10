<?php 
session_start(); // Start the session

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

// Include the database connection file
require 'db.php';

// Function to fetch followers of a user
function getFollowers($userId) {
    global $pdo;

    $sql = "SELECT u.id, u.name, u.email, u.profile_picture, u.bio 
            FROM follows f
            JOIN Users u ON f.follower_user_id = u.id
            WHERE f.followed_user_id = :followed_user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['followed_user_id' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get the user ID from the session
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null; // Get user ID from session

if ($userId !== null) {
    $followers = getFollowers($userId);

    echo json_encode($followers);
} else {
    // Handle the case where the user ID is not available
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'User is not logged in']);
}
?>
