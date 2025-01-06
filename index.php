<?php
include "db.php"; // Include database connection

session_start(); // Start a session to store user information

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // We'll check this against the hashed password in the database

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct; set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to dashboard or welcome page
            header("Location: dashboard.php");
            exit;
        } else {
            // Password is incorrect
            $error = "Invalid email or password.";
        }
    } else {
        // No user found
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vinted</title>
    <link rel="stylesheet" href="fontawesome-free-6.4.0-web/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container{
            background-color:#077480;
            color:white;
        }
    </style>
</head>
<body>
    


        <form action="" method="POST"> <!-- Specify the action and method -->
        <center>
        <div class="container">
            <h1>Welcome To<br>
            <img src="images/images.png" alt="Vinted Logo"></h1>
        </div>
        <h2>Sign In to Vinted</h2>

            <div>
                <table>
                    <tr>
                        <td>Email</td>
                        <td><input type="email" name="email" placeholder="your email" required></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td><input type="password" name="password" placeholder="*******" required></td>
                    </tr>
                </table>
                <p>Do you have an account? | <a href="register.php">Register</a></p>

                <button type="submit">Login</button> <!-- Remove the <a> tag from the button -->
            </div>
        </form>

        <?php
        // Display error message if login fails
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>
    </center>
</body>
</html>