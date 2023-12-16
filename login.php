<?php
// Start a session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("./pages/php/db_connection.php");

    // Get values from the form
    $rcsid = $_POST["rcsid"];
    $passcode = $_POST["passcode"];

    $query = "SELECT passcode FROM account_details WHERE rcsid = '$rcsid'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // check that the hashed password matches
        if (password_verify($passcode, $row['passcode'])) {
            // Store user information in the session
            $_SESSION['rcsid'] = $rcsid;
            
            // Redirect 
            header("Location: ./pages/homepage.php");
            exit();
        } else {
            // display an error message
            echo "Invalid email or RCSID. Please check your input.";
            header("Location: ./index.html");
        } 
    } else {
        header("Location: ./index.html");
    }

    $conn->close();
}
?>