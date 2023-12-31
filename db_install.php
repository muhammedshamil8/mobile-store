<?php

// db.php page 
// Function to establish the database connection
require_once "./db_conn.php";

// Function to create tables and insert data
function createTablesAndData($pdo)
{
    try {
        // Create 'device' table
        $pdo->exec("CREATE TABLE IF NOT EXISTS device (
            id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            product_name varchar(255) NOT NULL,
            groupid int NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        // Insert data into 'device' table
        $pdo->exec("INSERT INTO device (product_name, groupid) VALUES
            ('shamil', 1),
            ('lulu', 2),
            ('ronaldo', 3)
        ");

        // Create 'groups' table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `groups` (
            id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            groupname varchar(255) NOT NULL,
            userid int NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        // Insert data into 'groups' table
        $pdo->exec("INSERT INTO `groups` (groupname, userid) VALUES
            ('yes', 10),
            ('d', 10),
            ('lulu', 10)
        ");

        // Create 'upload' table
        $pdo->exec("CREATE TABLE IF NOT EXISTS upload (
            product_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            image varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
            heading varchar(255) NOT NULL,
            description varchar(500) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        // Insert data into 'upload' table
        $pdo->exec("INSERT INTO upload (image, heading, description) VALUES
        ('C.jpeg', 'happiness', 'it was something feelings in heart can\'t describe from words..!'),
        ('text.jpg', 'journey', 'Life is like a journey')
    ");
    

        // Create 'users' table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username varchar(255) NOT NULL,
            password varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        // Insert data into 'users' table
        $pdo->exec("INSERT INTO users (username, password) VALUES
        ('demo', 'demoo'),
        ('ashii', 'open123')
    ");
    

        echo "Tables and data created successfully!";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Call the function to connect and create tables with data
createTablesAndData($pdo);

// header("Location: index.php");
// exit;

?>
