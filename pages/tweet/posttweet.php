<?php
// Start a session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['rcsid'])) {
    header("Location: ../../index.html");
    exit();
}

// Include the database connection file
include("../php/db_connection.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assign variables from POST data
    $tweetText = $_POST['tweetText'] ?? '';
    $imageURL = $_POST['imageUrl'] ?? ''; 
    $rcsid = $_SESSION['rcsid']; 

    // Sanitize input
    $tweetText = $conn->real_escape_string($tweetText);
    $imageURL = $conn->real_escape_string($imageURL);

    // Prepare SQL statement to insert tweet
    $stmt = $conn->prepare("INSERT INTO Tweets (tweet_text, image_url, rcsid) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $tweetText, $imageURL, $rcsid);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: ../homepage.php"); 
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    header("Location: ../../index.html");
    exit();
}

// Close the database connection
$conn->close();
?>