<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        try {
            // Fetch user details
            $sql = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the user exists
            if ($user) {
                $user_type = $user['user_type'];

                // Initialize additional data variable
                $additional_data = null;

                // Fetch additional data based on user type
                if ($user_type === 'athlete') {
                    $sql = "SELECT sport, birthdate FROM athlites_table WHERE athlete_id = :athlete_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':athlete_id' => $user_id]);
                    $additional_data = $stmt->fetch(PDO::FETCH_ASSOC);

                } elseif ($user_type === 'club') {
                    $sql = "SELECT location, established_year, club_description FROM clube_page WHERE club_id = :club_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':club_id' => $user_id]);
                    $additional_data = $stmt->fetch(PDO::FETCH_ASSOC);

                } elseif ($user_type === 'coach') {
                    $sql = "SELECT specialty_sport, experience_years FROM coaches WHERE coach_id = :coach_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':coach_id' => $user_id]);
                    $additional_data = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                // Return the user data along with additional data
                echo json_encode([
                    "status" => "success",
                    "user" => $user,
                    "additional_data" => $additional_data
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "User not found"]);
            }

        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Error retrieving user data: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No user_id provided"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
