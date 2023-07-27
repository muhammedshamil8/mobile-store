<?php
session_start();
error_reporting(0);
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page or display an error message
    header("Location: index.php?error=Please log in first");
    exit();
  }
  
$message = "";
$deleteMessage = "";

// Check if there's a success message in the session
if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message from the session after displaying
}
// Check if there's a delete message in the session
if (isset($_SESSION['deleteMessage']) && !empty($_SESSION['deleteMessage'])) {
    $deleteMessage = $_SESSION['deleteMessage'];
    unset($_SESSION['deleteMessage']); // Clear the delete message from the session after displaying
}

$userId = isset($_GET['userid']) ? $_GET['userid'] : null;

// Fetch the actual userid from the users table
$stmt = $pdo->prepare("SELECT id FROM `users` WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rest of your POST handling code...
}
$product_name = isset($_GET['pn']) ? $_GET['pn'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_name = $_POST['product_name'];
    $groupname = $_POST['groupname'];

    if (!empty($product_name) && !empty($groupname)) {
       

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error());
        } else {
            $SELECT = "SELECT product_name FROM device WHERE product_name = ? AND groupid = (SELECT id FROM `groups` WHERE groupname = ?) LIMIT 1";
            $INSERT = "INSERT INTO device (product_name, groupid) VALUES (?, (SELECT id FROM `groups` WHERE groupname = ?))";

            // Prepare statement
            $stmt = $conn->prepare($SELECT);
            $stmt->bind_param("ss", $product_name, $groupname);
            $stmt->execute();
            $stmt->store_result();
            $rnum = $stmt->num_rows;

            if ($rnum == 0) {
                $stmt->close();

                $stmt = $conn->prepare($INSERT);
                $stmt->bind_param("ss", $product_name, $groupname);
                $stmt->execute();

                $message = "New product '$product_name' added successfully";
                $_SESSION['message'] = $message;
            } else {
                $message = "Someone has already added a product with the name '$product_name'";
                $_SESSION['message'] = $message;
            }
            $stmt->close();
        }
    } else {
        $message = "All fields are required";
        $_SESSION['message'] = $message;

        header("Location: product.php?groupid=" . urlencode($groupid));
exit();

    }
}

$product_name = isset($_GET['pn']) ? $_GET['pn'] : null;

if (!empty($product_name)) {
    $groupid = $_GET['groupid'];

    //  include 'console.php'; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "DELETE FROM device WHERE product_name = ? AND groupid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $product_name, $groupid);
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        $deleteMessage = "Record deleted from the database";
        $_SESSION['deleteMessage'] = $deleteMessage;
    } else {
        $deleteMessage = "Failed to delete record from the database";
        $_SESSION['deleteMessage'] = $deleteMessage;
    }

    $stmt->close();


    header("Location: product.php?groupid=" . urlencode($groupid));
    exit();

}

$groupid = $_GET['groupid'];

if (!empty($groupid)) {
    //  include 'console.php'; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $groupQuery = "SELECT groupname FROM `groups` WHERE id = ?";
    $stmt = $conn->prepare($groupQuery);
    $stmt->bind_param("s", $groupid);
    $stmt->execute();
    $stmt->bind_result($groupname);
    $stmt->fetch();
    $stmt->close();

    if (!empty($product_name)) {
        $productQuery = "SELECT device.id, device.product_name, `groups`.groupname 
    FROM device, `groups` 
    WHERE device.groupid = ? AND device.groupid = `groups`.id";

        $stmt = $conn->prepare($productQuery);
        $stmt->bind_param("ss", $groupid, $product_name);
    } else {
        $productQuery = "SELECT device.id, device.product_name, `groups`.groupname 
                 FROM device, `groups` 
                 WHERE device.groupid = ? AND device.groupid = `groups`.id";

        $stmt = $conn->prepare($productQuery);
        $stmt->bind_param("s", $groupid);
    }
    $stmt->execute();
    $productResult = $stmt->get_result();
    $products = $productResult->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $conn->close();

    // Retrieve all groups
    //  include 'console.php'; 

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM `device`";
    $result = $conn->query($sql);
    $productQuery = "SELECT device.id,device.product_name,upload.heading
FROM device,upload
WHERE device.id = upload.id
ORDER BY device.id";
    $product_names = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $product_names[$row["id"]] = $row["product_name"];
        }
    }

    $conn->close();

    //  include 'console.php'; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = $_GET['product_id'];

