<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['rcsid'])) {
    echo "User not logged in";
    exit();
}

// Check if a receiver id is there
if (!isset($_GET['receiver'])) {
    echo "No receiver specified";
    exit();
}

include("../php/db_connection.php");

$senderRcsid = $_SESSION['rcsid']; // Sender RCSID 
$receiverRcsid = $_GET['receiver']; // Receiver RCSID

// SQL to fetch messages between the sender and receiver
$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $senderRcsid, $receiverRcsid, $receiverRcsid, $senderRcsid);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are messages
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine who sent the message for styling
        if ($row['sender_id'] == $senderRcsid) {
            echo "<div class='message sender'>";
        } else {
            echo "<div class='message receiver'>";
        }

        // Display the message content
        echo htmlspecialchars($row['message_text']);
        echo "<span class='timestamp'>" . htmlspecialchars($row['timestamp']) . "</span>";
        echo "</div>"; // Closing message div
    }
} else {
    echo "<p>No messages found.</p>"; // Message if no conversation exists
}

$stmt->close();
$conn->close();
?>