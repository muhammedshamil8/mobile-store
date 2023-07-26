<?php
/*
// db.php page 
// Function to establish the database connection
function connectToDatabase() {
    $servername = "mysql_db";     
    $database = "root";        
    $username = "root";    
    $password = "mobile_store";    

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Function to create tables and insert data
function createTablesAndData($pdo) {
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
            ('ronaldo', 3),
            ('l', 1),
            ('luluuuuu', 1),
            ('messi', 3),
            ('dd', 10),
            ('first product', 14),
            ('chat', 12),
            ('first product', 15),
            ('lp', 10),
            ('shamil', 20),
            ('mm', 16),
            ('lkl', 16),
            ('hh', 16),
            ('mk', 16),
            ('kk', 16),
            ('Jj', 21)
        ");

        // Create 'groups' table
        $pdo->exec("CREATE TABLE IF NOT EXISTS groups (
            id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            groupname varchar(255) NOT NULL,
            userid int NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        // Insert data into 'groups' table
        $pdo->exec("INSERT INTO groups (groupname, userid) VALUES
            ('yes', 10),
            ('d', 10),
            ('lulu', 10),
            ('harifa', 7),
            ('mm', 7),
            ('shamil', 10),
            ('group', 10),
            ('jj', 7)
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
            ('text.jpg', 'journey', 'Life is like a journey'),
            ('helo.jpg', 'fly', 'we want to fly !'),
            ('torism.jpeg', 'ff', 'dd')
        ");

        // Create 'users' table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username varchar(255) NOT NULL,
            password varchar(100) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

        // Insert data into 'users' table
        $pdo->exec("INSERT INTO users (username, password) VALUES
            ('ashii', 'yes'),
            ('demo', 'demoo')
        ");

        echo "Tables and data created successfully!";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Call the function to connect and create tables with data
$pdo = connectToDatabase();
createTablesAndData($pdo);
*/
?>