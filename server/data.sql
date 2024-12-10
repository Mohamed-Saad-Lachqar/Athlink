-- 1. Users Table
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('athlete', 'club', 'coach') NOT NULL,
    profile_picture VARCHAR(255),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Athlete_Profile Table
CREATE TABLE Athlete_Profile (
    athlete_id INT PRIMARY KEY,
    sport VARCHAR(50) NOT NULL,
    description TEXT,
    FOREIGN KEY (athlete_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 3. Club_Profile Table
CREATE TABLE Club_Profile (
    club_id INT PRIMARY KEY,
    location VARCHAR(100),
    established_year YEAR,
    club_description TEXT,
    FOREIGN KEY (club_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 4. Coaches Table
CREATE TABLE Coaches (
    coach_id INT PRIMARY KEY,
    specialty_sport VARCHAR(50),
    experience_years INT,
    FOREIGN KEY (coach_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 5. Connections Table
CREATE TABLE Connections (
    connection_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    connected_user_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (connected_user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 6. Messages Table
CREATE TABLE Messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    read_status BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 7. Posts Table
CREATE TABLE Posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    media_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- 8. Follows Table
CREATE TABLE Follows (
    follower_user_id INT NOT NULL,
    followed_user_id INT NOT NULL,
    followed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_user_id) REFERENCES Users(id) ON DELETE CASCADE,
    PRIMARY KEY (follower_user_id, followed_user_id)
);

-- 9. Community Table
CREATE TABLE Community (
    community_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    profile_picture VARCHAR(255),
    banner VARCHAR(255),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES Users(id) ON DELETE CASCADE
);

-- 10. Community_Members Table
CREATE TABLE Community_Members (
    community_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (community_id) REFERENCES Community(community_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    PRIMARY KEY (community_id, user_id)
);
