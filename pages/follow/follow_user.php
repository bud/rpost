<?php
session_start();
include("../php/db_connection.php");

if (!isset($_SESSION['rcsid'])) {
    // Handle the case where the user is not logged in
    exit('User not logged in');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['follow'])) {
    $follower_id = $_SESSION['rcsid']; //  logged-in user
    $following_id = $_POST['follow']; //  user to follow

    // Log the IDs
    error_log("Follower ID: " . $follower_id);
    error_log("Following ID: " . $following_id);

    // Insert follow relationship into database
    $stmt = $conn->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $follower_id, $following_id);

    if ($stmt->execute()) {
        echo "Followed successfully";
    } else {
        echo "Error in place: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>