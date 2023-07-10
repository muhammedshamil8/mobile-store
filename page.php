<?php
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $groupname = $_POST['groupname'];

   if(!empty($groupname)){
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
                if (mysqli_connect_error()){
                  die('connect error ('. mysqli_connect_error().')'. mysqli_connect_error());
                }else {
                  $SELECT = "SELECT groupname FROM `groups` WHERE groupname = ? LIMIT 1";
                  $INSERT = "INSERT Into `groups` (groupname) values(?)";
                  
                 
                  
                 
                  // prepare statment
                  $stmt = $conn->prepare($SELECT);
                  $stmt->bind_param("s",$groupname);
                  $stmt->execute();
                  $stmt->bind_result($groupname);
                  $stmt->store_result();
                  $rnum =$stmt->num_rows;
                  // echo $SELECT;

                  if ($rnum==0){
                    $stmt->close();

                    $stmt = $conn->prepare($INSERT);
                    $stmt->bind_param("s",$groupname);
                    $stmt->execute(); 
                    echo '<center>';
                    echo "new group created sucessfully";
                  }else{
                     "someone already created using this group name";
                     echo '<center>';
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
      background-color: #afd2ee;
      transition: background-color 0.3s, color 0.3s;
      margin: 0;
      padding: 0;
    }

    body.dark-mode {
      background-color: #212121;
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
  left: 8px;
  background-color: #2196F3;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 10px 15px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
  z-index: 9999;
}



    .settings-button:hover {
      background-color: #0077C2;
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

    .settings-page {
      position: absolute;
      top: 60px;
      left: 20px;
      background-color: rgba(0, 0, 0, 0.8);
      color: #fff;
      border-radius: 5px;
      padding: 20px;
      text-align:center;
      z-index: 9999;
    }

    .settings-page h2 {
      margin-top: 0;
    }

    .settings-page ul {
      list-style-type: none;
      padding: 0;
    }

    .settings-page li {
      margin-bottom: 10px;
        display: block;
      }

      .settings-page li input[type="checkbox"] {
        margin-right: 5px;
      }

      .settings-page button {
        background-color: #2196F3;
        color: #fff;
        border: none;
       border-radius: 5px;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
      }

      .settings-page button:hover {
        background-color:#0077C2;
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
  padding: 5px;
  color: red;
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

  // Store the dark mode preference in localStorage
  if (body.classList.contains('dark-mode')) {
    localStorage.setItem('darkMode', 'true');
  } else {
    localStorage.setItem('darkMode', 'false');
  }
}

// Retrieve the dark mode preference from localStorage and apply the dark mode on page load
document.addEventListener('DOMContentLoaded', function() {
  var body = document.querySelector('body');
  var darkMode = localStorage.getItem('darkMode');

  if (darkMode === 'true') {
    body.classList.add('dark-mode');
  }
});


      function openSettings() {
        var settingsPage = document.querySelector('.settings-page');
        settingsPage.style.display = 'block';
      }

      function closeSettings() {
        var settingsPage= document.querySelector('.settings-page');
        settingsPage.style.display = 'none';
      }

      function search() {

        // var searchInput = document.getElementById('myinput').value;
        // console.log("Search input:", searchInput);
        // Perform search operation with the input value
      }
      function clearSearch() {
      var input = document.getElementById('myinput');
      input.value = ''; // Clear the input value
      }

      function showAddGroupForm() {
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
  <button class="settings-button" onclick="openSettings()">Settings</button>

    <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
            <h2>Welcome To My Data</h2>
            <hr>
            <div class="search-container">
  <span class="close-button" onclick="clearSearch()">&times;</span>
  <input type="text" id="myinput" class="form-control search-input" name="search" placeholder="Search">
  <button onclick="search()" class="btn btn-primary search-button">
    <i class="fas fa-search"></i>
  </button>
</div>





            <div class="add-group-form">
             
            <form method="POST" action="">
              <input type="text" name="groupname" placeholder="Group Name" required class="form-control"><br>
              <input type="submit" class="btn btn-primary" value="Create">&nbsp;
              <button onclick="backspace()" class="btn btn-secondary">Back</button>
            </form>
          </div>
          
            <button onclick="showAddGroupForm()" class="btn btn-primary add-group">+ Add Group</button>
            <br>
            <h3>My Data Groups</h3>
            <div class="container">
            <div class="search-container">
        <table class="table" id="mytable">
            <thead class="thead-dark">
                <tr>
                    <th>No:</th>
                    <th>Group Name</th>
                    <th>Open</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "mysql_db";
                $username = "root";
                $password = "root";
                $databse = "ashii";

                // create connection
                $connection = new mysqli($servername,$username,$password,$databse);

                // check connection
                if ($connection->connect_error){
                  die("connection failed: ". $connection->connect_error);
                }
                // read all row from databse table
                $sql ="select * from `groups`";
                $result = $connection->query($sql);

                if (!$result){
                  die("invalid query: . $connection->error");
                }

                // read data of each row
                while($row = $result->fetch_assoc()){
                  echo "<tr>
                  <td>" . $row["id"] ."</td>
                  <td>" . $row["groupname"] ."</td>
                  <td><a href=\"product.php\"><button>open</button></a></td>
                  </tr>";
 
                }

                
                ?>
              </tbody>
            </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="settings-page" style="display: none;">
      <span class="close-button" onclick="closeSettings()">&times;</span>
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