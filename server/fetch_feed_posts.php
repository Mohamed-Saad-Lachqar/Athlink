<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    try {
        // Begin transaction to ensure query consistency
        $pdo->beginTransaction();

        // Prepare SQL query for posts from followed athletes and clubs
        $followedQuery = "
            SELECT p.post_id, p.content, p.created_at, u.name AS author_name, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            JOIN follows f ON u.user_id = f.followed_user_id
            WHERE f.follower_user_id = :user_id
        ";

        // Prepare SQL query for posts from connections
        $connectionsQuery = "
            SELECT p.post_id, p.content, p.created_at, u.name AS author_name, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            JOIN connections c ON (c.user_one_id = :user_id AND c.user_two_id = u.user_id)
               OR (c.user_two_id = :user_id AND c.user_one_id = u.user_id)
        ";

        // Prepare SQL query for posts from shared groups
        $groupsQuery = "
            SELECT p.post_id, p.content, p.created_at, u.name AS author_name, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            JOIN group_memberships gm ON u.user_id = gm.user_id
            JOIN groups g ON gm.group_id = g.group_id
            WHERE gm.user_id = :user_id
        ";

        // Prepare SQL query for additional public posts
        $additionalQuery = "
            SELECT p.post_id, p.content, p.created_at, u.name AS author_name, u.profile_picture
            FROM posts p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.visibility = 'public'
            AND u.user_id != :user_id
        ";

        // Combine the queries with UNION to prevent duplicates and order by priority
        $finalQuery = "
            ($followedQuery)
            UNION
            ($connectionsQuery)
            UNION
            ($groupsQuery)
            UNION
            ($additionalQuery)
            ORDER BY created_at DESC
            LIMIT 50
        ";

        $stmt = $pdo->prepare($finalQuery);
        $stmt->execute([':user_id' => $user_id]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Commit transaction
        $pdo->commit();

        if ($posts) {
            echo json_encode(["status" => "success", "data" => $posts]);
        } else {
            echo json_encode(value: ["status" => "success", "message" => "No posts found"]);
        }
    } catch (PDOException $e) {
        // Rollback transaction if error occurs
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error fetching posts: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method or missing user_id"]);
}
?>
