<?php
session_start();
include("../php/db_connection.php");

if (!isset($_SESSION['rcsid'])) {
    // Handle the case where the user is not logged in
    exit('User not logged in');
}

$currentUserRcsid = $_SESSION['rcsid'];

// Prepare the SQL query
$query = "SELECT ad.first_name, ad.last_name, ad.rcsid, ad.bio, ad.profile_picture 
          FROM account_details ad
          LEFT JOIN followers f ON ad.rcsid = f.following_id AND f.follower_id = ?
          WHERE f.following_id IS NULL AND ad.rcsid != ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $currentUserRcsid, $currentUserRcsid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<h1 style="text-align: center;">Connect with People!</h1>';
    echo '<hr>';    
    while ($user = $result->fetch_assoc()) {

        // Default image URL
        $profileImageUrl = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : "https://static.vecteezy.com/system/resources/thumbnails/009/734/564/small/default-avatar-profile-icon-of-social-media-user-vector.jpg";

        echo '<div class="tweet-wrap">';
        echo '<div class="tweet-header user-card">';
        
        // User's profile picture
        echo '<img src="' . $profileImageUrl . '" alt="Avatar" class="avator">';
        
        // Display the user's name, RCSID, and bio
        echo '<div class="tweet-header-info">';
        echo '<span>' . htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) . '</span> ';
        echo '<span>@' . htmlspecialchars($user['rcsid']) . '</span><br>';
        echo '<span>' . htmlspecialchars($user['bio']) . '</span>';
        echo '</div>';

        // Follow button
        echo '<button class="follow-btn" data-rcsid="' . htmlspecialchars($user['rcsid']) . '">Follow</button>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>No new users to follow.</p>";
}

$stmt->close();
$conn->close();
?>