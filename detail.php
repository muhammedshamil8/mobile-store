<?php
$servername = "mysql_db";
$username = "root";
$password = "root";
$database = "ashii";

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

// Check if the upload form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload'])) {
        // Upload image
        $image = $_FILES['image'];

        // Check if the image is not empty and there are no errors
        if (!empty($image['name']) && $image['error'] === 0) {
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
            } else {
                // Handle file upload error
                die("File upload failed.");
            }
        }
    } elseif (isset($_POST['editImage'])) {
        // Edit image
        $image = $_FILES['image'];

        // Check if the image is not empty and there are no errors
        if (!empty($image['name']) && $image['error'] === 0) {
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
            } else {
                // Handle file upload error
                die("File upload failed.");
            }
        }
    } elseif (isset($_POST['editHeading'])) {
        // Edit heading
        $heading = $_POST['heading'];

        // Check if the heading is not empty
        if (!empty($heading)) {
            // Update the heading in the upload table
            $updateQuery = "UPDATE `upload` SET heading = ? WHERE product_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $heading, $product_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['editDescription'])) {
        // Edit description
        $description = $_POST['description'];

        // Check if the description is not empty
        if (!empty($description)) {
            // Update the description in the upload table
            $updateQuery = "UPDATE `upload` SET description = ? WHERE product_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $description, $product_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['removeUpload'])) {
        // Remove upload
        $removeQuery = "DELETE FROM `upload` WHERE product_id = ?";
        $stmt = $conn->prepare($removeQuery);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the product page
    header("Location: detail.php?product_id=" . urlencode($product_id) . "&product_name=" . urlencode($productDetails['product_name']));
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moving Bird</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="page3.css">
    <script src="page3.js"></script>
</head>

<body>
    <div class="dark-mode-toggle" style="display:none;">
        <input type="checkbox" name="darkMode" id="darkModeToggle" onclick="toggleDarkMode()">
        <label for="darkModeToggle">Dark Mode</label>
    </div>
    <div class="container">
        <div class="contact-form">
            <a href="group.php" class="btn btn-primary">Back</a>
            <h2 class="text-center mb-4">Welcome to Moving Bird</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Product: <?php echo $_GET['product_name']; ?></h3>
                    <h3>Group: <?php echo $productDetails['groupname']; ?></h3><br>

                    <div class="image-gallery">
                        <?php if ($uploadDetails): ?>
                            <div class="card mb-3">
                                <img src="uploads/<?php echo $uploadDetails['image']; ?>" class="card-img-top">
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
                                    <input type="file" name="image" class="form-control" id="image" required>
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
                                    <input type="file" name="image" class="form-control" id="image">
                                    <button type="submit" name="editImage" class="btn btn-primary mt-2">Save</button>
                                </div>
                            </form>
                            <form class="my-s" method="post">
                                <div class="form-group">
                                    <label for="heading">Change the heading:</label>
                                    <input type="text" name="heading" class="form-control" id="heading" value="<?php echo $uploadDetails['heading']; ?>">
                                    <button type="submit" name="editHeading" class="btn btn-primary mt-2">Save</button>
                                </div>
                            </form>
                            <form class="my-s" method="post">
                                <div class="form-group">
                                    <label for="description">Change the description:</label>
                                    <textarea name="description" class="form-control" id="description"><?php echo $uploadDetails['description']; ?></textarea>
                                    <button type="submit" name="editDescription" class="btn btn-primary mt-2">Save</button>
                                </div>
                            </form>
                            <button onclick="cancelEditForm()" class="btn btn-primary">Cancel</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

  
</body>

</html>
