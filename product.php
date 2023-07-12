<?php
session_start();

error_reporting(0);

$message = "";
$deleteMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_name = $_POST['product_name'];
  $groupname = $_POST['groupname'];

  if (!empty($product_name) && !empty($groupname)) {
    $servername = "mysql_db";
    $username = "root";
    $password = "root";
    $database = "ashii";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if (mysqli_connect_error()) {
      die('Connect Error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error());
    } else {
      $SELECT = "SELECT product_name FROM device WHERE product_name = ? LIMIT 1";
      $INSERT = "INSERT INTO device (product_name, groupid) VALUES (?, (SELECT id FROM `groups` WHERE groupname = ?))";

      // Prepare statement
      $stmt = $conn->prepare($SELECT);
      $stmt->bind_param("s", $product_name);
      $stmt->execute();
      $stmt->store_result();
      $rnum = $stmt->num_rows;

      if ($rnum == 0) {
        $stmt->close();

        $stmt = $conn->prepare($INSERT);
        $stmt->bind_param("ss", $product_name, $groupname);
        $stmt->execute();

        $message = "New product added successfully";
        $_SESSION['message'] = $message;
      } else {
        $message = "Someone has already added a product with this name";
        $_SESSION['message'] = $message;
      }
      $stmt->close();
    }
    
  } else {
    $message = "All fields are required";
    $_SESSION['message'] = $message;
  }
}



$product_name = isset($_GET['pn']) ? $_GET['pn'] : null;

if (!empty($product_name)) {
  $groupid = $_GET['groupid'];

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
}

?>
<!DOCTYPE html>
<html>

<head>
  <title>Product</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">

  <style>
    body {
      font-size: 30px;
      background-color: #91ec7e;
      transition: background-color 0.3s, color 0.3s;
      margin: 0;
      padding: 0;
    }

    body.dark-mode {
      background-color: #212121;
      color: #ffffff;
    }

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
      background-color: white;
      -webkit-transition: .4s;
      transition: .3s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: #2196F3;
    }

    input:focus+.slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
      -webkit-transform: translateX(45px);
      -ms-transform: translateX(45px);
      transform: translateX(45px);
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
      background-color: #171717;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(255, 255, 255, 0.1);
    }

    .add-group {
      background-color: #2196F3;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 20px 30px;
      font-size: 24px;
      cursor: cell;
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
      /* Increase the font size for the input field, create button, and back button */
      padding: 12px 20px;
      /* Increase the padding for the input field, create button, and back button */
      margin-bottom: 10px;
      /* Add some spacing between the elements */
      width: 300px;
      /* Adjust the width value as per your requirement */
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
      right: 120px;
      transform: translateY(-50%);
      cursor: pointer;
      background: red;
      padding: 1px;
      color: white;
      font-size: 40px;
    }

    .search-button {
      height: 80px;
      padding: 20px 40px;
      background-color: #2196F3;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 32px;
      color: #fff;
    }

    .search-button:hover {
      background-color: #0077C2;
    }

    .add-group-form {
      display: none;
    }

    .show-add-group-form .add-group-form {
      display: block;
    }

    .styled-table {
  border-collapse: separate;
  border-spacing: 0;
  margin: 25px 0;
  font-size: 0.9em;
  font-family: sans-serif;
  min-width: 700px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
}

.styled-table.dark-mode {
  background-color: #171717;
  color: #ffffff;
  box-shadow: 0 0 20px rgba(255, 255, 255, 0.15);
}

.styled-table thead tr {
  background-color: #0c784b;
  color: #ffffff;
  text-align: left;
}

.styled-table.dark-mode thead tr {
  background-color: #061148;
}

