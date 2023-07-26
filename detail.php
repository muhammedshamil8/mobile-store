<?php
session_start();
ini_set('upload_max_filesize', '30M'); // Set maximum upload file size to 30MB
ini_set('post_max_size', '30M'); // Set maximum POST data size to 30MB

include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page or display an error message
    header("Location: index.php?error=Please log in first");
    exit();
  }
  
$userId = isset($_GET['userid']) ? $_GET['userid'] : null;
$groupId = isset($productDetails['groupid']) ? $productDetails['groupid'] : null;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = $_GET['product_id'];
// Retrieve details from the device, groups, and upload tables based on product_id
$productQuery = "SELECT device.id, device.product_name,device.groupid, `groups`.groupname, `groups`.userid
                 FROM device
                 INNER JOIN `groups` ON device.groupid = `groups`.id
                 WHERE device.id = ?";

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

// Check if the upload form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload'])) {
        // Upload image
        $image = $_FILES['image'];

        // Check if the image is not empty and there are no errors
        if (!empty($image['name']) && $image['error'] === 0) {
            // Check file size
            $maxFileSize = 30 * 1024 * 1024; // 30MB

            if ($image['size'] > $maxFileSize) {
                $errorMsg = "File size exceeds the maximum limit (30MB).";
            } elseif (!in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
                $errorMsg = "Only JPG, JPEG, and PNG files are allowed.";
            } else {
                // Upload the image file
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($image['name']);

                if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                    // Insert the upload details into the upload table
                    $insertQuery = "INSERT INTO `upload` (product_id, image, heading, description) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bind_param("isss", $product_id, $image['name'], $_POST['heading'], $_POST['description']);
                    $stmt->execute();
                    $stmt->close();
                    // Redirect to the same page to prevent duplicate form submission
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                } else {
                    // Handle file upload error
                    $errorMsg = "File upload failed.";
                }
            }
        } else {
            // Handle empty file error
            $errorMsg = "Please select an image to upload.";
        }

        // Check if there is an error message
        if (isset($errorMsg)) {
            $_SESSION['uploadErrorMessage'] = $errorMsg;
            // Redirect to the same page to display the error message
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    } elseif (isset($_POST['editform'])) {
        
        // Edit image
        $image = $_FILES['image'];
        // Edit heading
        $heading = $_POST['heading'];
        // Edit description
        $description = $_POST['description'];

        // Check if the image is not empty and there are no errors
        if (!empty($image['name']) && $image['error'] === 0) {
            // Check file size
            $maxFileSize = 30 * 1024 * 1024; // 30MB

            if ($image['size'] > $maxFileSize) {
                $errorMsg = "File size exceeds the maximum limit (30MB).";
            } elseif (!in_array(strtolower(pathinfo($image['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])) {
                $errorMsg = "Only JPG, JPEG, and PNG files are allowed.";
            } else {
                // Upload the image file
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($image['name']);

                if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                    // Update the image in the upload table
                    $updateQuery = "UPDATE `upload` SET image = ? WHERE product_id = ?";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("si", $image['name'], $product_id);
                    $stmt->execute();
                    $stmt->close();
                    // Redirect to the same page to prevent duplicate form submission
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit;
                } else {
                    // Handle file upload error
                    $errorMsg = "File upload failed.";
                }
            }
        } else {
            // Handle empty file error
            $errorMsg = "Please select an image to upload.";
        }

        // Check if the heading is not empty
        if (!empty($heading)) {
            // Update the heading in the upload table
            $updateQuery = "UPDATE `upload` SET heading = ? WHERE product_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $heading, $product_id);
            $stmt->execute();
            $stmt->close();

            // Redirect to the same page to prevent duplicate form submission
            header("Location: " . $_SERVER['REQUEST_URI']);
            
        }

        // Check if the description is not empty
        if (!empty($description)) {
            // Update the description in the upload table
            $updateQuery = "UPDATE `upload` SET description = ? WHERE product_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $description, $product_id);
            $stmt->execute();
            $stmt->close();

            // Redirect to the same page to prevent duplicate form submission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    } elseif (isset($_POST['removeUpload'])) {
        // Remove upload
        $removeQuery = "DELETE FROM `upload` WHERE product_id = ?";
        $stmt = $conn->prepare($removeQuery);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
        // Redirect to the same page to prevent duplicate form submission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$conn->close();



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details Mobile-store</title>
  <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="page3.css">
    <script src="page3.js"></script>
    <script>
    // Function to go back to the previous page
    function goBack() {
      window.history.back();
    }
  </script>
</head>

<body>

    <!-- <div class="dark-mode-toggle" style="display:none;">
        <input type="checkbox" name="darkMode" id="darkModeToggle" onclick="toggleDarkMode()">
        <label for="darkModeToggle">Dark Mode</label>
    </div> -->
    <div class="container">
        <!-- <button class="btn-back" onclick="window.location.href = 'group.php?userid=<?php //echo $userId; ?>'" > -->
  <!-- <i class="fas fa-chevron-left"></i></button> -->
  <div class="contact-form"><div class="headname">
                <img src="image/mobilelogo77.png" alt="project Logo"> <h1><em>Mobile-store </em></h1>
            </div><hr>
            <div class="row">
                <div class="col-md-6">
                    <h3>Product: <?php echo $_GET['product_name']; ?></h3>
                    <h3>Group: <?php echo $productDetails['groupname']; ?></h3><br>

                    <div class="image-gallery">
                        <?php if (isset($_SESSION['uploadErrorMessage'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['uploadErrorMessage']; ?></div>
                            <?php unset($_SESSION['uploadErrorMessage']); ?>
                        <?php endif; ?>
                        <?php if ($uploadDetails): ?>
                            <div class="card mb-3">
                                <img src="uploads/<?php echo $uploadDetails['image']; ?>" class="card-img-top" alt="product_image" >
                                <div class="card-body">
                                    <h4 class="card-title"><?php echo $uploadDetails['heading']; ?></h4>
                                    <p class="card-text"><?php echo $uploadDetails['description']; ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="no-images">No images uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <h3 class="text-center mb-4">Upload Details</h3>

                    <?php if (!$uploadDetails): ?>
                        <div class="uploadForm">
                            <h4>Upload Details</h4>
                            <form class="my-s" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="image">Choose an image:</label>
                                    <input type="file" name="image" accept="image/*"  class="form-control" id="image" required>
                                </div>
                                <div class="form-group">
                                    <label for="heading">Heading:</label>
                                    <input type="text" name="heading" class="form-control" id="heading" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <textarea name="description" class="form-control" id="description" required></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="upload" class="btn btn-success">Upload</button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <button onclick="showEditForm()" class="btn btn-primary">Edit Details</button>
                        <button onclick="removeUpload()" class="btn btn-danger">Remove Upload</button>
                        <div class="editForm" style="display: none;">
                            <h4>Edit Upload Details</h4>
                            <form class="my-s" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="image">Change the image:</label>
                                    <input type="file" name="image" accept="image/*"  class="form-control" id="image">
                                   
                                    <label for="heading">Change the heading:</label>
                                    <input type="text" name="heading" class="form-control" id="heading" value="<?php echo $uploadDetails['heading']; ?>">
                                   
                                    <label for="description">Change the description:</label>
                                    <textarea name="description" class="form-control" id="description"><?php echo $uploadDetails['description']; ?></textarea>

                                    <button type="submit" name="editform" class="btn btn-primary mt-2">Save</button>
                                </div>
                            </form>
                            <button onclick="cancelEditForm()" class="btn btn-primary">Cancel</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    <nav >
      <div class="container">
        <ul> 
        <!-- Modify the anchor element for the "products" link -->
        <li>
  <a href="product.php?userid=<?php echo $productDetails['userid']; ?>&groupid=<?php echo urlencode($productDetails['groupid']); ?>">products</a>
</li>



          <li><a href="contact.php">Contact</a></li>
          <li><a class="active" href="">Details</a></li>
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
    <div class="settings-page" style="display: none;">
    <h2></h2>

<ol>
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
  <li>
    <label>
      <span class="return-button" onclick="closeSettings()">&times;</span>
    </label>
  </li>
</ol>
    </div>

    <!--completed the project-->
</body>
</html>