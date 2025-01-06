<?php
include "db.php"; // Include your database connection

session_start(); // Start the session

// Check if the user is logged in (optional, depending on your application)
if (isset($_SESSION['user_id'])) {
    // Unset all of the session variables
    $_SESSION = [];

    // If it's desired to kill the session, also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session
    session_destroy();
}

// Redirect to index.php
header("Location: index.php");
exit;
?>