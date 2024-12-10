<?php
session_start(); // Start the session

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

// Include the database connection file
require 'db.php'; // Ensure the path is correct based on your directory structure

// Check if the user ID is available in the session
if (isset($_SESSION['user_id'])) {
    $current_user_id = (int)$_SESSION['user_id']; // Get user ID from the session

    try {
        // Prepare the SQL query to fetch friends
        $sql = "SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.user_type,
                    u.profile_picture,
                    u.bio,
                    c.status
                FROM 
                    Connections c
                JOIN 
                    Users u ON (u.id = c.connected_user_id OR u.id = c.user_id)
                WHERE 
                    (c.user_id = :user_id OR c.connected_user_id = :user_id)
                    AND c.status = 'accepted'
                    AND u.id <> :current_user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $current_user_id);
        $stmt->bindParam(':current_user_id', $current_user_id);
        $stmt->execute();
        
        // Fetch the friends list
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send the result back to the front end in JSON format
        echo json_encode([
            'success' => true,
            'friends' => $friends
        ]);
    } catch (PDOException $e) {
        // Handle any database errors
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    // If no user ID is in the session, return an error
    echo json_encode([
        'success' => false,
        'message' => 'User is not logged in.'
    ]);
}
?>
