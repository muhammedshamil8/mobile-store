<?php
// group.php
// include 'groupphp.php';

session_start();
error_reporting(E_ALL);
ini_set('display_startup_errors',1);
ini_set('display_errors',1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $groupname = $_POST['groupname'];

  if (!empty($groupname)) {
     include 'console.php'; 

    // create connection
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // check connection
    if (mysqli_connect_error()) {
      die('Connect Error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error());
    } else {
      $SELECT = "SELECT groupname FROM `groups` WHERE groupname = ? LIMIT 1";
      $INSERT = "INSERT Into `groups` (groupname) values(?)";


      // prepare statement
      $stmt = $conn->prepare($SELECT);
      $stmt->bind_param("s", $groupname);
      $stmt->execute();
      $stmt->bind_result($groupname);
      $stmt->store_result();
      $rnum = $stmt->num_rows;

      if ($rnum == 0) {
        $stmt->close();

        $stmt = $conn->prepare($INSERT);
        $stmt->bind_param("s", $groupname);
        $stmt->execute();
        $message = "New group created successfully";
      } else {
        $message = "Someone has already created a group with this name";
      }
      $stmt->close();
      $conn->close();
    }
  } else {
    echo "All fields are required";
  }
}
$groupname = $_GET['gn'];

if (!empty($groupname)) {
   include 'console.php'; 

  // Create connection
  $conn = new mysqli($servername, $username, $password, $database);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $groupQuery = "DELETE FROM `groups` WHERE groupname = ?";
  $stmt = $conn->prepare($groupQuery);
  $stmt->bind_param("s", $groupname);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    $deleteMessage = "Group deleted from the database";
  } else {
    $deleteMessage = "Failed to delete group from the database";
  }

  $stmt->close();
  $conn->close();
}


// Retrieve all groups
 include 'console.php'; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `groups`";
$result = $conn->query($sql);

$groupNames = array();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $groupNames[$row["id"]] = $row["groupname"];
  }
}

$conn->close();

$product_name = $_GET['search'];
$productQuery = "SELECT device.id, device.product_name, `groups`.groupname FROM device, `groups` WHERE device.groupid = `groups`.id";

if (!empty($product_name)) {
  $productQuery .= " AND device.product_name LIKE '%$product_name%'";
}

$productQuery .= " ORDER BY device.id";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare($productQuery);
$stmt->execute();
$productResult = $stmt->get_result();
$products = $productResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();


?>