.styled-table th,
.styled-table td {
  padding: 12px 15px;
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

.btn {
  padding: 10px 30px;
  font-size: 24px;
}

.btn-primary {
  background-color: #2196F3;
  color: #fff;
}

.btn-secondary {
  background-color: #6c757d;
  color: #fff;
}

.btn-red {
  background-color: red;
  color: white;
  padding: 12px 24px;
  font-size: 18px;
  border-radius: 6px;
}

.btn-red:hover {
  background-color: darkred;
}

.btn-blue {
  background-color: blue;
  color: white;
  padding: 12px 24px;
  font-size: 18px;
  border-radius: 6px;
}

    h3,
    h2 {
      font-size: 32px;
    }
  </style>
  <script>
    function toggleDarkMode() {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table = document.querySelector('.styled-table');
  var isDarkMode = localStorage.getItem('darkMode');

  body.classList.toggle('dark-mode');
  
  // Update card and table class based on dark mode
  if (body.classList.contains('dark-mode')) {
    card.classList.add('dark-mode');
    table.classList.add('dark-mode');
    localStorage.setItem('darkMode', 'true');
  } else {
    card.classList.remove('dark-mode');
    table.classList.remove('dark-mode');
    localStorage.setItem('darkMode', 'false');
  }
}

// Retrieve the dark mode preference from localStorage and apply the dark mode on page load
document.addEventListener('DOMContentLoaded', function () {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table = document.querySelector('.styled-table');
  var isDarkMode = localStorage.getItem('darkMode');

  if (isDarkMode === 'true') {
    body.classList.add('dark-mode');
    card.classList.add('dark-mode');
    table.classList.add('dark-mode');
  }
});

const searchFun = () => {
      let filter = document.getElementById('myInput').value.toUpperCase();
      let mytable = document.getElementById('mytable');
      let tr = mytable.getElementsByTagName('tr');

      for (var i = 0; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td')[1];

        if (td) {
          let textvalue = td.textContent || td.innerHTML;

          if (textvalue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
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
    }

    function showAddproductForm() {
      var addGroupForm = document.querySelector('.add-group-form');
      addGroupForm.style.display = 'block';
    }

    function backspace() {
      var addGroupForm = document.querySelector('.add-group-form');
      addGroupForm.style.display = 'none';
    }

    function confirmRemove() {
      return confirm("Are you sure you want to remove this product?");
    }
  </script>
</head>

<body>

  <label class="switch">
    <input type="checkbox" onclick="toggleDarkMode()">
    <span class="slider"></span>
  </label>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <a href="group.php" class="btn btn-secondary">Groups</a>
          <br>
          <h2>Welcome to the Product Page</h2>
          <hr>
          <div class="search-container">
            <input type="text" id="myInput" class="form-control search-input" placeholder="Search"
              onkeyup="searchFun()">
            <span class="close-button" onclick="clearSearch()">&times;</span>
            <button onclick="searchFun()" class="btn btn-primary search-button">
              <i class="fas fa-search"></i>
            </button>
          </div>

          <div class="add-group-form">
            <form method="POST" action="">
              <input type="text" name="product_name" placeholder="Product Name" required class="form-control"><br>
              <input type="text" name="groupname" placeholder="Group Name" required class="form-control"><br>
              <input type="submit" class="btn btn-primary" value="Create">&nbsp;
              <button onclick="backspace()" class="btn btn-secondary">Back</button>
            </form>
          </div>

          <button onclick="showAddproductForm()" class="btn btn-primary add-group">+ Add products</button>
          <br>
          <h3>Group:
            <?php echo $groupname; ?>
          </h3>
          <table class="styled-table" id="mytable">
            <thead class="thead-dark">
              <tr>
                <th>No</th>
                <th>Product Name</th>
                <th>Group Name</th>
                <th>Remove</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $product_name = isset($_GET['product_name']) ? $_GET['product_name'] : null;
              if (isset($products)) {
                if (!empty($products)) {
                  $counter = 1;
                  foreach ($products as $row) {
                    echo "<tr class=\"active-row\">
      <td>" . $counter . "</td>
      <td>" . $row["product_name"] . "</td>
      <td>" . $row["groupname"] . "</td>
      <td>
      <a href='product.php?groupid=" . urlencode($groupid) . "&pn=" . urlencode($row["product_name"]) . "' onclick='return confirmRemove();'>
        <button class='btn btn-red search-button'>Remove</button>
      </a>
    </td>
    
      </tr>";

                    $counter++;
                  }
                } else {
                  echo "<tr><td colspan='4'>No products found for this group</td></tr>";
                }
              } else {
                echo "<tr><td colspan='4'>No products found</td></tr>";
              }
              ?>

            </tbody>
          </table>
          <?php if (!empty($message)): ?>
            <center>
              <p>
                <?php echo $message; ?>
              </p>
            </center>
          <?php endif; ?>

          <?php if (!empty($deleteMessage)): ?>
            <center>
              <p>
                <?php echo $deleteMessage; ?>
              </p>
            </center>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>