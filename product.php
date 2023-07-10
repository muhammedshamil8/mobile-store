<?php
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_name = $_POST['product_name'];
  $groupid = $_POST['groupid'];

   if(!empty($product_name) || !empty($groupid)){
    $servername = "mysql_db";
    $username = "root";
    $password = "root";
    $database = "ashii";
  

                // create connection
                $conn = new mysqli($servername, $username, $password, $database);
                
                // check connection
                if (mysqli_connect_error()){
                  die('connect error ('. mysqli_connect_error().')'. mysqli_connect_error());
                }else {
                  $SELECT = "SELECT product_name FROM device WHERE product_name = ? LIMIT 1";
                  $INSERT = "INSERT Into device (product_name,groupid) values(?,?)";
                  
                 
                  
                 
                  // prepare statment
                  $stmt = $conn->prepare($SELECT);
                  $stmt->bind_param("s",$product_name);
                  $stmt->execute();
                  $stmt->bind_result($product_name);
                  $stmt->store_result();
                  $rnum =$stmt->num_rows;
                  // echo $SELECT;

                  if ($rnum==0){
                    $stmt->close();

                    $stmt = $conn->prepare($INSERT);
                    $stmt->bind_param("si",$product_name,$groupid);
                    $stmt->execute(); 
                    echo '<center>';
                    echo "new product added sucessfully";
                  }else{
                     echo "someone already added using this product name";
                     echo '</center>';
                  }
                  $stmt->close();
                  $conn->close();
                }
}else {
  echo "All field are required";
  die();
}
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>ashii</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">

  <style>
    body {
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
      position: fixed;
      top: 20px;
      right: 20px;
      display: inline-block;
      width: 60px;
      height: 34px;
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
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
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
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    .card {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  padding: 50px;
  text-align: center;
}


    .add-group {
      background-color: #2196F3;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 10px 50px;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 20px;
    }

    .add-group:hover {
      background-color: #0077C2;
    }

    /* Media queries for different screen sizes */
    @media (max-width: 480px) {
      .card {
        padding: 20px;
      }
    }

    @media (min-width: 481px) and (max-width: 768px) {
      .card {
        padding: 30px;
      }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
      .card {
        padding: 40px;
      }
    }

    @media (min-width: 1025px) {
      .card {
        padding: 50px;
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
  padding: 10px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
}

.close-button {
  position: absolute;
  top: 50%;
  right: 50px;
  transform: translateY(-50%);
  cursor: pointer;
  background: red;
  padding: 5px;
  color: white;
}

.search-button {
  height: 38px;
  padding: 8px 15px;
  background-color: #2196F3;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-size: 16px;
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

     
      .table {
       width: 400px;
       border-collapse: collapse;
    
      }

     .table th, .table td {
     border: 3px solid #808080;
     padding: 8px;
     text-align: left;
      }
      .table td{
      background-color: #CAE7D3;
      }
     .table th {
     background-color: #f2f2f2;
     font-weight: bold;
    }

    </style>
    <script>
      function toggleDarkMode() {
        var body = document.querySelector('body');
        body.classList.toggle('dark-mode');
      }

      function search() {
  var searchInput = document.getElementById('search-input').value;
  console.log("Search input:", searchInput);
  // Perform search operation with the input value
}

function clearSearch() {
  var input = document.getElementById('search-input');
  input.value = ''; // Clear the input value
}

      function showAddproductForm() {
        var addGroupForm = document.querySelector('.add-group-form');
        addGroupForm.style.display = 'block';
      }
      function backspace() {
        var addGroupForm = document.querySelector('.add-group-form');
        addGroupForm.style.display = 'none';
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
          <a href="page.php" class="btn btn-secondary">Groups</a>
          <br>
            <h2>welcome to device </h2>
            <hr>
            <div class="search-container">
  <input type="text" id="search-input" class="form-control search-input" placeholder="Search">
  <span class="close-button" onclick="clearSearch()">&times;</span>
  <button onclick="search()" class="btn btn-primary search-button">
    <i class="fas fa-search"></i>
  </button>
</div>

            <div class="add-group-form">
            <form method="POST" action="">
            <input type="text" name="product_name" placeholder="Product Name" required class="form-control"><br>
            <input type="nummber" name="groupid" placeholder="Group id" required class="form-control"><br>
  <input type="submit" class="btn btn-primary" value="Create">&nbsp;
  <button onclick="backspace()" class="btn btn-secondary">Back</button>
</form>

            </div>
            <button onclick="showAddproductForm()" class="btn btn-primary add-group">+ Add products</button>
            <br>
            <h3>My products</h3>
            <table class="table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Product Name</th>
                  <th>Group name </th>
                </tr>
              </thead>
              <tbody>
              <?php
                $servername = "mysql_db";
                $username = "root";
                $password = "root";
                $database = "ashii";

                // create connection
                $connection = new mysqli($servername,$username,$password,$database);

                // check connection
                if ($connection->connect_error){
                  die("connection failed: ". $connection->connect_error);
                }
                // read all row from databse table
                $sql ="SELECT device.id,device.product_name,`groups`.`groupname`
                FROM device,`groups`
                WHERE device.groupid = `groups`.`id`
                ORDER BY device.id";
                $result = $connection->query($sql);

                if (!$result){
                  die("invalid query: . $connection->error");
                }

                // read data of each row
                while($row = $result->fetch_assoc()){
                  echo "<tr>
                  <td>" . $row["id"] ."</td>
                  <td>" . $row["product_name"] ."</td>
                  <td>" . $row["groupname"] ."</td>
                  </tr>";
 
                }

                
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

   

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>

  </html>
