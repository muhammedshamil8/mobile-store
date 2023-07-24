<?php
session_start();

error_reporting(0);

$message = "";
$deleteMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $groupname = $_POST['groupname'];

    if (!empty($product_name) && !empty($groupname)) {
         include 'console.php'; 

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

     include 'console.php'; 

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
     include 'console.php'; 

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
     include 'console.php'; 

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

     include 'console.php'; 

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
<title>Mobile-store</title>
  <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
    <link rel="stylesheet" href="page2.css">
    <script src="page2.js"></script>
</head>
<body id="body">

    <label class="switch">
        <input type="checkbox" name="darkMode" checked>
        <span class="slider"></span>
    </label>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
        <div class="headname">
                <img src="image/mobilelogo77.png" alt="project Logo"> <h1><em>Mobile-store </em></h1> </div>
           
                    <a href="group.php" class="btn btn-secondary">Groups</a><br>
                    
                    <div class="search-container mb-4">
                        <input type="text" id="myInput" class="form-control search-input" placeholder="Search" onkeyup="searchFun()">
                        <span class="close-button" onclick="clearSearch()">&times;</span>
                        <button onclick="searchFun()" class="btn btn-primary search-button"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="add-group-form">
                        <form method="POST" action="">
                            <input type="text" name="product_name" placeholder="Product Name" required class="form-control mb-2">
                            <input type="text" name="groupname" placeholder="Group Name" required class="form-control mb-2">
                            <div class="d-flex justify-content-between">
                                <input type="submit" class="btn btn-primary" value="Create">
                                <button onclick="backspace()" class="btn btn-secondary">Back</button>
                            </div>
                        </form>
                    </div>

                    <button onclick="showAddproductForm()" class="btn btn-primary add-group mb-3">+ Add Products</button>
                    <h3 class="mb-3">Group: <?php echo $groupname; ?></h3>
                    <div class="table-container">
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
                            <?php if (isset($products) && !empty($products)): ?>
                                <?php $counter = 1; ?>
                                <?php foreach ($products as $row): ?>
                                    <tr class="active-row">
                                        <td><?php echo $counter; ?></td>
                                        <td><?php echo $row["product_name"]; ?></td>
                                        <td><?php echo $row["groupname"]; ?></td>
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
                                <tr><td colspan='5'>No products found for this group</td></tr>
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

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- completed the project -->
</body>
</html>
