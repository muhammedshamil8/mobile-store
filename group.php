<?php
session_start();
include 'db_conn.php';


error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
// $userId = (int)$user['id'];
// try {
//   $pdo = new PDO('mysql:host=' . $servername . ';dbname=' . $database . ';charset=utf8mb4', $username, $password);
//   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//   $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
// } catch (PDOException $e) {
//   die("Connection failed: " . $e->getMessage());
// }

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  // Redirect to the login page or display an error message
  header("Location: index.php?error=Please log in first");
  exit();
}

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}

// Check if the userid is provided in the URL
if (!isset($_GET['userid'])) {
  header("Location: group.php");
  exit();
}

$userId = isset($_GET['userid']) ? $_GET['userid'] : null;

// Fetch the actual userid from the users table
$stmt = $pdo->prepare("SELECT id FROM `users` WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("User not found."); // Handle the case when the user is not found
}

$userid = $user['id'];

// Fetch groups for the user
$query = "SELECT id, groupname FROM `groups` WHERE userid = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userid]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['groupname'])) {
    $groupname = $_POST['groupname'];

    if (!empty($groupname)) {
      $stmt = $pdo->prepare("SELECT groupname FROM `groups` WHERE groupname = ? LIMIT 1");
      $stmt->execute([$groupname]);
      $rnum = $stmt->rowCount();

      if ($rnum == 0) {
        try {
          $stmt = $pdo->prepare("INSERT INTO `groups` (groupname, userid) VALUES (:groupname, :userid)");
          $stmt->bindParam(':groupname', $groupname);
          $stmt->bindParam(':userid', $userid); // Use the fetched userid here
          $stmt->execute();
          $message = "New group created successfully";
          $newGroup = ['id' => $pdo->lastInsertId(), 'groupname' => $groupname];
          $groups[] = $newGroup;
        } catch (PDOException $e) {
          $message = "Error: " . $e->getMessage();
        }
        // echo "SQL Query: INSERT INTO `groups` (groupname, userid) VALUES ('$groupname', '$userid')";

        $message = "New group created successfully";
      } else {
        $message = "Someone has already created a group with this name";
      }
    } else {
      echo "All fields are required";

      header("Location: group.php?userid=" . urlencode($userId));
exit();

    }
  }
}


$groupname = "";

