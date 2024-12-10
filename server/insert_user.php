<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get common user data from POST
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $user_type = $_POST['user_type'];
    $profile_picture = $_POST['profile_picture']; // Assume this is a file path or URL
    $bio = $_POST['bio'];

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Insert into the users table
        $sql = "INSERT INTO users (name, email, password, user_type, profile_picture, bio) 
                VALUES (:name, :email, :password, :user_type, :profile_picture, :bio)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':user_type' => $user_type,
            ':profile_picture' => $profile_picture,
            ':bio' => $bio
        ]);

        // Get the last inserted user ID
        $user_id = $pdo->lastInsertId();

        // Insert additional data based on user_type
        if ($user_type === 'athlete') {
            // Athlete-specific data
            $sport = $_POST['sport'];
            $birthdate = $_POST['birthdate'];

            $sql = "INSERT INTO athlites_table (athlete_id, sport, birthdate) VALUES (:athlete_id, :sport, :birthdate)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':athlete_id' => $user_id,
                ':sport' => $sport,
                ':birthdate' => $birthdate
            ]);

        } elseif ($user_type === 'club') {
            // Club-specific data
            $location = $_POST['location'];
            $established_year = $_POST['established_year'];
            $club_description = $_POST['club_description'];

            $sql = "INSERT INTO clube_page (club_id, location, established_year, club_description) 
                    VALUES (:club_id, :location, :established_year, :club_description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':club_id' => $user_id,
                ':location' => $location,
                ':established_year' => $established_year,
                ':club_description' => $club_description
            ]);
        } elseif ($user_type === 'coach') {
            // Coach-specific data
            $specialty_sport = $_POST['specialty_sport'];
            $experience_years = $_POST['experience_years'];

            $sql = "INSERT INTO coaches (coach_id, specialty_sport, experience_years) 
                    VALUES (:coach_id, :specialty_sport, :experience_years)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':coach_id' => $user_id,
                ':specialty_sport' => $specialty_sport,
                ':experience_years' => $experience_years
            ]);
        }

        // Commit the transaction
        $pdo->commit();

        $_SESSION['user_id'] =  $user_id;



        echo json_encode(["status" => "success", "message" => "User and details added successfully"]);

    } catch (PDOException $e) {
        // Roll back if there is an error
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error adding user details: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
