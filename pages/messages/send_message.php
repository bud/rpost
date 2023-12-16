<?php
session_start();

if (!isset($_SESSION['rcsid'])) {
    header("Location: ../login/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $senderRcsid = $_SESSION['rcsid'];
    $receiverRcsid = $_POST['receiver'];
    $message = $_POST['message'];

    echo 'sender: ' . $senderRcsid;
    echo '<br> reciever: ' . $receiverRcsid;
    echo '<br> message: ' . $message;

    include("../php/db_connection.php");

    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) 
            VALUES ('$senderRcsid', '$receiverRcsid', '$message')";

    if (mysqli_query($conn, $sql)) {
         echo "Message sent successfully.";
        header("Location: ../homepage.php");
    } else {
         echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Invalid request method.";
}
?>