// Retrieve details from the device and groups tables based on product_id
$productQuery = "SELECT device.id, device.product_name, `groups`.groupname 
                 FROM device, `groups` 
                 WHERE device.id = ? AND device.groupid = `groups`.id";

$stmt = $conn->prepare($productQuery);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$productResult = $stmt->get_result();
$productDetails = $productResult->fetch_assoc();
$stmt->close();

// Retrieve details from the upload table based on product_id
$uploadQuery = "SELECT * FROM `upload` WHERE product_id = ?";
$stmt = $conn->prepare($uploadQuery);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$uploadResult = $stmt->get_result();
$uploadDetails = $uploadResult->fetch_assoc();
$stmt->close();

$conn->close();
}

?>
<!DOCTYPE html>
<html>
<head>
<title>product Mobile-store</title>
  <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <!-- <link rel="stylesheet" href="page2.css"> -->
    <script src="page2.js"></script>
    <style>
    /* page2.css */
body {
  font-size: 30px;
  background-color: #949398ff;
  transition: background-color 0.3s, color 0.3s;
  margin: 0;
  padding: 0;
}

body.dark-mode {
  background-color: #212121;
  color: #ffffff;
}


/* Common styles for navigation bar */
nav {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #38444d;
  padding: 8px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 999;
  font-size: 18px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Reset default list styles and add common styles for navigation bar */
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #38444d;
}

li {
  float: left;
}

