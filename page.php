<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ashii</title>
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
      text-align: center;
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
      background-color: #0077C2;
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

    .table th,
    .table td {
      border: 3px solid #808080;
      padding: 8px;
      text-align: left;
    }

    .table td {
      background-color: #CAE7D3;
    }

    .table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }

    .custom-link {
      text-decoration: none;
      color: black;
    }
  </style>
  <script>
    function searchFun() {
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

    //     function searchProductSuggestions() {
    //   var input = document.getElementById('searchInput').value.toUpperCase();
    //   var suggestions = document.getElementById('productSuggestions');
    //   var products = 

    //   td.textContent || td.innerHTML = '';

    //   if (input.trim() !== '') {
    //     products.forEach(function (product) {
    //       if (product.product_name.toUpperCase().includes(input)) {
    //         var option = document.createElement('option');
    //         option.value = product.product_name;
    //         suggestions.appendChild(option);
    //       }
    //     });
    //   }
    // }
  </script>
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="search-container">
            <span class="close-button" onclick="clearSearch()">&times;</span>
            <input type="text" id="searchInput" name="search" placeholder="Search" class="form-control search-input"
              onkeyup="searchFun()">
            <datalist id="groupSuggestions"></datalist>
            <button onclick="searchFun()" class="btn btn-primary search-button">
              <i class="fas fa-search"></i>
            </button>
          </div>
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
                  $connection = new mysqli($servername, $username, $password, $databse);

                  // check connection
                  if ($connection->connect_error) {
                    die("connection failed: " . $connection->connect_error);
                  }
                  // read all row from databse table
                  $sql = "select * from `groups`";
                  $result = $connection->query($sql);

                  if (!$result) {
                    die("invalid query: . $connection->error");
                  }

                  // read data of each row
                  while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                  <td><a href=\"product.php\" class=\"custom-link\">" . $row["id"] . "</a></td>
                  <td><a href=\"product.php\" class=\"custom-link\">" . $row["groupname"] . "</a></td>
                  <td><a href=\"product.php\" class=\"custom-link\"><button class=\"search-button\">open</button></a></td>
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
</body>

</html>

