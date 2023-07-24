<?php
session_start();

// Include the console.php file first
include 'console.php';
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Create a new PDO connection using the environment variables
  $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);

  // Prepare the SQL query to check if the user exists
  $query = "SELECT * FROM users WHERE username = :username";
  $stmt = $pdo->prepare($query);
  // Bind the parameter
  $stmt->bindParam(':username', $_POST['username']);
  // Execute the query
  $stmt->execute();
  // Fetch the result
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Check if a user was found
  if ($user) {
      // Verify the password (no hashing needed here)
      if ($_POST['password'] === $user['password']) {
          // Passwords match, authentication successful
          $_SESSION['username'] = $_POST['username'];
          // Redirect to the group page based on the username
          header("Location: group.php?userid=" . $user['id']);
          exit();
      } else {
          // Passwords do not match
          $errorMsg = "Invalid password";
      }
  } else {
      // User not found, show an error message
      $errorMsg = "User not found. Please register.";
  }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Mobile-store</title>
    <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="page0.css">
</head>

<body>
    <div class="headname">
        <img src="image/mobilelogo77.png" alt=" Logo">
        <h1><em> Mobile-store &lt;3 </em></h1>
    </div>
    <div class="container">
        <div class="card my-auto shadow">
            <div class="card-header">
                <h2>Login Form</h2>
            </div>
            <div class="card-body">
                <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
                <?php endif; ?>
                <form action="index.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" class="form-control" name="username" required />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" class="form-control" name="password" required />
                    </div><br>
                    <input type="submit" class="btn btn-primary w-100" value="Login" />
                </form>
            </div>
            <div class="card-footer">
                <a href="product.php">
                    <small>&copy; zamil</small>
                </a>
            </div>
        </div>
    </div>
</body>
</html>