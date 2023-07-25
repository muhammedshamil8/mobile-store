<?php
/*


<!-- try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ... (execute queries, fetch data, etc.)
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

another 


$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ... (execute queries, fetch data, etc.)
$conn->close();

 -->
 <!-- 

 
<div class="search-container">
  Corrected the form method to "GET" and removed the action 
  <form method="GET">

    <input type="text" id="myInput" class="form-control search-input" placeholder="Search the products" name="search" onkeyup="searchFun()" />
    onsubmit="event.preventDefault(); searchFun();" 
    <span class="close-button" onclick="clearSearch()">&times;</span>
    Use the type="button" attribute to prevent form submission on button click 
    <button type="button" class="btn btn-primary search-button" onclick="searchFun()">
      <i class="fas fa-search"></i>
    </button>
  </form>
</div>


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

function clearSearch() {
  var input = document.getElementById('myInput');
  input.value = ''; // Clear the input value

  // Reload the page without the search query
  let userId = <?php echo json_encode($userId); ?>;
  location.href = 'group.php?userid=' + userId;
}
</script>

  -->

 
<input type="text" name="fun" id="greeting" class="form-control" disabled><br>

<script>
function getGreeting() {
    const currentTime = new Date();
    const currentHour = currentTime.getHours();

    let greeting = "";

    if (currentHour < 12) {
        greeting = "Good morning 😃";
    } else if (currentHour < 18) {
        greeting = "Good afternoon 🌞";
    } else {
        greeting = "Good evening 🌙";
    }

    // Update the input field value with the calculated greeting
    document.getElementById("greeting").value = greeting;
}

// Call the getGreeting function to set the initial greeting when the page loads
getGreeting();
</script>

*/
?>