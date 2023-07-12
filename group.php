<?php
session_start();
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $groupname = $_POST['groupname'];

  if (!empty($groupname)) {
    $servername = "mysql_db";
    $username = "root";
    $password = "root";
    $database = "ashii";

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
  $servername = "mysql_db";
  $username = "root";
  $password = "root";
  $database = "ashii";

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
$servername = "mysql_db";
$username = "root";
$password = "root";
$database = "ashii";

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
  <title>Group</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
  <style>
    body {
      font-size: 20px;
      background-color: #afd2ee;
      transition: background-color 0.3s, color 0.3s;
      margin: 0;
      padding: 0;
    }

    body.dark-mode {
      background-color: #212124;
      color: #ffffff;
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
      top: 20px;
      right: 5px;
      background-color: #2196F3;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 20px 30px;
      font-size: 20px;
      cursor: pointer;
      transition: background-color 0.3s;
      z-index: 9999;
    }

    .settings-button:hover {
      background-color: #0077C2;
    }

    .card {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      padding: 40px;
      text-align: center;
      /* width: 800px; */
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
      font-size: 24px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 20px;
    }

    .add-group:hover {
      background-color: #0077C2;
    }

    .add-group-form {
      display: none;
      text-align: center;
      padding: 30px;
    }

    .add-group-form input[type="text"],
    .add-group-form input[type="submit"],
    .add-group-form button {
      font-size: 24px;
      padding: 12px 24px;
      margin-bottom: 10px;
      width: 400px;
    }

    /* Media queries for different screen sizes */
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
        padding: 25px;
      }
    }

    @media (min-width: 1025px) {
      .card {
        padding: 40px;
      }
    }

    .settings-page {
      position: absolute;
      top: 60px;
      right: 20px;
      background-color: rgba(0, 0, 0, 0.8);
      color: #fff;
      border-radius: 8px;
      padding: 100px;
      text-align: center;
      z-index: 9999;
    }

    .settings-page h2 {
      margin-top: 0;
      font-size: 40px;
    }

    .settings-page ul {
      list-style-type: none;
      padding: 0;
      font-size: 30px;
    }

    .settings-page li {
      margin-bottom: 30px;
      display: block;
    }

    .settings-page li input[type="checkbox"] {
      margin-right: 15px;
      transform: scale(2.5);
    }

    .settings-page button {
      background-color: #2196F3;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 15px 30px;
      font-size: 24px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 20px;
    }

    .settings-page button:hover {
      background-color: #0077C2;
    }

    .settings-page .close-button {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 18px;
      color: #fff;
      cursor: pointer;
    }

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

    .close-button {
      position: absolute;
      top: 50%;
      right: 90px;
      transform: translateY(-50%);
      cursor: pointer;
      padding: 5px;
      color: red;
      font-size: 50px;
    }

    .search-button {
      height: 60px;
      padding: 15px 30px;
      background-color: #2196F3;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 24px;
      color: #fff;
    }

    .search-button:hover {
      background-color: #0077C2;
    }

    .add-group-form {
      display: none;
    }
  /* search table style */
.table {
  border-collapse: collapse;
  margin: 25px 0;
  font-size: 0.9em;
  font-family: sans-serif;
  min-width: 700px;
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
  background-color: #061148;
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
  margin: 25px 0;
  font-size: 0.9em;
  font-family: sans-serif;
  width: 100%;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
  table-layout: fixed;
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
  border-bottom: thin solid #dddddd;
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

.btn-red {
  background-color: red;
  color: white;
  padding: 12px 22px;
  font-size: 21px;
  border-radius: 6px;
  position: absolute;
  top: 12px;
  transform: translateY(-50%);
  left: 70px;
  transform: translateX(-50%);
}

.btn-red:hover {
  background-color: darkred;
}

.btn-blue {
  background-color: blue;
  color: white;
  padding: 12px 24px;
  font-size: 24px;
  border-radius: 6px;
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
  </style>
  <script>
    function openSettings() {
      var settingsPage = document.querySelector('.settings-page');
      settingsPage.style.display = 'block';
    }

    function closeSettings() {
      var settingsPage = document.querySelector('.settings-page');
      settingsPage.style.display = 'none';
    }

    function toggleDarkMode() {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table1 = document.querySelector('.table');
  var table2 = document.querySelector('.styled-table');
  body.classList.toggle('dark-mode');

  // Update classes based on dark mode
  if (body.classList.contains('dark-mode')) {
    card.classList.add('dark-mode');
    table1.classList.add('dark-mode');
    table2.classList.add('dark-mode');
    localStorage.setItem('darkMode', 'true');
  } else {
    card.classList.remove('dark-mode');
    table1.classList.remove('dark-mode');
    table2.classList.remove('dark-mode');
    localStorage.setItem('darkMode', 'false');
  }
}

// Retrieve the dark mode preference from localStorage and apply the dark mode on page load
document.addEventListener('DOMContentLoaded', function () {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table1 = document.querySelector('.table');
  var table2 = document.querySelector('.styled-table');
  var darkMode = localStorage.getItem('darkMode');

  if (darkMode === 'true') {
    body.classList.add('dark-mode');
    card.classList.add('dark-mode');
    table1.classList.add('dark-mode');
    table2.classList.add('dark-mode');
  }
});


    function searchFun() {
      let filter = document.getElementById('myInput').value.toUpperCase();
      let mytable = document.getElementById('mytable');
      let tr = mytable.getElementsByTagName('tr');

      for (var i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td')[1];

        if (td) {
          let textvalue = td.textContent || td.innerText;

          if (textvalue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }

      // Display product table if filter is not empty
      if (filter.length > 0) {
        mytable.style.display = "table";
      } else {
        mytable.style.display = "none";
      }
    }

    function clearSearch() {
      var input = document.getElementById('myInput');
      var tr = document.getElementsByTagName('tr');
      input.value = ''; // Clear the input value

      // Display all table rows
      for (var i = 0; i < tr.length; i++) {
        tr[i].style.display = "";
      }

      // Hide the product table
      document.getElementById('mytable').style.display = "none";
    }

    function showAddGroupForm() {
      var addGroupForm = document.querySelector('.add-group-form');
      addGroupForm.style.display = 'block';
    }

    function backspace() {
      var addGroupForm = document.querySelector('.add-group-form');
      addGroupForm.style.display = 'none';
    }

    function confirmRemove() {
      return confirm("Are you sure you want to remove this group?");
    }
    function open() {

    }
  </script>
</head>

<body>
  <button class="settings-button" onclick="openSettings()">Settings</button>
  <button class="return-button" onclick="window.location.href = 'index.html'">Back</button>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <h2><em>Welcome To My Data</em></h2>
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
                <th colspan="3">suggestions !</th>
              </tr>
              <tr>
                <th>No</th>
                <th>Product Name</th>
                <th>Group Name</th>
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

          <h3><i>My Data Groups</i></h3>
          <div class="container">
            <table class="styled-table"  >
              <thead >
                <tr>
                  <th>No:</th>
                  <th>Group Name</th>
                  <th>Action</th>
                  <th>Kill</th>
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
           <td><a href='group.php?gn=" . urlencode($group) . "' onclick='return confirmRemove();' class='btn btn-red search-button'>Remove</a></td>

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
          <input type="checkbox" name="darkMode" onclick="toggleDarkMode()"> Dark Mode
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
</body>

</html>