<?php
// if (isset($_POST["submit"])){

//   $username = $_POST["name"];
//   $email = $_POST["email"];
//   $subject = $_POST["subject"];
//   $message = $_POST["message"];

//   $to = $email;

//   $subject = $subject;

//   $message = $message;

//   // Always set content-type when sending HTML email
//   $headers = "MIME-Version: 1.0" . "\r\n";
//   $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

//   // More headers
//   $headers .= 'From: kimtaehyung5578@gmail.com';
 

//   $mail = mail($to,$subject,$message,$headers);
  
//   if($mail){
//     echo "<script>alert('Mail Send ! :-)');</script>";
//   }else{
//     echo "<script>alert('Mail Not Send :-)');</script>";
//   }
//   }
  ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Mobile-store</title>
  <link rel="icon" href="image/mobilelogo77.png" type="image/x-icon">
  <script>
  //   function sent() {
  //     alert('Mail sent successfully!');
  //   }
  
    function sent() {
      // Check if all required fields are filled
      var name = document.getElementById("name").value;
      var email = document.getElementById("email").value;
      var subject = document.getElementById("subject").value;
      var message = document.getElementById("message").value;

      if (name === "" || email === "" || subject === "" || message === "") {
        alert("Please fill in all the required fields.");
      } else {
        alert("Mail sent successfully!");
      }
    }
  </script>
  
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      margin: 0;
      padding: 0;
      background-color: #f2f2f2; /* Light mode background color */
      color: #333; /* Light mode text color */
    }

    header {
      background-color: #28a745; /* Green header background color */
      color: #fff; /* Header text color */
      text-align: center;
      padding: 1rem;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 2rem;
      background-color: #fff; /* Light mode container background color */
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Light mode box shadow */
    }

    /* Dark mode styles */
    body.dark-mode {
      background-color: #333; /* Dark mode background color */
      color: #fff; /* Dark mode text color */
    }

    body.dark-mode header {
      background-color: #006400; /* Dark mode header background color */
    }

    body.dark-mode .container {
      background-color: #444; /* Dark mode container background color */
      box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1); /* Dark mode box shadow */
    }

    /* Other styles remain the same for both light and dark modes */
    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    label {
      font-weight: bold;
    }

    input,
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      background-color: #fff; /* Light mode input background color */
      color: #333; /* Light mode input text color */
    }

    textarea {
      resize: vertical;
    }

    input[type="submit"].btn {
      grid-column: 1 / span 2;
      background-color: #ff0000; /* Red button background color */
      color: #fff; /* Button text color */
      cursor: pointer;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
    }
     /* Dark mode styles for the red button */
     body.dark-mode input[type="submit"].btn {
      background-color: #8b0000; /* Dark mode button background color */
    }
    /* Dark mode styles for form inputs and button */
    body.dark-mode input,
    body.dark-mode textarea {
      background-color: #444; /* Dark mode input background color */
      color: #fff; /* Dark mode input text color */
    }

    body.dark-mode input[type="submit"] {
      background-color: #006400; /* Dark mode button background color */
    }
  </style>
  <script src="page1.js"></script>
</head>

<body>
  <header>
    <h1>Contact Us</h1>
    
  </header>

    <div class="dark-mode-toggle" style="display:none;">
        <input type="checkbox" name="darkMode" id="darkModeToggle" onclick="toggleDarkMode()">
        <label for="darkModeToggle">Dark Mode</label>
    </div>
  <div class="container">
    <p>
      If you have any questions, suggestions, or just want to say hello, feel free to get in touch with me using the form
      below. I would love to hear from you!
    </p>
    <form action="" method="post" autocomplete="off">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required />

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required />

      <label for="subject">Subject:</label>
      <input type="text" id="subject" name="subject"  required />

      <label for="message">Message:</label>
      <textarea id="message" name="message" rows="6"  required ></textarea>

      <input type="submit" name="submit" value="Sent" onclick="sent()" class="btn" />
    </form>
  </div>


</body>

</html>
