<?php


session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
         
         body {
        background-image: url('wel2.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        overflow-y: hidden;
    }  
    
       
    </style>
</head>
<body>

    <div class="welcome-bar">
       
        
    </div>

    <div class="banner">
        <header>
        <div class="navbar">
            <img src="logo1.jpeg" class="logo" alt="Logo">
            <ul>
                <li><a href="welcome.php">Home</a></li>
                <li><a href="#Production">Production</a></li>

                <li><a href="#Services">Services</a></li>
                <li><a href="#About Us">About Us</a></li>
                <li><a href="#Contact">Contact Us</a></li>
                <li><a href="index.php">Logout</a></li>
            </ul>
        </div>
        </header>

        

        <div class="content">
             <p style="margin-right:1180px; color: #f98d43; font-size:25px;">Wellcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

            <h1>DESIGN YOUR HOUSE</h1>
             
            <p >
                Creating a home that's both beautiful and functional can feel overwhelming. At MAINE MADE & FURNICHERS, we simplify the process by connecting you with expert insights, practical guides, and innovative solutions for every aspect of home design. From space planning and material selection to finding the right professionals, we're here to empower you to make informed decisions and bring your vision to life with confidence.
            </p>
            <div>
                <button onclick="location.href='index5.html'"><span></span>WATCH</button>
                <button onclick="location.href='https://www.youtube.com/@Nick_Lewis/videos'"><span></span>SUBSCRIBE</button>
            </div> 
        </div>
    </div>
    <!--Home-->
    <section class="welcome.php" id="welcome.php">

    </section>
    <!--production-->
    <section class="Production" id="Production">
        
        
       
    </section>
    <!--services-->
    <section class="Services" id="Services">
        
    </section>
    <!--About us-->
    <section class="About Us" id="About Us">

    </section>
    <!--contact us-->
    <section class="Contact" id="Contact">

    </section>

    <script src="script.js"></script>
</body>
</html>
 