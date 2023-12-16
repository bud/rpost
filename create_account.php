<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST["first"];
    $last_name = $_POST["last"];
    $email = $_POST["email"];
    $rcsid = $_POST["rcsid"];
    $passcode = $_POST["passcode"];
    $bio = $_POST["bio"];

    //We need to hash our given password for extra security
    $passcode = password_hash($passcode, PASSWORD_DEFAULT);

    // Use provided URL or uploaded file URL or use a default image
    $defaultImageUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQN1_yYuKCCC3uu-36xM7XntMDF1cku1S_oNqP2ntw&s'; // Replace with the actual path to your default image
    $pic = $_POST["profilePicUrl"] ?? $defaultImageUrl;

    include("./pages/php/db_connection.php");

    // Check if an account with the same email or rcsid already exists
    $check_query = "SELECT rcsid FROM account_details WHERE email = '$email' OR rcsid = '$rcsid'";
    $result = $conn->query($check_query);

    //Checking to make sure username has only valid characters
    $validUsername = true;
    $numLetters = 0;
    $numDigits = 0;
    for ($i = 0; $i < sizeof($rcsid); $i++) {
        if (ctype_alpha($rcsid[$i])) { $numLetters++; }
        else if (ctype_digit($rcsid[$i])) { $numDigits++; }
    }

    if (!ctype_alnum($rcsid) || $numLetters > 6 || $numLetters + $numDigits != sizeof($rcsid)) {
        $validUsername = false;
    }

    if ($result->num_rows > 0) {
        // Account already exists
        echo "An account with this email or rcsid already exists. Please log in.";
    } else if (!$validUsername) {
        echo "Your RCSID is invalid. Please try again.";
    } else {
        // If no URL is provided, check for an uploaded file URL or use the default image URL
        if (empty($pic)) {
            $pic = $_POST['profilePicUrl'] ?? $defaultImageUrl;
        }

        // Insert data into the database
        $insert_query = "INSERT INTO account_details (first_name, last_name, email, rcsid, passcode, bio, profile_picture)
                         VALUES ('$first_name', '$last_name', '$email', '$rcsid', '$passcode', '$bio', '$pic')";

        $insert_query_one = "INSERT INTO users (first_name, last_name, rcsid)
                             VALUES ('$first_name', '$last_name', '$rcsid')";

        // Start transaction
        $conn->begin_transaction();

        try {
            if ($conn->query($insert_query) === TRUE && $conn->query($insert_query_one) === TRUE) {
                $conn->commit();
                $_SESSION['rcsid'] = $rcsid;
                // Redirect to the main homepage
                header("Location: ./pages/homepage.php");
                exit();
            } else {
                throw new Exception("Error: " . $conn->error);
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo $e->getMessage();
        }
    }

    // Close the database connection
    $conn->close();
}
?>