li a, .dropbtn {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

li a:hover:not(.active), .dropdown:hover .dropbtn {
  background-color: #111;
}

.active {
  background-color: #04AA6D;
  padding: 12px 16px;
}
.active:hover{
  color:#000;
  
}
/* Styles for dropdown menu */
li.dropdown {
  display: inline-block;
  position: relative;
}

.dropdown-content {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  text-align: left;
}

.dropdown-content a:hover {
  background-color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.logout-button { 
  position: absolute;
  background-color:  #38444d;
  color: #fff;
  border: none;
  border-radius: 1px;
  padding: 15px 16px;
  font-size: 19px;
  cursor: pointer;
  transition: background-color 0.3s;
  z-index: 9999;
  right: 100px;
}

.logout-button:hover {
  background-color: darkred;
}

input:checked+.slider {
  background-color: #2196F3;
}

input:focus+.slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked+.slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

.settings-button {
  position: absolute;
   top: 0px;
  right: 10px; 
  background-color: #38444d;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 15px 25px;
  font-size: 25px;
  cursor: pointer;
  transition: background-color 0.3s;
  z-index: 9999;
}

.settings-button:hover {
  background-color: #0095c2;
}

button.return-button {
  position: absolute;
  top: 20px;
  left: 5px;
  padding: 15px 25px;
  font-size: 20px;
  background-color: #5aa4dddc;
  color: #fff;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: background-color 0.3s;
  z-index: 1;
  overflow: hidden;
  text-align: center; 
}

button.return-button:hover {
  background-color: #207bc5f5;
}


button.return-button b {
  text-decoration: none;
}
button.return-button:before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background-color: #fff;
  opacity: 0.3;
  border-radius: 50%;
  transform: scale(0);
  transition: transform 0.5s ease-out;
}

button.return-button:hover:before {
  transform: scale(1);
}

button.return-button:hover {
  background-color: #207bc5f5;
}

button.return-button b {
  position: relative;
  z-index: 2;
}
@media (max-width: 480px) {
  .card {
    padding: 10px;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .card {
    padding: 15px;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .card {
    padding: 20px;
  }
}

@media (min-width: 1025px) {
  .card {
    padding: 40px;
  }
}

.settings-page {
  position: fixed;
  top: 72px;
  right: 20px;
  background-color: rgba(0, 0, 0, 0.8);
  color: #fff;
  border-radius: 8px;
  padding: 100px 60px;
  text-align: center;
  z-index: 9999;
}

.settings-page h2 {
  margin-top: 0;
  font-size: 32px;
}

.settings-page ol {
  list-style-type: none;
  padding: 0;
  font-size: 24px;
}

.settings-page li {
  margin-bottom: 22px;
  display: block;
}

.settings-page li input[type="checkbox"] {
  margin-right: 15px;
  transform: scale(2);
}

.headname h1 {
  font-size: 32px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  margin-bottom: 0;
}

.headname img {
  max-height: 50px; 
  border-radius: 50%; 
  margin-right: 10px; 
  border:2px solid   ;
}

.headname {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  border-radius: 10px 10px 0 0;
}
.headname.dark-mode {
  /* background-image: linear-gradient(to right, midnightblue, darkslateblue);  */
  color: #ffffff;
}

.headname img.dark-mode {
  border: 2px solid #fff; 
} 
.headname h1.dark-mode {
  color: #fff; 
}



/* The switch styles for dark mode */
.switch {
  position: absolute;
  top: 15px;
  right: 6px;
  display: inline-block;
  width: 100px;
  height: 60px;
  z-index: 9999;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
  border-radius: 60px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 50px;
  width: 50px;
  left: 2px;
  bottom: 6px;
  background-color: #ffffff;
  -webkit-transition: .4s;
  transition: .3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #3187ce;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(45px);
  -ms-transform: translateX(45px);
  transform: translateX(45px);
}

/* Card styles */
.card {
  position: absolute;
  top: 90px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #f7f4f4;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  padding: 20px;
  text-align: center;
  width:90%;
  max-width: 800px;
  /* border: 1px solid #ccc; */
}

.card.dark-mode {
  background-color: #171717;
  color: #ffffff;
  box-shadow: 0 2px 6px rgba(255, 255, 255, 0.1);
}

/* Add group button styles */
.add-group {
  background-color: #2196F3;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 16px 20px;
  font-size: 20px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 20px;
}

.add-group:hover {
  background-color: #48c72f;
}
.add-group-form {
  background-color: #f7f7f7;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  max-width: 400px;
  margin: 0 auto;
  display:none;
}

.add-group-form label {
  font-size: 18px;
  margin-bottom: 10px;
}

.add-group-form input[type="text"]{
  font-size: 16px;
  padding: 10px 16px;
  margin-bottom: 10px;
  width: 100%;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.add-group-form input[type="submit"] {
  font-size: 16px;
  margin-bottom: 10px;
  margin-left: 100px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #2196F3;
  color: #fff;
  border: none;
   padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-group-form input[type="submit"]:hover {
  background-color: #0d2880e8;
}

.add-group-form button {
  font-size: 16px;
  padding: 10px 16px;
  margin-bottom: 10px;
  margin-right: 100px;
  /* width: 30%; */
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #38444d;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.add-group-form button:hover {
  background-color: #202020;
  color: #fff;
}

/* Search container styles */
.search-container {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.search-input {
  flex: 1;
  margin-right: 10px;
  padding: 16px 32px;
  border: none;
  border-radius: 5px;
  font-size: 28px;
  width: 600px;
}

@media (max-width: 768px) {
  .search-input {
    font-size: 20px;
    padding: 16px 30px 16px 24px;
  }
}

@media (min-width: 1024px) {
  .search-input {
    font-size: 20px;
    padding: 27px 30px 16px 34px;
  }
}
body.dark-mode .search-input {
  background-color: #525151;
  color: #fff;
}
/* Close button styles */
.close-button {
  position: absolute;
  top: 50%;
  right: 100px;
  transform: translateY(-50%);
  cursor: pointer;
  background: red;
  padding: 1px;
  color: rgb(255, 255, 255);
  font-size: 45px;
  border-radius: 5px;
}

/* Search button styles */
.search-button {
  height: 70px;
  padding: 16px 32px;
  background-color: #2196F3;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-size: 24px;
  color: #fff;
}

.search-button:hover {
  background-color: #0d2880e8;
}
.table-container {
  overflow-x: auto;
}
/* Styled table styles */
.styled-table {
  border-collapse: collapse;
  margin: 25px auto; 
  font-size: 18px;
  font-family: sans-serif;
  max-width: 800px; 
  width: 90%;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
  table-layout: fixed;
}

.styled-table.dark-mode {
  background-color: #171717;
  color: #ffffff;
  box-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
}

.styled-table tr.header1 {
  background-color: #020533;
  color: #ffffff;
  text-align: center;
}

.styled-table.dark-mode tr.header1 {
  background-color: #03050c;
}

.styled-table tr.header2 {
  background-color: #009879;
  color: #ffffff;
  text-align: left;
}

.styled-table.dark-mode tr.header2 {
  background-color: #061148;
}
.styled-table th,
.styled-table td {
  padding: 10px 12px;
}

.styled-table tbody tr {
  border-bottom: thin solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
  background-color: #E1F8DC;
}

.styled-table.dark-mode tbody tr:nth-of-type(even) {
  background-color: #212124;
}

.styled-table tbody tr:nth-of-type(odd) {
  background-color: #C1E1D2;
}

.styled-table.dark-mode tbody tr:nth-of-type(odd) {
  background-color: #161618;
}

.styled-table tbody tr:last-of-type {
  border-bottom: none;
}

.styled-table tbody tr.active-row {
  font-weight: bold;
  color: #171717;
}

.styled-table.dark-mode tbody tr.active-row {
  color: #ffffff;
}

.styled-table tbody td:last-child {
  text-align: center;
}

/* Button styles */
.btn {
  padding: 20px 20px;
  font-size: 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  width: 30;
}


  .btn-primary {
    background-color: #2196F3;
    color: #fff;
    width: 60%;
  }

.btn-secondary {
  background-color: #38444d ;
  color: #fff;
  padding: 12px 20px;
  font-size: 20px;
  border: none;
  border-radius: 1px;
  cursor: pointer;
  width: 30;
}

.btn-red,
.btn-blue {
  padding: 11px 15px;
  font-size: 15px;
  border-radius: 6px;
  text-align: center;
  /* display: flex; */
  align-items: center;
  justify-content: center;
  cursor: pointer;
  width: 100px; /* Set the width to 100% for both buttons */
  transition: background-color 0.3s;
}
.btn-red {
  background-color: #dc3545;
  color: #fff;
  border: 2px solid #dc3545;
}
.btn-red:hover {
  background-color: #c82333;
}

.btn-blue {
  background-color: #007bff;
  color: #fff;
  border: 2px solid #007bff;
}
.btn-blue:hover {
  background-color: #0056b3;
}
h2 {
  font-size: 36px;
  margin-bottom: 20px;
}

h3 {
  font-size: 24px;
  margin-bottom: 15px;
}



.custom-link {
  text-decoration: none;
  color: black;
  font-size: 20px;
}
a{
  text-decoration: none;
}


@media (max-width: 768px) {
  .btn-red,
  .btn-blue {
    width: 100%; 
  }
}
@media (max-width: 480px) {
  .styled-table td {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 14px;
    width:100%;
  }

  .styled-table td .btn-red,
  .styled-table td .btn-blue {
    margin-top: 10px; 
  }
}

@media (max-width: 768px) {
  .styled-table {
    font-size: 16px;
    width:100%;
  }

  .styled-table th,
  .styled-table td {
    padding: 10px 12px;
  }
}

@media (max-width: 480px) {
  .styled-table {
    font-size: 14px;
  }

  .styled-table th,
  .styled-table td {
    padding: 8px 10px;
  }
}

.btn-red {
  background-color: red;
  color: white;
 
}

.btn-red:hover {
  background-color: darkred;
}

.btn-blue {
  background-color: rgb(4, 125, 206);
  color: white;
  
}
.btn-blue:hover {
  background-color: darkblue;
}

@media (max-width: 768px) {
  .styled-table {
    font-size: 16px;
  }

  .styled-table th,
  .styled-table td {
    padding: 10px 12px;
  }
}

@media (max-width: 480px) {
  .styled-table {
    font-size: 14px;
  }

  .styled-table th,
  .styled-table td {
    padding: 8px 10px;
  }
}

.table-container {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  overflow-x: auto;
}
.table-buttons {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-top: 10px;
  margin-bottom: 20px; 
}


@media (max-width: 480px) {
  .table-buttons {
    flex-direction: column;
    align-items: center;
  }

  .btn-red,
  .btn-blue {
    width: 100%;
  }
}



    </style>
</head>
<body id="body">
<header>
    <nav >
      <div class="container">
        <ul>
        <li><a href="group.php?userid=<?php echo $userId; ?>&groupid=<?php echo urlencode($groupid); ?>">Groups</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="about.php">About</a></li>
          <li style="float:left"><button class="logout-button"
              onclick="window.location.href = 'index.php?logout=true'">Log out</button></li>
          <li class="dropdown" style="float:right">
          <li> <a class="active" href="">Product</a></li>
            <button class="settings-button" class="settings-btn" onclick="openSettings()"><i class="fas fa-cog"></i>
            </button>
          </li>
        </ul>
      </div>
    </nav>
  </header>
<!-- 
    <label class="switch">
        <input type="checkbox" onclick="toggleDarkMode()" name="darkMode" checked>
        <span class="slider"></span>
    </label> -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
        <div class="headname">
                <img src="image/mobilelogo77.png" alt="project Logo"> <h1><em>Mobile-store </em></h1> </div>
                <div class="button-container">
  
                   

                    <div class="search-container mb-4"><br><br>
                        <input type="text" id="myInput" class="form-control search-input" placeholder="Search" onkeyup="searchFun()">
                        <span class="close-button" onclick="clearSearch()">&times;</span>
                        <button onclick="searchFun()" class="search-button"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="add-group-form" <?php if (!empty($groupname)) { echo 'style="display: none;"'; } ?>>
    <form method="POST" action="">
        <label>Product Name:
            <input type="text" name="product_name" placeholder="Product Name" required class="form-control mb-2">
        </label><br>
        <label>
        <input  type="text" name="fun" value="Have a Nice day &#x1F60A; " disabled class="form-control mb-2">
</label>
            <!-- Hidden input field to pass the group name -->
            <input type="hidden" name="groupname" value="<?php echo $groupname; ?>">
        
        <div class="d-flex justify-content-between">
            <input type="submit" class="btn btn-primaryy" value="Create"><br>
            <button onclick="backspace()" class="btn btn-secondaryy">Back</button>
        </div>
    </form>
</div>

                    
  
                              <button onclick="showAddproductForm()" class="btn btn-primary add-group mb-3">+ Add Products</button></div>

                    <!-- <h3 class="mb-3">★彡[ɢʀᴏᴜᴘ @ ]彡★</h3> -->
                    <div class="table-container">
                    <table class="styled-table" id="mytable">
                        <thead class="thead-dark">
                            
                            <tr class="header1"><th colspan="4" >🄶🅁🄾🅄🄿 :&nbsp;<?php echo $groupname; ?></th></tr>

                            <tr class="header2">
                                <th>No</th>
                                <th>Product Name</th>
                                <!-- <th>Group Name</th> -->
                                <th>Action</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($products) && !empty($products)): ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($products as $row): ?>
                                    <tr class="active-row">
                                        <td><?php echo $counter; ?></td>
                                        <td><?php echo $row["product_name"]; ?></td>
                                        
                                        <td>
                                            <a href='detail.php?product_id=<?php echo urlencode($row["id"]); ?>&product_name=<?php echo urlencode($row["product_name"]); ?>' class='custom-link'>
                                                <button class='btn-blue'>Open</button>
                                            </a>
                                        </td>
                                        <td>
                                            <a href='product.php?groupid=<?php echo urlencode($groupid); ?>&pn=<?php echo urlencode($row["product_name"]); ?>' onclick='return confirmRemove();'>
                                                <button class='btn-red'>Remove</button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $counter++; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan='4'>No products found for this group</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                            </div>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success text-center mb-3">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($deleteMessage)): ?>
                        <div class="alert alert-danger text-center mb-3">
                            <?php echo $deleteMessage; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div> 

    <div class="settings-page" style="display: none;">

      <h2>Settings</h2>
      <ol>
        <li>
          <label>
            <input type="checkbox" name="darkMode" onclick="toggleDarkMode()" checked> Dark Mode
          </label>
        </li><br>
        <li>
          <label>
            <input type="checkbox" name="notifications"> Notifications
          </label>
        </li><br>
        <li>
          <label>
            <input type="checkbox" name="privacy"> Privacy
          </label>
        </li><br>
        <li>
          <label>
            <button class="return-button" onclick="closeSettings()">
              <i class="fas fa-chevron-left"></i></button>
          </label>
        </li>
      </ol>

    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- completed the project -->
    <!-- Your existing HTML code... -->

<!-- Add the JavaScript script here -->
<script>
    // Function to automatically hide the alert message after a specified duration
    function hideAlerts() {
        var successAlert = document.querySelector('.alert-success');
        var errorAlert = document.querySelector('.alert-danger');

        if (successAlert) {
            setTimeout(function() {
                successAlert.style.display = 'none';
            }, 5000); // Hide the success alert after 5 seconds (5000 milliseconds)
        }

        if (errorAlert) {
            setTimeout(function() {
                errorAlert.style.display = 'none';
            }, 5000); // Hide the error alert after 5 seconds (5000 milliseconds)
        }
    }

    // Call the function when the page is loaded
    window.onload = function() {
        hideAlerts();
    };
    function openSettings() {
  var settingsPage = document.querySelector('.settings-page');
  settingsPage.style.display = 'block';
}

function closeSettings() {
  var settingsPage = document.querySelector('.settings-page');
  settingsPage.style.display = 'none';
}

</script>

</body>
</html>
