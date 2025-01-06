<?php
include "db.php"; // Ensure this file is correctly linked

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO users (name, email, number, address, password) VALUES ('$name', '$email', '$number', '$address', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        // Registration successful, set session variables
        $_SESSION['user_id'] = mysqli_insert_id($conn); // Get the last inserted user ID
        $_SESSION['username'] = $name; // Optionally store the username

        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css"> <!-- Corrected link attribute -->
</head>
<body>
    <div>
        <h1>Register to</h1>
    </div>
    <form action="" method="POST"> <!-- Set action and method -->
        <table>
            <h1>Vinted</h1>
            <tr>
                <td>Name</td>
                <td><input type="text" name="name" placeholder="Paul" required></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><input type="email" name="email" placeholder="******@gmail.com" required></td>
            </tr>
            <tr>
                <td>Number</td>
                <td><input type="text" name="number" placeholder="+237 *********" required></td> <!-- Changed to text for international numbers -->
            </tr>
            <tr>
                <td>Address</td>
                <td><input type="text" name="address" placeholder="Douala" required></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input type="password" name="password" placeholder="*******" required></td>
            </tr>
        </table>
        <button type="submit">Register</button> <!-- Moved button inside the form -->
    </form>
</body>
</html>