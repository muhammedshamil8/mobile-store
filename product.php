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
  <title>Moving Bird</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
  <link rel="stylesheet" href="page2.css">
  <script>
function toggleDarkMode() {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table = document.querySelector('.styled-table');
  var isDarkMode = body.classList.toggle('dark-mode');

  // Update card and table class based on dark mode
  card.classList.toggle('dark-mode', isDarkMode);
  table.classList.toggle('dark-mode', isDarkMode);

  // Store dark mode preference
  localStorage.setItem('darkMode', isDarkMode ? 'true' : 'false');
}

document.addEventListener('DOMContentLoaded', function () {
  var body = document.querySelector('body');
  var card = document.querySelector('.card');
  var table = document.querySelector('.styled-table');

  // Retrieve the dark mode preference from localStorage
  var isDarkMode = localStorage.getItem('darkMode');

  // Set the initial state of dark mode based on the stored preference
  if (isDarkMode === 'true') {
    body.classList.add('dark-mode');
    card.classList.add('dark-mode');
    table.classList.add('dark-mode');
  }

  // Update dark mode toggle button state
  var darkModeCheckbox = document.querySelector('input[name="darkMode"]');
  darkModeCheckbox.checked = isDarkMode === 'true';

  // Attach event listener to toggle button
  darkModeCheckbox.addEventListener('change', toggleDarkMode);
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
  <input type="checkbox" name="darkMode" checked>
  <span class="slider"></span>
</label>


  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <a href="group.php" class="btn btn-secondary">Groups</a>
          <br>
          <h2>Welcome to Moving Bird 'Products '</h2>
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
                <th>Action</th>
                <th>Delete</th>
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
      <td><a href='page.html' class='custom-link'>
          <button class='btn btn-primary search-button'>Click</button></a></td>
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