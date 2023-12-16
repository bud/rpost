<?php
session_start();
include("../php/db_connection.php"); // Adjust the path as needed

if (!isset($_SESSION['rcsid'])) {
    echo "User not logged in";
    exit();
}

$rcsid = $_SESSION['rcsid'];

// Fetch the user's details from the database
$query = "SELECT first_name, last_name, rcsid, profile_picture FROM account_details WHERE rcsid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rcsid);
$stmt->execute();
$result = $stmt->get_result();

// Count the number of followers and following
$followingCountQuery = "SELECT COUNT(*) AS following_count FROM followers WHERE follower_id = ?";
$followersCountQuery = "SELECT COUNT(*) AS followers_count FROM followers WHERE following_id = ?";

$stmtFollowing = $conn->prepare($followingCountQuery);
$stmtFollowing->bind_param("s", $rcsid);
$stmtFollowing->execute();
$followingResult = $stmtFollowing->get_result();
$followingCount = $followingResult->fetch_assoc()['following_count'];

$stmtFollowers = $conn->prepare($followersCountQuery);
$stmtFollowers->bind_param("s", $rcsid);
$stmtFollowers->execute();
$followersResult = $stmtFollowers->get_result();
$followersCount = $followersResult->fetch_assoc()['followers_count'];

// Count the number of tweets made by the user
$queryTweetCount = "SELECT COUNT(*) AS tweet_count FROM Tweets WHERE rcsid = ?";
$stmtTweetCount = $conn->prepare($queryTweetCount);
$stmtTweetCount->bind_param("s", $rcsid);
$stmtTweetCount->execute();
$resultTweetCount = $stmtTweetCount->get_result();
$tweetCount = $resultTweetCount->fetch_assoc()['tweet_count'];

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $profileImageUrl = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : "https://pbs.twimg.com/profile_images/1012717264108318722/9lP-d2yM_400x400.jpg";

    echo '<div class="tweet-wrap">';
    echo '  <div class="tweet-header">';
    echo '    <img src="' . $profileImageUrl . '" alt="Avatar" class="avator">';

    echo '    <div class="tweet-header-info">';
    echo '      <h1>' . htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) . '</h1>';
    echo '      <p>@' . htmlspecialchars($user['rcsid']) . '</p>';
    echo '      <p>Following: ' . $followingCount . ' | Followers: ' . $followersCount . '</p>';
    echo '      <p>Number of Tweets: ' . $tweetCount . '</p>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
} else {
    echo "<p>Profile not found.</p>";
}

$stmt->close();
$stmtFollowing->close();
$stmtFollowers->close();
$conn->close();