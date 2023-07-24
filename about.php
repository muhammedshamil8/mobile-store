<!DOCTYPE html>
<html>

<head>
  <title>About Mobile-store</title>
  <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" type="text/css" href="#styles.css">
  <style>
    /* Custom CSS for Dark Mode */
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      margin: 0;
      padding: 0;
      
    }

    body.dark-mode {
      background-color: #212529;
      color: #f8f9fa;
    }

    .card {
      background-color: #f2f2f2;
      color: #333;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
       width: 600px;
       margin: 0 auto;
      right:50%;
    }

    .card.dark-mode {
      background-color: #343a40;
      color: #f8f9fa;
    }

    .card.dark-mode h1,
    .card.dark-mode h3,
    .card.dark-mode p {
      color: #f8f9fa;
    }

    /* Adjust the background color and text color for the header */
    header {
      background-color: #28a745;
      color: #fff;
      align-items: center;
      padding: 1rem;
    }
    
    header.dark-mode {
      background-color: #006400; /* Dark mode header background color */
    }

    /* Add more custom styles for the header in dark mode here as needed */

    /* Adjust the background color and text color for the main content */
    main {
      padding: 3rem;
    }

    main.dark-mode {
      background-color: #212529;
    }

    /* Add more custom styles for the main content in dark mode here as needed */

    /* Make the image circular */
    img {
      border-radius: 50%;
      border: 2px solid #000;
      
    }

    /* Align the name and logo on the same line */
    .headname {
      display: flex;
      align-items: center;
    }

    .headname h1 {
      margin-left: 1rem;
    }

  </style>
  <script src="page1.js"></script>
</head>

<body id="body" class="light-mode">
  <header>
    <div class="headname">
      <img src="image/mobilelogo77.png" alt="project Logo" width="50">
      <h1><em>Mobile-store</em></h1>
    </div>
  </header>

    <div class="dark-mode-toggle" style="display:none;">
        <input type="checkbox" name="darkMode" id="darkModeToggle" onclick="toggleDarkMode()">
        <label for="darkModeToggle">Dark Mode</label>
    </div>
  <main>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card">
            <h3 class="mb-3">About Mobile-store</h3>
            <p>Welcome to Mobile-store, your one-stop destination for all your data storing needs. We are committed to providing a secure and efficient platform for storing and managing your data.</p>
            <p>At Mobile-store, we offer a reliable and user-friendly system for storing a wide range of data, including documents, images, videos, and more. Our platform is designed to ensure that your data remains safe and accessible whenever you need it.</p>
            <p>Our team of experts is dedicated to providing exceptional service, and we are always here to assist you with any queries or concerns you may have. Whether you're an individual user or a business, Mobile-store provides scalable solutions to meet your data storage requirements.</p>
            <p>Thank you for choosing Mobile-store as your preferred platform for data storage. We value your trust and strive to exceed your expectations with our secure and efficient data management services.</p>
            <p>Stay connected with us, and feel free to explore our platform to experience seamless data storage. We are committed to making your data storage journey a hassle-free one!</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
