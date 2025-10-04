<?php
session_start();
require 'db.php'; // Ensure this path is correct

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name']);
    $phone = trim($_POST['no']);
    $email = trim($_POST['em']);
    $password = password_hash($_POST['pa'], PASSWORD_DEFAULT);

    // Basic validation before database interaction
    if (empty($username) || empty($phone) || empty($email) || empty($_POST['pa'])) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone number must be 10 digits.";
    } else {
        // Check if user already exists (email or username)
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, phone, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $phone, $email, $password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header("Location: login.php"); // Redirect to login page after successful registration
                exit();
            } else {
                $error = "Error during registration: " . $stmt->error;
            }
        }
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Maine Made Sign Up</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* General Styles */
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden;
            padding-top: 60px; /* Space for the fixed navbar */
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            padding: 0 0px;
            color: white;
            width: 100%;
            position: fixed; /* Makes the navbar stick to the viewport */
            top: 0;          /* Positions it at the top of the viewport */
            left: 0;         /* Ensures it starts from the left edge */
            z-index: 1000;   /* Keeps it on top of other content when scrolling */
        }

        .navbar img.logo {
            cursor: pointer;
            border-radius: 50%;
            width: 60px;
            padding: 0 4px;
            left: 100px;
            position: relative;
            animation: logorotation 4s infinite alternate-reverse; /* Apply animation */
        }

        .navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex; /* Use flexbox for horizontal list items */
        }

        .navbar ul li {
            position: relative;
            font-size: 20px;
            padding: 15px;
        }

        .navbar ul li::after {
            content: '';
            width: 0;
            height: 3px;
            background: blue;
            position: absolute;
            left: 0;
            bottom: -10px;
            transition: 0.5s;
        }

        .navbar ul li:hover::after {
            width: 100%;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            text-transform: uppercase;
            display: block; /* Make links fill the padding area for better clickability */
        }

        /* Dropdown specific styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none; /* Initially hidden */
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            left: 0; /* Align dropdown with the parent li */
            top: 100%; /* Position below the parent li */
            border-radius: 5px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-transform: none;
            border-radius: 5px;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        /* Class to show the dropdown when toggled by JavaScript */
        .dropdown-content.show {
            display: block;
        }

        /* Main Container and Slider */
        .container {
            display: flex;
            height: calc(100vh - 60px); /* minus navbar height */
        }

        .slider {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        .slides {
            display: flex;
            width: 100%;
            height: 100%;
            animation: slide 10s infinite alternate;
        }

        .slides img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures images cover the area without distortion */
        }

        /* Animations */
        @keyframes slide {
            0% { transform: translateX(0); }
            25% { transform: translateX(-25%); }
            50% { transform: translateX(-50%); }
            75% { transform: translateX(-75%); }
            100% { transform: translateX(-85%); } /* Adjusted for a smoother loop or fewer images */
        }

        @keyframes logorotation {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(360deg); }
        }

        /* Hero Section Text and Buttons */
        h1.page-title {
            position: absolute;
            top: 10px; /* Adjusted to be visible */
            left: 10px;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            z-index: 10;
            text-align: center;
            font-size: 70px;
            margin-left: 200px;
            margin-top: 210px; /* Aligned with .slider h1 */
        }

        p.pagecontent {
            z-index: 5;
            position: absolute;
            text-align: center;
            color: white;
            margin-top: 350px;
            left: 50%;
            transform: translateX(-50%); /* Center the paragraph horizontally */
            width: 100%; /* Ensure it doesn't span too wide */
             /* Limit max width */
        }

        .watch-more-btn, .subscribe-btn {
            position: absolute;
            top: 65%;
            width: 200px;
            padding: 15px 20px;
            margin: 10px;
            border-radius: 25px;
            border: 2px solid #009688;
            background-color: black;
            color: white;
            cursor: pointer;
            font-weight: bold;
            z-index: 10;
            transition: background 0.3s, color 0.3s;
        }

        .watch-more-btn {
            left: 41%;
            transform: translateX(-50%);
        }

        .subscribe-btn {
            left: 61%;
            transform: translateX(-50%);
        }

        .watch-more-btn:hover,
        .subscribe-btn:hover {
            background-color: #009688;
            color: black;
        }

        /* Modal (Sign Up Form) Styles */
        .form-overlay {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 100; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }

        .form-section {
            background-color: #fefefe;
            margin: auto;
            padding: 15px;
            border: 1px solid #888;
            width: 90%;
            max-width: 320px;
            border-radius: 8px;
            position: relative;
        }

        .form-overlay.active {
            display: flex; /* Show the overlay */
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .form-box label {
            display: block;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-box input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .form-box button[type="submit"] {
            width: 100%;
            padding: 9px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-box button[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-box a {
            display: block;
            text-align: center;
            margin-top: 8px;
            text-decoration: none;
            color: #333;
            font-size: 13px;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Close button for the modal */
        .close-button {
            color: #aaa;
            position: absolute;
            top: 5px;
            right: 10px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* New styles for the streamlined Services section */
        .Services1 {
            padding: 40px 20px; /* Add some vertical padding */
            max-width: 1400px; /* Max width to keep content readable on large screens */
            margin: 20px auto; /* Center the content area */
            background-color: #fff; /* Optional: give it a white background */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Optional: subtle shadow */
            border-radius: 8px; /* Optional: rounded corners */
        }

        .Services1 h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 36px; /* Adjust as needed */
        }

        .Services1 .section-intro {
            text-align: center;
            margin-bottom: 40px;
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }

        .service-section {
            margin-bottom: 40px;
        }

        .service-section h2, .why-us-section h2 {
            text-align: center;
            color: #007bff; /* A nice blue for section headings */
            margin-bottom: 25px;
            font-size: 28px;
        }

        .service-section ul {
            list-style: none; /* Remove default bullets */
            padding: 0;
            margin: 0 auto;
            max-width: 800px;
        }

        .service-section ul li {
            background-color: #e9f5ff; /* Light blue background for list items */
            padding: 15px 20px;
            margin-bottom: 10px;
            border-left: 5px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
            line-height: 1.5;
            color: #444;
        }

        .service-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive grid */
            gap: 25px; /* Space between grid items */
            margin-top: 20px;
        }

        .service-item {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .service-item:hover {
            transform: translateY(-5px);
        }

        .service-item h3 {
            color: #333;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .service-item p {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
        }

        .why-us-section ul {
            list-style: disc; /* Use standard bullets */
            padding-left: 25px;
            max-width: 800px;
            margin: 0 auto 40px auto;
            line-height: 1.8;
            font-size: 16px;
            color: #444;
        }

        .cta-section {
            text-align: center;
            padding: 30px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            margin-top: 50px;
        }

        .cta-section h2 {
            color: white;
            margin-bottom: 15px;
            font-size: 32px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 25px;
        }

        .cta-section .button {
            display: inline-block;
            padding: 12px 25px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .cta-section .primary-cta {
            background-color: white;
            color: #007bff;
            border: 2px solid white;
        }

        .cta-section .primary-cta:hover {
            background-color: #e6e6e6;
            color: #0056b3;
        }

        .cta-section .secondary-cta {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .cta-section .secondary-cta:hover {
            background-color: white;
            color: #007bff;
        }
        .About1{
           padding: 40px 20px; /* Add some vertical padding */
           max-width: 1400px; /* Max width to keep content readable on large screens */
           margin: 20px auto; /* Center the content area */
           background-color: #fff; /* Optional: give it a white background */
           box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Optional: subtle shadow */
           border-radius: 8px;
        }
        .About1 h1{
           color:rgb(240, 160, 47);
           font-size:50px;
        }
        .About1 p{
           background-color:rgba(0,0,0,0.1);
           padding:50px 50px;
        }
        .Team1{
            padding: 40px 20px;/* Add some vertical padding */
            max-width: 1400px; /* Max width to keep content readable on large screens */
            margin: 20px auto; /* Center the content area */
            background-color: #fff; /* Optional: give it a white background */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Optional: subtle shadow */
            border-radius: 8px;
        }
        .Team1 p{
            text-align:center;
            color:black;
            font-size:23px;
            background-color:#007bff;
            padding:20px;
            border-radius:12px;
        }
        .Team1 h1{
            text-align:center;
            color:rgb(240, 160, 47);
            font-size:56px;
        }
        .Team{
            display:grid;
            grid-template-columns: repeat(auto-fit,minmax(280px,auto));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .Team .box{
            background-color: #f9f3f3;
            height:300px;
            position: relative;
            box-shadow:2px 2px 10px 14px rgb(14 55 54 / 15%);
        }
        .Team .box img{
           width:100%;
           height: 250px;
           object-fit: contain;
           object-position: center;
           padding: 15px;
           background-color: #f9f3f3;
           border-radius: 9px;
        }
        .Team .box h3 {
            margin-top:0.1px;
            text-align:center;
        }
        .Contact1{
            padding: 40px 20px;/* Add some vertical padding */
            max-width: 1400px; /* Max width to keep content readable on large screens */
            margin: 20px auto; /* Center the content area */
            background-color: #fff; /* Optional: give it a white background */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Optional: subtle shadow */
            border-radius: 8px;
        }
        .Contact1 h1{
            font-size:50px;
            text-align:center;
            color:rgb(240, 160, 47);
        }
        .contact {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px 20px;
            border-radius:12px;
            color:white;
            display: flex; /* Use flexbox for layout */
            flex-wrap: wrap; /* Allow items to wrap to the next line on smaller screens */
            justify-content: space-around; /* Distribute space around items */
            align-items: flex-start; /* Align items to the top */
            gap: 30px; /* Add space between flex items */
        }

        .contact .social {
            flex: 1; /* Allow this section to grow and shrink */
            min-width: 280px; /* Minimum width before wrapping */
            display: flex; /* Use flexbox for social icons */
            flex-direction: column; /* Stack social title and icons vertically */
            align-items: flex-start; /* Align contents to the start */
            gap: 10px; /* Space between social title and icons */
            padding: 10px 0;
        }

        .contact .social h2 {
            margin-bottom: 5px; /* Small margin below social media title */
        }

        .contact .social div { /* This div holds the actual social links */
            display: flex;
            gap: 20px; /* Space between social icons */
        }

        .contact .social a {
            color: white; /* Ensure icons are visible on the dark background */
            font-size: 30px; /* Make the icons larger */
            transition: color 0.3s ease;
        }

        .contact .social a:hover {
            color: #007bff; /* Change color on hover */
        }

        .content2 {
            flex: 1; /* Allow this section to grow and shrink */
            min-width: 280px; /* Minimum width before wrapping */
            text-align: left; /* Align text left within its flex item */
            padding: 10px 0;
        }

        .content2 span {
            display: block; /* Make each contact detail a new line */
            margin-bottom: 10px; /* Space between contact details */
            font-size: 18px;
        }

        .content2 i {
            padding-right: 15px; /* Space between icon and text */
        }

        /* Google Map Container and Iframe */
        .map-container {
            flex: 1 1 500px; /* Allows the map to grow, shrink, and have a base width of 500px */
            min-width: 300px; /* Minimum width for the map */
            height: 450px; /* Fixed height for the map, adjust as needed */
            overflow: hidden; /* Ensures no scrollbars if content overflows */
            border-radius: 8px; /* Optional: rounded corners for the map */
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Optional: subtle shadow */
        }

        .map-container iframe {
            width: 100%; /* Make the iframe fill its container */
            height: 100%; /* Make the iframe fill its container */
            border: none; /* Remove default iframe border */
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .contact {
                flex-direction: column; /* Stack items vertically on small screens */
                align-items: center; /* Center items when stacked */
            }

            .contact .social,
            .contact .content2 {
                text-align: center; /* Center text/icons for better mobile layout */
                min-width: unset; /* Remove min-width to allow full width */
                width: 100%; /* Take full width */
            }

            .contact .social div { /* Adjust social icons for mobile */
                justify-content: center; /* Center icons horizontally */
                width: 100%;
            }

            .map-container {
                width: 100%; /* Full width on smaller screens */
                height: 300px; /* Adjust map height for mobile */
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <img src="logo1.jpeg" class="logo" alt="Logo">
        <ul>
            <li><a href="#Home1">Home</a></li>
            <li class="dropdown">
                <a href="javascript:void(0);" onclick="toggleDropdown()" class="dropbtn">House Plans</a>
                <div class="dropdown-content" id="housePlansDropdown">
                    <a href="Bedroom.html">Bed Room</a>
                    <a href="kitchen.html">Kitchen</a>
                    <a href="livingroom.html">Living Room</a>
                    <a href="Barthroom.html">Barth Room</a>
                    <a href="fullhouse.html">Full house</a>
                </div>
            </li>
            <li><a href="#Services1">Services</a></li>
            <li><a href="#About1">About Us</a></li>
            <li><a href="#Team1">Team</a></li>
            <li><a href="#Contact1">Contact</a></li>
            <li><a href="#" onclick="openSignUpForm(); return false;">Sign Up</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="slider">
            <h1 class="page-title">MAINE MADE & FURNICHURS</h1>

            <p class="pagecontent">
                Creating a home that's both beautiful and functional can feel overwhelming. At MAINE MADE & FURNICHERS, we simplify the process by connecting you with expert insights, practical guides, and innovative solutions for every aspect of home design. From space planning and material selection to finding the right professionals, we're here to empower you to make informed decisions and bring your vision to life with confidence.
            </p>

            <button class="watch-more-btn" onclick="location.href='index5.html'">WATCH MORE</button>
            <button class="subscribe-btn" onclick="location.href='https://www.youtube.com/@Nick_Lewis/videos'">SUBSCRIBE</button>

            <div class="slides">
                <img src="1.jpg" alt="Slide 1">
                <img src="5.jpeg" alt="Slide 2">
                <img src="jj.jpg" alt="Slide 3">
            </div>
        </div>
    </div>

    <div class="form-overlay" id="signUpFormOverlay">
        <div class="form-section">
            <span class="close-button" onclick="closeSignUpForm()">×</span>
            <div class="form-box">
                <h2>Sign Up</h2>
                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <form method="POST" onsubmit="return validateForm()">
                    <label for="name">User Name</label>
                    <input type="text" id="name" name="name" required>

                    <label for="no">Telephone</label>
                    <input type="text" id="no" name="no" required>

                    <label for="em">Email</label>
                    <input type="email" id="em" name="em" required>

                    <label for="pa">Password</label>
                    <input type="password" id="pa" name="pa" required>

                    <button type="submit">Submit</button>
                    <a href="login.php">Already have an account?</a>
                </form>
            </div>
        </div>
    </div>

    <section class="Home1" id="Home1"></section>
    <section class="houseplan" id="houseplan"></section>

    <section class="Services1" id="Services1">
        <h1>Our Services</h1>
        <p class="section-intro">
            At MAINE MADE & FURNITURES, we transform visions into extraordinary living spaces. Our expert interior design services are tailored to your unique needs, creating beautiful, functional homes across Sri Lanka.
        </p>

        <div class="service-section">
            <h2>Our Design Process</h2>
            <p>We guide you through a transparent, step-by-step journey from concept to completion:</p>
            <ul>
                <li>**1. Discovery & Consultation:** Understanding your style, budget, and project scope.</li>
                <li>**2. Concept & Vision:** Presenting mood boards, sketches, and initial design ideas.</li>
                <li>**3. Detailed Design:** Developing precise plans, layouts, and selecting all materials.</li>
                <li>**4. Procurement & Management:** Sourcing items and coordinating with trusted local trades.</li>
                <li>**5. Installation & Styling:** Managing delivery and perfecting every detail for the "reveal."</li>
                <li>**6. Post-Project Support:** Ensuring your complete satisfaction after completion.</li>
            </ul>
        </div>

        <div class="service-section">
            <h2>Our Offerings</h2>
            <div class="service-list">
                <div class="service-item">
                    <h3>Full-Service Interior Design</h3>
                    <p>Complete design solutions for entire homes or multiple rooms, from concept to project management. Ideal for a seamless, cohesive transformation.</p>
                </div>
                <div class="service-item">
                    <h3>Room-by-Room Design</h3>
                    <p>Expert design for individual spaces like your living room, kitchen, or bedroom. Perfect for targeted refreshes or enhancing specific areas.</p>
                </div>
                <div class="service-item">
                    <h3>Design Consultations</h3>
                    <p>Hourly or fixed-fee sessions for professional advice on styling, color schemes, material selection, and tackling design dilemmas.</p>
                </div>
                <div class="service-item">
                    <h3>Space Planning & Layout</h3>
                    <p>Optimizing your space for functionality and flow, including furniture arrangement and maximizing tricky layouts.</p>
                </div>
                <div class="service-item">
                    <h3>Custom Millwork & Furnishings</h3>
                    <p>Designing bespoke cabinetry, built-ins, and sourcing unique furniture to perfectly fit your space and style.</p>
                </div>
                <div class="service-item">
                    <h3>Local Material Integration</h3>
                    <p>Incorporating authentic Sri Lankan timbers, stones, and textiles for a unique, climate-appropriate aesthetic.</p>
                </div>
            </div>
        </div>

        <div class="why-us-section">
            <h2>Why Choose MAINE MADE & FURNITURES?</h2>
            <ul>
                <li>**Local Expertise:** Deep understanding of Sri Lankan materials, craftsmanship, and climate-appropriate design.</li>
                <li>**Personalized Approach:** Every design is uniquely tailored to your lifestyle and preferences.</li>
                <li>**Attention to Detail:** Meticulous planning and execution for flawless results.</li>
                <li>**Seamless Experience:** We make the design journey easy and enjoyable.</li>
                <li>**Proven Track Record:** Years of experience delivering beautiful, functional spaces.</li>
            </ul>
        </div>

        <div class="cta-section">
            <h2>Ready to Begin Your Design Journey?</h2>
            <p>Let's create a space that inspires you. Reach out today for a personalized consultation!</p>
            <a href="contact.html" class="button primary-cta">Schedule a Discovery Call</a>
            <a href="portfolio.html" class="button secondary-cta">View Our Portfolio</a>
        </div>
    </section>

    <section class="About1" id="About1">
        <h1> Our Vision</h1>
        <p>To empower every Sri Lankan household to experience the joy and functionality of beautifully designed spaces, setting new benchmarks for quality and accessible home <br>

            Why it works: "Empower every household" suggests accessibility, "joy and functionality" highlights practical benefits, and "new benchmarks" speaks to elevating industry standards.</p>
        <h1> Our Mision</h1>
        <p>
            At MAINE MADE & FURNITURES, our mission is to collaborate closely with our clients in Sri Lanka to deliver exceptional, tailor-made interior design and furnishing solutions that blend aesthetic elegance with practical functionality, creating spaces they truly love. <br>

            Why it works: "Collaborate closely" emphasizes partnership, "exceptional, tailor-made" highlights quality and customization, "aesthetic elegance with practical functionality" covers both beauty and utility, and "spaces they truly love" focuses on the emotional outcome.
        </p>
    </section>
    <section class="Team1" id="Team1">
      <p>Meet the dedicated minds behind our designs! Our team is a vibrant collective of passionate architects, skilled engineers,
        and meticulous designers, each bringing unique expertise and a shared commitment to excellence. We believe in the power of
        collaboration, blending innovative ideas with practical solutions to transform your visions into tangible, beautiful, and lasting spaces. Together,
        we're here to guide you through every step of your project, ensuring a seamless and inspiring journey from concept to completion.</p>
        <h1>Our Team</h1>
        <div class="Team">
            <div class="box">
                <img src="AR.jpg" alt="">
                <h3>Architect</h3>
            </div>
            <div class="box">
                <img src="ID.jpg" alt="">
                <h3>Interior Designer</h3>
            </div>
            <div class="box">
                <img src="CI.jpg" alt="">
                <h3>civil ingineer</h3>
            </div>
            <div class="box">
                <img src="pasi.jpg" alt="">
                <h3>Structural Engineer</h3>
            </div>
            <div class="box">
                <img src="QS.jpg" alt="">
                <h3>Quantity Surveyor (QS)</h3>
            </div>
            <div class="box">
                <img src="PM.jpg" alt="">
                <h3>Project Manager/Site Supervisor</h3>
            </div>
            <div class="box">
                <img src="Lr.webp" alt="">
                <h3>Landscape Architect/Designer</h3>
            </div>

        </div>
    </section>
    <section class="Contact1" id="Contact1">
        <h1>Contact Us</h1>
        <div class="contact">
            <div class="social">
                <h2>Social Media</h2>
                <div> <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#"><i class="fa-brands fa-square-instagram"></i></a>
                </div>
            </div>
            <div class="content2">
                <span><i class="fa-solid fa-location-dot"></i>H.P pasindu lakshan, <br>Hendiyagala sandalankawa</span><br>
                <span><i class="fa-solid fa-phone"></i>076-9634154</span><br>
                <span><i class="fa-solid fa-envelope"></i>coffee@shop.com</span>
            </div>

            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.08386341492!2d79.94819777500161!3d7.067303092928096!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2fb9f6c7c4c1d%3A0xb68b200b3e60c874!2sPasindu%20heshan!5e0!3m2!1sen!2slk!4v1717750500000!5m2!1sen!2slk"
                    width="600"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            </div>
    </section>

    <script>
        function openSignUpForm() {
            document.getElementById('signUpFormOverlay').classList.add('active');
        }

        function closeSignUpForm() {
            document.getElementById('signUpFormOverlay').classList.remove('active');
        }

        // Close the modal and dropdown if the user clicks outside of them
        window.onclick = function(event) {
            const overlay = document.getElementById('signUpFormOverlay');
            if (event.target == overlay) {
                overlay.classList.remove('active');
            }

            // Close the dropdown if the user clicks outside of the dropdown button or content
            if (!event.target.matches('.dropbtn')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        function validateForm() {
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('no').value.trim();
            const email = document.getElementById('em').value.trim();
            const password = document.getElementById('pa').value;

            // Basic client-side validation
            if (name === "" || phone === "" || email === "" || password === "") {
                alert("All fields are required.");
                return false;
            }

            const phonePattern = /^[0-9]{10}$/;
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (!phone.match(phonePattern)) {
                alert("Please enter a valid 10-digit phone number.");
                return false;
            }

            if (!email.match(emailPattern)) {
                alert("Please enter a valid email address (e.g., example@domain.com).");
                return false;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters long.");
                return false;
            }

            return true;
        }

        // Function to toggle the dropdown visibility
        function toggleDropdown() {
            document.getElementById("housePlansDropdown").classList.toggle("show");
        }
    </script>

</body>
</html>