if (isset($_GET['delete']) && $_GET['delete'] === 'true') {
  if (isset($_GET['gn'])) {
    $groupnameToDelete = $_GET['gn'];
    $groupname = $_GET['gn'];

    if (!empty($groupname)) {
      $stmt = $pdo->prepare("DELETE FROM `groups` WHERE groupname = ?");
      $stmt->execute([$groupname]);

      if ($stmt->rowCount() > 0) {
        $deleteMessage = "Group deleted from the database";
      } else {
        $deleteMessage = "Failed to delete group from the database";
      }

      header("Location: group.php?userid=" . urlencode($userId));
      exit();
    }
  }
}
if (isset($_GET['search'])) {
  $product_name = $_GET['search'];

  // Prepare the base product query
  $productQuery = "SELECT device.id, device.product_name, `groups`.groupname 
                  FROM device
                  JOIN `groups` ON device.groupid = `groups`.id
                  WHERE `groups`.userid = :userid";

  // Check if a search query is provided
  if (!empty($product_name)) {
    // Add the search condition to the query
    $productQuery .= " AND device.product_name LIKE :product_name";
  }

  $productQuery .= " ORDER BY device.id";

  try {
    $stmt = $pdo->prepare($productQuery);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);

    if (!empty($product_name)) {
      $product_name = "%{$product_name}%";
      $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    die("Error: " . $e->getMessage());
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>Group Mobile-store</title>
  <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">

  <!-- <link rel="stylesheet" type="text/css" href="page1.css"> -->
  <script src="page1.js">
  </script>
  <style>
    /* page1.css */
body {
  font-size: 16px;
  background-color: #f7f7f7;
  color: #000000;
  margin: 0;
  padding: 0;
}

body.dark-mode {
  background-color: #212124;
  color: #ffffff;
}

/* Common styles for navigation bar */
nav {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #38444d;
  padding: 10px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 999;
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
@media (max-width: 768px) {
  /* Styles for the navigation bar when the screen width is 768 pixels or less */
  nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #38444d;
    padding: 10px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 999;
  }
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
  background-image: linear-gradient(to right, #25b4ff, #75caff);
}
.dark-mode .headname {
  background-image: linear-gradient(to right, rgb(25, 25, 194), rgb(62, 183, 204));
  color: #fff;
}
.headname img.dark-mode {
  border: 2px solid #fff; 
} 
.headname h1.dark-mode {
  color: #fff; 
}

.show-button{
  color: rgb(255, 255, 255);
}

/* CSS */
.btn-danger {
  background-color: red;
  color: white;
  border: none;
  padding: 5px 10px;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-danger:hover {
  background-color: darkred;
}

.card {
  position: absolute;
  top: 80px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #f7f4f4;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  padding: 40px;
  text-align: center;
  font-size: 24px;
  width:90%;
   max-width: 800px;
}

.card.dark-mode {
  background-color: #000000;
  color: #ffffff;
  box-shadow: 0 2px 6px rgba(255, 255, 255, 0.1);
}
.add-group {
  background-color: #2196F3;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 20px 40px;
  font-size: 22px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 20px;
}

.add-group:hover  {
  background-color: #12c402f5;
}

.add-group-form {
  display: none;
  text-align: center;
  padding: 30px;
}

.add-group-form input[type="text"],
.add-group-form input[type="submit"],
.add-group-form button {
  font-size: 26px;
  padding: 12px 24px;
  margin-bottom: 10px;
  width: 400px;
}


.logout-button { 
  position: absolute;
  background-color:  #38444d;
  color: #fff;
  border: none;
  border-radius: 1px;
  padding: 13px 20px;
  font-size: 18px;
  cursor: pointer;
  transition: background-color 0.3s;
  z-index: 9999;
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
  right: 1px; 
  background-color: #38444d;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 13px 25px;
  font-size: 25px;
  cursor: pointer;
  transition: background-color 0.3s;
  z-index: 9999;
}

.settings-button:hover {
  background-color: #00a5c2;
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

/* .settings-page .close-button {
  background-color: red;
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 18px;
  color: #fff;
  cursor: pointer;
} */

.search-container {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}
/* Search input */
.search-input {
  flex: 1;
  margin-right: 10px;
  padding: 16px 235px 16px 32px; 
  border: none;
  border-radius: 5px;
  font-size: 28px;
  width:100%;
  max-width: 800px; /* Limit the width for larger screens */
}

body.dark-mode .search-input {
  background-color: #525151;
  color: #fff;
}

@media (max-width: 768px) {
  .search-input {
    font-size: 20px;
    padding: 16px 195px 16px 24px;
  }
}

@media (min-width: 1024px) {
  .search-input {
    font-size: 20px;
    padding: 27px 310px 16px 34px;
  }
}
/* Search button */
.search-button {
  height: 70px;
  padding: 15px 25px;
  background-color: #2196F3;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-size: 34px;
  color: #fff;
  position: absolute;
  right: -1px;
  top:1px;
}
.search-button:hover {
  background-color: #2a1e6ee8;
}
@media (max-width: 768px) {
  .search-button {
    padding: 15px 20px;
    height: 60px;
    font-size: 18px;
    right:15px;
  }
}
/* Close button */
.close-button {
  position: absolute;
  top: 35px;
  right: 115px;
  transform: translateY(-50%);
  padding: 5px;
  color: red;
  font-size: 60px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .close-button {
    top: 30px;
    right: 90px; /* Adjust the position for smaller screens */
  }
}

.add-group-form {
  display: none;
}
/* search table style */
.table {
  border-collapse: collapse;
  margin: 25px auto; 
  font-size: 18px;
  font-family: sans-serif;
  max-width: 800px; 
  width: 90%;
box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table.dark-mode {
background-color: #e1dbd6;
color: #ffffff;
}

.table td {
background-color:#506680;
color:#000000;
}

.table.dark-mode td {
background-color: #152642 ;
color: #ffffff;
}

.table th {
background-color: #0c1d42;
color: #ffffff;
font-weight: bold;
}

.table.dark-mode th {
background-color: #061148;
color: #ffffff;
font-weight: bold;
}

/* group table style */
.styled-table {
  border-collapse: collapse;
  margin: 25px auto; 
  font-size: 18px;
  font-family: sans-serif;
  max-width: 800px; 
  width: 90%;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
  
}
.styled-table thead tr {
  background-color: #009879;
  color: #ffffff;
  text-align: left;
}

.styled-table.dark-mode thead tr {
  background-color: #061148;
  color: #ffffff;
}

.styled-table th,
.styled-table td {
  padding: 12px 15px;
}

.styled-table td {
  padding: 12px 0;
  position: relative;
}

.styled-table tbody tr {
  border-bottom: 1px solid #dddddd;
}

.styled-table tbody tr:nth-of-type(even) {
  background-color: #f3f3f3;
}

.styled-table.dark-mode tbody tr:nth-of-type(even) {
  background-color: #152642;
}

.styled-table tbody tr:nth-of-type(odd) {
  background-color: #ffffff;
}

.styled-table.dark-mode tbody tr:nth-of-type(odd) {
  background-color: #2F4562;
}

.styled-table tbody tr:last-of-type {
  border-bottom: 2px solid #009879;
}
.styled-table.dark-mode tbody tr:last-of-type {
  border-bottom: 2px solid #040e31;
}

.styled-table tbody tr.active-row {
  font-weight: bold;
  color: #009879;
}

.styled-table.dark-mode tbody tr.active-row {
  color: #ffffff;
}



.custom-link {
  text-decoration: none;
  color: black;
  font-size: 20px;
}
a{
  text-decoration: none;
}


.btn-red,
.btn-blue {
  padding: 11px 15px;
  font-size: 18px;
  border-radius: 10px;
  text-align: center;
  width: 100px; 
  /* display: flex; */
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
/* .btn-blue{
  padding: 11px 15px;
} */
@media (max-width: 768px) {
  .btn-red,
  .btn-blue {
    width: 100px; 
  }
}
@media (max-width: 480px) {
  .styled-table td {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .styled-table td .btn-red,
  .styled-table td .btn-blue {
    margin-top: 10px; 
  }
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

.btn-red {
  background-color: red;
  color: white;
  border: 1px solid #d30000;
 
}

.btn-red:hover {
  background-color: darkred;
}
.btn-blue {
  background: linear-gradient(to right, #4f9de1, #2654b8);
  color: white;
  border: 1px solid #007bff;
}.btn-blue:hover {
  background: linear-gradient(to right, #2654b8, #4f9de1);
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


button.return-button {
  position: absolute;
  top: 20px;
  left: 5px;
  padding: 20px 35px;
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

/* Media queries for responsive buttons */
@media (max-width: 768px) {
  .settings-button {
    font-size: 20px;
    padding: 15px 25px;
  }

  button.return-button {
    font-size: 18px;
    padding: 15px 25px;
  }
}

@media (max-width: 480px) {
  .settings-button {
    font-size: 16px;
    padding: 12px 20px;
  }

  button.return-button {
    font-size: 16px;
    padding: 12px 20px;
  }
}
/* completed the project */
    </style>
</head>


<body id="body">
  <header>
    <nav >
      <div class="container">
        <ul>
          <li><a class="active" href="">Groups</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="about.php">About</a></li>
          <li style="float:left"><button class="logout-button"
              onclick="window.location.href = 'index.php?logout=true'">Log out</button></li>
          <li class="dropdown" style="float:right">
            <button class="settings-button" class="settings-btn" onclick="openSettings()"><i class="fas fa-cog"></i>
            </button>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <main>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <div class="headname">
              <img src="image/mobilelogo77.png" alt="project Logo">
              <h1><em>Mobile-store </em></h1>
            </div>
            <hr>
    
            <div class="search-container">
      <form method="GET">
        <input type="text" id="myInput" class="form-control search-input" placeholder="Search the products" name="search" required/>
        <span class="close-button" onclick="clearSearch()">&times;</span>
        <button type="button" class="btn btn-primary search-button" onclick="searchFun()">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>
<table class="table" id="mytable" style="display:none;">
  <thead class="thead-dark">
    <tr>
      <th colspan="3">Suggestions.!</th>
<th> <button class="btn-danger" onclick="clearSearch()">&times;</button></th>

</tr>
    <tr>
      <th>No</th>
      <th>Product Name</th>
      <th>Group Name</th>
      <th>Details</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (!empty($products)) {
      foreach ($products as $product) {
        echo "<tr>
                <td>" . $product["id"] . "</td>
                <td>" . $product["product_name"] . "</td>
                <td>" . $product["groupname"] . "</td>
                <td><a href='detail.php?product_id=" . urlencode($product["id"]) . "&product_name=" . urlencode($product["product_name"]) . "' class='custom-link'>
                <button class='btn btn-primary show-button'>Show</button></a></td>
              </tr>";
      }
    } else {
      
      echo "<tr>
              <td colspan='4'>No results found.</td>
            </tr>";
    }
    ?>
  </tbody>
</table>

            <div class="add-group-form" style="display: none;">
              <!-- Corrected the form action to include the userid parameter -->
              <form method="POST" action="group.php?userid=<?php echo $userId; ?>">
                <input type="hidden" name="userid" value=" <?php echo $userId; ?>">
                <!-- echo $_GET['gn']; -->
                <input type="text" name="groupname" placeholder="Group Name" required class="form-control"><br>
               
                <input type="text" name="fun" id="motivation" class="form-control" disabled><br>

                <!-- Changed the name of the input field to "groupname" -->
                <input type="submit" class="btn btn-primary" value="Create">&nbsp;
                <button onclick="backspace()" class="btn btn-secondary">Back</button>
              </form>
            </div>
            <button onclick="showAddGroupForm()" class="btn btn-primary add-group">+ Add Group</button>
            <br>
            
<h3 class="mb-3">꧁𓊈𒆜🆄🆂🅴🆁 🅸🅳 -<?php echo $userId; ?>𒆜𓊉꧂</h3>
<div class="container">
   <table class='styled-table'>
            <thead>
              <tr>
                <th>No:</th>
                <th>Group Name</th>
                <th>&nbsp;&nbsp;Action</th>
                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kill</th>
              </tr>
            </thead>
            
<tbody>
  <?php
  if (count($groups) > 0) {
    // echo "User belongs to the following groups:<br>"; // Move this line inside the if statement
    // Display the table header

    // Loop through each group and display the table rows
    foreach ($groups as $group) {
      echo "<tr class='active-row'>
              <td>" . $group['id'] . "</td>
              <td>" . $group['groupname'] . "</td>
              <td><a href='product.php?groupid=" . urlencode($group['id']) . "&groupname=". urlencode($group['groupname']) . "&userid=" . urlencode($userId) . "' class='custom-link'><button class='btn-blue'>Open</button></a></td>
              <td><a href='group.php?userid=" . urlencode($userId) . "&gn=" . urlencode($group['groupname']) . "&delete=true' onclick='return confirm(\"Are you sure you want to delete this group?\");' class='btn-red'>Delete</a></td>
            </tr>";
    }

    echo "</tbody></table>";
  } else {
    echo "<tr> <td colspan=\"4\"> User does not belong to any group.</td></tr>";
  }
  ?>
</tbody>
            </div>
            <?php
            if (isset($message)) {
              echo "<center><p>" . $message . "</p></center>";
            }

            if (isset($deleteMessage)) {
              echo "<center><p>" . $deleteMessage . "</p></center>";
            }
            ?>
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
  </main>
  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- completed the project -->

  <script>
  // Function to handle the search functionality
function searchFun() {
  let filter = document.getElementById('myInput').value.trim().toUpperCase();
  let userId = <?php echo json_encode($userId); ?>;
  let currentSearch = new URLSearchParams(location.search).get('search');

  // Update the search query parameter based on the user's input
  let newSearch = '';
  if (filter.length > 0) {
    newSearch = `?userid=${userId}&search=${encodeURIComponent(filter)}`;
  } else {
    newSearch = `?userid=${userId}`;
  }

  // Store the search query in localStorage only if the filter is not empty
  if (filter.length > 0) {
    localStorage.setItem('searchQuery', filter);
  }

  // Reload the page with the updated search query
  location.href = 'group.php' + newSearch;
}

// Function to show or hide the table based on the stored search query
function showTableIfResults() {
  let searchQuery = localStorage.getItem('searchQuery');
  let myInput = document.getElementById('myInput');
  let mytable = document.getElementById('mytable');

  // Check if there is a stored search query
  if (searchQuery) {
    myInput.value = searchQuery; // Set the input value from localStorage
    mytable.style.display = "table"; // Show the table
  } else {
    mytable.style.display = "none"; // Hide the table
  }
}

// Hide the table when the page loads
window.onload = function() {
  showTableIfResults();
};
function clearSearch() {
  var input = document.getElementById('myInput');
  input.value = ''; // Clear the input value

  // Hide the table
  let mytable = document.getElementById('mytable');
  mytable.style.display = "none";

  // Clear the search query from localStorage
  localStorage.removeItem('searchQuery');
}


  
// Array of 10 motivational messages
const motivationalMessages = [
  "Believe in yourself and your dreams! 🌟",
  "You are capable of great things! 💪",
  "Every small step brings you closer to success! 🚀",
  "Stay positive and keep moving forward! 🏃‍♂️",
  "Your efforts will pay off - keep going! 💯",
  "Challenges make you stronger - embrace them! 🔥",
  "You have the power to make a difference! ✨",
  "Success is a journey - enjoy it! 🌈",
  "Your potential is limitless - unleash it! 🚀",
  "The only limit is the one you set for yourself! 🌟"
];

function getRandomMessage() {
  // Get the current time and use it to generate an index for the motivationalMessages array
  const currentTime = new Date();
  const currentHour = currentTime.getHours();
  const index = currentHour % 10; // Ensure the index stays within the array length

  return motivationalMessages[index];
}

// Function to update the input field with a new motivational message
function updateMotivation() {
  const motivationInput = document.getElementById("motivation");
  const randomMessage = getRandomMessage();

  motivationInput.value = randomMessage;
}

// Call the updateMotivation function to set the initial motivational message when the page loads
updateMotivation();

// Update the motivational message every 30 minutes (adjust as needed)
setInterval(updateMotivation, 1 * 60 * 1000); // 30 minutes in milliseconds

</script>

</body>

</html>