<?php
session_start();
include 'db_conn.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
  $query = "SELECT * FROM users WHERE username = :username";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':username', $_POST['username']);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);


  if ($user) {
      if ($_POST['password'] === $user['password']) {
          $_SESSION['username'] = $_POST['username'];
          header("Location: group.php?userid=" . $user['id']);
          exit();
      } else {
          $errorMsg = "Invalid password";
      }
  } else {
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
    <!-- <link rel="stylesheet" type="text/css" href="page0.css"> -->
<style>
    /* Common styles for body and container */
body {
    background-color: #38444d;
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
  }
  
  .container {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  /* Styles for the card */
  .card {
    max-width: 400px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(230, 227, 227, 0.548);
    border: solid rgb(6, 16, 46);
    margin: 10px; /* Add some margin to the card to reduce free space */
  }
  
  /* Heading styling */
  .headname {
    background-image: linear-gradient(to left, lightskyblue, royalblue, lightskyblue);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    border-radius: 10px 10px;
    margin-bottom: 20px; /* Add margin at the bottom of the header */
  }
  
  .headname img {
    max-height: 50px;
    border-radius: 50%;
    margin-right: 10px;
  }
  
  .headname h1 {
    font-size: 32px;
    color: white;
    margin-bottom: 0;
  }
  
  /* Card header styling */
  .card-header {
    background-image: linear-gradient(to left, lightskyblue, royalblue);
    color: white;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    padding: 15px 0;
    border-radius: 10px 10px 0 0;
    margin: 0; /* Remove the margin to reduce free space */
  }
  
  /* Card body styling */
  .card-body {
    padding: 20px;
  }
  .form-group label {
    font-size: 20px;
    font-weight: bold;
    color: #333;
  }
  
  .form-control {
    font-size: 18px;
    padding: 12px ;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  
  
  /* Card footer styling */
  .card-footer {
    background-color: #dfdede;
    color: #222222;
    text-align: right;
    font-size: 14px;
    padding: 10px;
    border-top: 1px solid #ccc;
    border-radius: 0 0 10px 10px;
  }
  
  .card-footer a {
    text-decoration: none;
    color: #333333;
  }
  
  .card-footer a:hover {
    color: #000000;
  }
  
  /* Primary button styling */
  .btn-primary {
    margin-top: 20px;
    background-color: #2485c5;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 50px;
    font-size: 30px;
    cursor: pointer;
    z-index: 9999;
    animation: pulsate 2s infinite;
  }
  
  .btn-primary b {
    position: relative;
    z-index: 2;
  }
  
  @keyframes pulsate {
    0% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.1);
    }
    100% {
      transform: scale(1);
    }
  }
  
  /* Media Queries */
  /* (Your media query rules here) */
  /* @media (min-width: 769px) {
    html{
        font-size: 45%;
    }
  }
  @media (max-width: 1024px){
 html{
    font-size: 55%;
 }
} */
@media (min-width: 769px) {
    /* Larger font size for screens wider than 768px */
    .form-group label {
      font-size: 24px;
    }

  .container {
    height: 50vh;
  }
  .form-control {
    font-size: 18px;
    padding: 15px 25px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  }
  
  @media (max-width: 768px) {
    /* Smaller font size for screens up to 768px */
    .form-group label {
      font-size: 16px;
    }

  
  }
  
    </style>
</head>

<body>
    
    <div class="headname">
        <img src="image/mobilelogo77.png" alt=" Logo">
        <h1><em> Mobile-store &lt;3 </em></h1>
    </div>
    <div class="container">
        <div class="card my-auto shadow">
            <div class="card-header">
                <h2>Welcome..!</h2>
            </div>
            <div class="card-body">
                <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
                <?php endif; ?>
                <form action="index.php" method="POST">
                
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
              
                    <small>&copy; zamil</small>
                
            </div>
        </div>
    </div>
</body>
</html>
