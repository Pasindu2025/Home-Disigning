<?php
session_start();
require 'db.php';

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header("Location: welcome.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        *{
            box-sizing: border-box;
            
        }
        body {
            font-family: Arial, sans-serif;
            background-image: url('5.jpeg'); /* your image path here */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed; /* Makes the background fixed */
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Prevents horizontal scrolling */
            overflow-y: hidden;
        }

        /* --- Navbar Styles (Keeping them as is) --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            padding: 0 0px;
            color: white;
            width: 100%;
            margin: auto;
        }

        .navbar img.logo {
            cursor: pointer;
            border-radius: 50%;
            width: 60px;
            padding: 0 4px;
            left: 100px;
            position: relative;
        }

        .navbar ul li {
            display: inline-block;
            list-style: none;
            margin: 0 20px;
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
        }
        /* --- End Navbar Styles --- */

        /* --- Main Content Area (Keeping it as is for layout) --- */
        .form-container {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: calc(100vh - 60px); /* Adjust for navbar height */
            position: relative; /* For title positioning */
            flex-direction: column; /* Stack items vertically */
        }

        .form-container h1 {
            border: 1px solid black;
            padding: 15px 30px; /* Adjusted padding */
            text-align: center;
            color: white;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            font-size: 40px; /* Slightly smaller font for title */
            margin-bottom: 20px; /* Space between title and form */
            margin-top: -100px; /* Pull title up if needed to fit */
            width: fit-content; /* Make the box fit content */
            max-width: 90%; /* Ensure it doesn't overflow on smaller screens */
        }
        /* --- End Main Content Area --- */

        /* --- Form Box Styles (Key changes for smaller size) --- */
        .form-box {
            background-color:  #fefefe;
            padding: 15px; /* Reduced padding */
            border-radius: 10px;
            width: 250px; /* Significantly reduced width */
            height:310px;
            border: 2px solid black;
            box-shadow: 0 0 15px rgba(0,0,0,0.5); /* Added shadow for better appearance */
            margin-top: 0; /* Reset margin top */
            margin-bottom: 10px; /* Maintain small bottom margin */
            display: flex; /* Use flexbox for internal layout */
            flex-direction: column; /* Stack form elements vertically */
            align-items: center; /* Center items horizontally within the box */
        }

        

        .form-box h2 {
            color: black; /* Made heading white for contrast */
            margin-bottom: 15px; /* Adjust margin */
            font-size: 20px; /* Slightly smaller heading */
        }

        .form-box label {
            color: black; /* Made labels white for contrast */
            font-size: 14px; /* Smaller label font */
            margin-bottom: 5px;
            display: block; /* Ensure label is on its own line */
            align-self: flex-start; /* Align labels to the left within the form box */
        }
        
        .form-box input {
            width: 100%;
            padding: 8px; /* Reduced padding */
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 10px; /* Reduced margin */
            font-size: 14px; /* Smaller input font */
        }

        .form-box button {
            width: 100%;
            padding: 8px; /* Reduced padding */
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px; /* Slightly smaller button text */
        }

        .form-box button:hover {
            background-color: #45a049;
        }

        .form-box a {
            text-decoration: none;
            color:black; /* Light blue for sign-up link */
            font-size: 12px; /* Smaller link font */
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .error {
            color: #FF6347; /* Tomato color for errors */
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="banner">
        <div class="navbar">
            <img src="logo1.jpeg" class="logo" alt="Logo">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Production</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Contact Us</a></li>
               
            </ul>
        </div>

        <div class="form-container">
            <h1>MAINE MADE & FURNICHURS</h1>
            <div class="form-box">
                
                <h2>Sign In</h2>

                <?php if (!empty($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST" onsubmit="return validateForm()">
                    <div>
                        <label for="username">User Name</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="submit">Login</button>
                    <a href="index.php">Don't have an account? Sign up</a>
                </form>
            </div>
        </div>
    </div>

<script>
    function validateForm() {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if (username === "") {
            alert("Username is required.");
            return false;
        }

        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }

        return true;
    }
</script>

</body>
</html>