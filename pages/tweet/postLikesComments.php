<?php
// Start a session
session_start();
// Check if the user is logged in
if (!isset($_SESSION['rcsid'])) {
    header("Location: ../login/login.html");
    exit();
}


// Include the database connection file
include("../php/db_connection.php");


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Assign variables from POST data and sanitize input
    $postID = (int)$_POST['postID'] ?? '';
    $mode = $_POST['mode'] ?? '';
    $comment = $_POST['comment'] ?? ''; 
    $rcsid = $_SESSION['rcsid'];

    $mode = $conn->real_escape_string($mode);
    $comment = $conn->real_escape_string($comment);

    
    // Using logic to determine what kind of SQL statement to use
    // Need to go through 3 columns for comments and only 1 for likes
    if ($mode == "c") { $mode = "commentlist"; $repeats = 3; }
    else if ($mode == "l") { $mode = "likelist"; $repeats = 1;}

    $result = "";
    $resultUser = "";
    $resultTime = "";
    
    $sql = "SELECT $mode FROM Tweets WHERE tweet_id = ?";
    $sqlUser = "SELECT commentuser FROM Tweets WHERE tweet_id = ?";
    $sqlTime = "SELECT commenttime FROM Tweets WHERE tweet_id = ?";

    
    $stmt = $conn->prepare($sql);
    $stmtUser = $conn->prepare($sqlUser);
    $stmtTime = $conn->prepare($sqlTime);
    
    if (!$stmt) {
        error_log('Error in loading the statement: ' . $stmt->error);
        // Handle the error, e.g., redirect with an error message
        header("Location: ../homepage.php?error=preparation_failed");
    }

    
    $stmt->bind_param("i", $postID);    
    $stmt->bind_result($result);
    $stmtUser->bind_param("i", $postID);
    $stmtUser->bind_result($resultUser);
    $stmtTime->bind_param("i", $postID);    
    $stmtTime->bind_result($resultTime);

    
    $valid = $stmt->execute();
        if ($valid) {
            $stmt->fetch();
            $stmt->close();
            $stmtUser->execute();
            $stmtUser->fetch();
            $stmtUser->close();
            $stmtTime->execute();
            $stmtTime->fetch();
            $stmtTime->close();
        } else {
            error_log('Error in executing the statement: ' . $conn->error);
            // Handle the error, e.g., redirect with an error message
            header("Location: ../homepage.php?error=execution_failed");
        }

    $prevSize = 0;
    if (is_null($result)) { $newTempString = array(); }
    else { 
        if ($mode == "likelist") { $newTempString = explode(",", $result); }
        else if ($mode == "commentlist") { $newTempString = explode("ª", $result); $prevSize = sizeof($newTempString); }
    }


    $addToList = false;
    $newValue = "";
    
//==========================FOR IF CHANGING LIKES===============================       
    if ($mode == "likelist") {
        if (!in_array($rcsid,$newTempString)) { $addToList = true; }

        if ($addToList) {
            array_push($newTempString,$rcsid);
        } else {
            $key = array_search($rcsid,$newTempString);
            unset($newTempString[$key]);
        }
        if ($mode == "likelist") { $newValue = implode(",", $newTempString); }
        else if ($mode == "commentlist") { $newValue = implode("ª", $newTempString); }
        
        if (sizeof($newTempString) <= 0) { $newValue = NULL; }

        $sql = "UPDATE Tweets SET $mode = ? WHERE tweet_id = ?"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newValue, $postID);

        if ($stmt->execute()) {
            if ($mode == "commentlist") { updateComments($result); }
            header("Location: ../homepage.php"); 
            exit();
        } else {
            error_log('Error in executing the statement: ' . $conn->error);
            // Handle the error, e.g., redirect with an error message
            header("Location: ../homepage.php?error=execution_failed");
        }

        $stmt->close();
        header("Location: ../homepage.php"); 
        exit();


//==========================FOR IF CHANGING COMMENTS============================   
    } else if ($mode == "commentlist") {
        $newValueComment = $newTempString;

        if (is_null($resultUser)) { $newValueUser = array(); }
        else { $newValueUser = explode(",", $resultUser); }

        if (is_null($resultTime)) { $newValueTime = array(); }
        else { $newValueTime = explode(",", $resultTime); }

        array_push($newValueUser, $rcsid);
        date_default_timezone_set("America/New_York");
        $dateTime = date("m/d/Y") . " " . date("H:i:s");
        array_push($newValueTime, $dateTime);
        array_push($newValueComment, $comment);

        //A workaround for a duplication glitch
        for ($i = 0; $i < sizeof($newValueComment) - $prevSize - 1; $i++) {
            array_pop($newValueUser);
            array_pop($newValueTime);
            array_pop($newValueComment);
        }


        $value1 = implode(",", $newValueTime);
        $value2 = implode(",", $newValueUser);
        $value3 = implode("ª", $newValueComment);

        $value1 = $conn->real_escape_string($value1);
        $value2 = $conn->real_escape_string($value2);
        $value3 = $conn->real_escape_string($value3);
    

        $sql = "UPDATE Tweets SET commenttime = ?, commentuser = ?, commentlist = ? WHERE tweet_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $value1, $value2, $value3, $postID);

        if (!$stmt->execute()) {
            error_log('Error in loading the statement: ' . $conn->error);
            // Handle the error, e.g., redirect with an error message
            header("Location: ../homepage.php?error=preparation_failed");
        }

        header("Location: ../homepage.php"); 
        exit();
    }
    



} else {
    header("Location: ../../index.php");
    exit();
}

// Close the database connection
$conn->close();
?>