<?php 
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id']; 

    try {
        // Prepare SQL query for additional public posts
        $Query = "
            SELECT p.post_id, p.content, p.created_at, u.name AS author_name, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.visibility = 'public'
            AND u.user_id = :user_id
        ";

        $stmt = $pdo->prepare($Query);
        $stmt->execute([':user_id' => $user_id]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($posts) {
            echo json_encode(["status" => "success", "data" => $posts]);
        } else {
            echo json_encode(["status" => "success", "message" => "No posts found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error fetching posts: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method or user_id not provided"]);
}
?>
