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

  <link rel="stylesheet" type="text/css" href="page1.css">
  <script src="page1.js">
  </script>
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
        <input type="text" id="myInput" class="form-control search-input" placeholder="Search the products" name="search" />
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

  // Reload the page with the updated search query
  location.href = 'group.php' + newSearch;
}

// // Unhide the table when there are search results
function showTableIfResults() {
  let mytable = document.getElementById('mytable');
  if (mytable.rows.length > 0) {
    mytable.style.display = "table";
  } else {
    mytable.style.display = "none";
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
}

function clearSearch() {
  var input = document.getElementById('myInput');
  input.value = ''; // Clear the input value

  // Hide the table
  let mytable = document.getElementById('mytable');
  mytable.style.display = "none";
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