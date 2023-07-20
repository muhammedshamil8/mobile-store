<?php 
session_start();
error_reporting(0);

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


<!DOCTYPE html>
<html>

<head>
  <title>Moving Bird</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">

  <link rel="stylesheet" type="text/css" href="page1.css">
  <script src="page1.js">
  </script>
</head>

<body>
  <button class="settings-button" class="settings-btn" onclick="openSettings()"><i class="fas fa-cog"></i> </button>
  <button class="return-button" onclick="window.location.href = 'index.html'" class="back-btn">
  <i class="fas fa-chevron-left"></i></button>
  



  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <h2><em>Welcome To "Moving Bird"</em></h2>
          <hr>
          <div class="search-container">
            <input type="text" id="myInput" class="form-control search-input" placeholder="Search">
            <span class="close-button" onclick="clearSearch()">&times;</span>
            <button onclick="searchFun()" class="btn btn-primary search-button">
              <i class="fas fa-search"></i>
            </button>
          </div>

          <table class="table" id="mytable" style="display: none;">
            <thead class="thead-dark">
              <tr>
                <th colspan="4">suggestions !</th>
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
                <button class='btn btn-primary search-button'>show</button></a></td>                
              </tr>";
                }
              }
              ?>

            </tbody>
          </table>



          <div class="add-group-form" style="display: none;">
            <form method="POST" action="">
              <input type="text" name="groupname" placeholder="Group Name" required class="form-control"><br>
              <input type="submit" class="btn btn-primary" value="Create">&nbsp;
              <button onclick="backspace()" class="btn btn-secondary">Back</button>
            </form>
          </div>

          <button onclick="showAddGroupForm()" class="btn btn-primary add-group">+ Add Group</button>
          <br>

          <h3><i>Moving Bird 'groups'</i></h3>
          <div class="container">
            <table class="styled-table">
              <thead>
                <tr>
                  <th>No:</th>
                  <th>Group Name</th>
                  <th>&nbsp;&nbsp;Action</th>
                  <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Kill</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($groupNames as $id => $group) {
                  echo "<tr class=\"active-row\">
          <td><a href='product.php?groupid=" . urlencode($id) . "&groupname=" . urlencode($group) . "' class='custom-link'>" . $id . "</a></td>
          <td><a href='product.php?groupid=" . urlencode($id) . "&groupname=" . urlencode($group) . "' class='custom-link'>" . $group . "</a></td>
          <td><a href='product.php?groupid=" . urlencode($id) . "&groupname=" . urlencode($group) . "' class='custom-link'>
          <button class='btn btn-primary search-button'>Open</button></a></td>
           <td><a href='group.php?gn=" . urlencode($group) . "' onclick='return confirmRemove();' class='btn btn-red search-button'>Delete</a></td>

                       </tr>";
                }
                ?>


              </tbody>
            </table>


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
    <!-- <span class="close-button" onclick="closeSettings()">&times;</span> -->
    <h2>Settings</h2>
    <ul>
      <li>
        <label>
          <input type="checkbox" name="darkMode" onclick="toggleDarkMode()" checked> Dark Mode
        </label>
      </li>
      <li>
        <label>
          <input type="checkbox" name="notifications"> Notifications
        </label>
      </li>
      <li>
        <label>
          <input type="checkbox" name="privacy"> Privacy
        </label>
      </li>
    </ul>
    <button onclick="closeSettings()" class="btn btn-secondary">Close</button>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- completed the project -->
</body>

</html>