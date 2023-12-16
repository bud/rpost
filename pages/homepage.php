<?php
// Start a session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['rcsid'])) {
    header("Location: ../index.html");
    exit();
}

include("./php/db_connection.php");
include "./functions.php";

$rcsid = $_SESSION['rcsid'];

$stmt = $conn->prepare("SELECT u.first_name, u.last_name, u.rcsid 
                        FROM users u 
                        WHERE u.rcsid NOT IN (
                            SELECT f.following_id 
                            FROM followers f 
                            WHERE f.follower_id = ?
                        ) AND u.rcsid != ?");

$stmt->bind_param("ss", $rcsid, $rcsid);

$stmt->execute();

$result = $stmt->get_result();

$suggested_users = [];
while ($row = $result->fetch_assoc()) {
    $suggested_users[] = $row;
}

$stmt->close();

$get_details = "SELECT first_name, last_name, profile_picture FROM account_details WHERE rcsid = ?";
$stmt = $conn->prepare($get_details);
$stmt->bind_param("s", $rcsid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstName = htmlspecialchars($row['first_name']);
    $lastName = htmlspecialchars($row['last_name']);
    $profilePictureUrl = htmlspecialchars($row['profile_picture']);

    if (empty($profilePictureUrl)) {
        $profilePictureUrl = "https://static.vecteezy.com/system/resources/thumbnails/009/734/564/small/default-avatar-profile-icon-of-social-media-user-vector.jpg";
    }
    
} else {
    echo "User details not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" >
  <?php renderHeadSection(); ?>
<body>
<div class="container">
  <?php renderNavBar($profilePictureUrl,$rcsid) ?>
  <div class="middle-section"></div>
  <?php renderInfoBar(); ?>
</div>

<script src="jquery-3.7.1.min.js"></script>
<script src="./messages/messages.js"></script>
<script src="./news/news.js"></script>
<script src="./tweet/imageAPI.js"></script>
<script src="./tweet/sendtweet.js"></script>
<script src="./follow/functions.js"></script>
<script src="./tweet/postButtons.js"></script>

<!-- MESSAGE POP UP BOTTOM RIGHT -->
<div class="message-panel">
    <div class="button-container">
        <button id="toggle-button">Messages</button>
        <p id="arrow-icon" class="collapsed"><span>&#8657;</span></p>
    </div>
        
    <div class="message-content">

      <div id="messages-container"></div>
      <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>

      <form id='message-form' action='./messages/send_message.php' target='dummyframe' method='POST' onsubmit='reloadMessages(receiverRcsid);'>

        <input type='hidden' name='sender' value='<?php echo $senderRcsid; ?>'>
        <input type='hidden' name='receiver' id='receiver-id-input' value=''>
        
        <div class="message-input-container">
            <input id='typeMessage' type='text' name='message' placeholder='Type your message...' required>
            <button type='submit' id='sendMessage'>Send</button>
        </div>
      </form>
  </div>
</div>

<!-- HERE WE WILL HANDLE WHEN USER WANTS TO POST -->
<section>
    <div class="overlay"></div>
    <div class="modal-box" style="display: none">
        <button type="button" class="close-btn">X</button>

        <!-- User Profile Pic -->
        <div class="tweet-header">
            <img src="<?php echo $profilePictureUrl; ?>" alt="Avatar" class="avator">
            <div class="tweet-header-info">
                <?php echo $firstName . ' ' . $lastName; ?> <span>@<?php echo $rcsid; ?></span>
                <p>Tweet</p>
            </div>
        </div>
        
        <!-- Tweet Input Form -->
        <form id="tweetForm" class="tweet-form">
            <textarea id="tweetText" name="tweetText" placeholder="What's happening?" required></textarea>

            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file">

            <div class="buttons">
                <button type="submit" class="post-btn">Post</button>
            </div>
        </form>
    </div>
</section>

<!-- POPUP BOX FOR IMAGE ANALYZE -->
<div class="popup-overlay">
  
    <div class="popup-content">
    <span class="close-button">&times;</span>
        
        <div class="left-section">
            <!-- This is the left section for the tweet wrapper -->
            <div class="tweet-wrapper"></div>
        </div>
        <div class="right-section">
        <div class="keywords"></div>
        <div id="newsArticles"></div>

        </div>
    </div>
</div>

<!-- buttons for image ANALYZE -->
<script>
// Define the popupOverlay and popupContent variables in a common scope
const popupOverlay = document.querySelector('.popup-overlay');
const popupContent = document.querySelector('.popup-content');

const leftSection = document.querySelector('.left-section');
const rightSection = document.querySelector('.right-section');
const tweetWrapper = document.querySelector('.tweet-wrapper');

// Close the popup box when the close button is clicked
const closePopupButton = document.querySelector('.close-button');
closePopupButton.addEventListener('click', function() {
    // Clear the content of the left and right sections
    leftSection.innerHTML = '';
    rightSection.innerHTML = '';
    popupOverlay.style.display = 'none'; // Hide the popup overlay
});
</script>

<!-- OPENS THE NAVBAR SELECTED INFO -->
<script>
  // Default load the home page
  document.addEventListener('DOMContentLoaded', function() {
      updateMiddleSection('Home');
  });

  // add event listener to all nav buttons
  document.querySelectorAll('.nav-button').forEach(button => {
    button.addEventListener('click', function() {
      const navType = this.getAttribute('data-nav');
      updateMiddleSection(navType);
    });
  });

  // display the middle content
  function updateMiddleSection(navType) {
    const middleSection = document.querySelector('.middle-section');
    switch(navType) {
      case 'Home':
        console.log("Home");
        fetch('./getHomeContent.php') // Adjust the path as needed
          .then(response => response.text())
          .then(html => {
              middleSection.innerHTML = html;
              connectPostButtons();
              countAnalyzeButtons();
          })
          .catch(error => {
              console.error('Error:', error);
              middleSection.innerHTML = '<p>Error loading content.</p>';
          });

        console.log("Done fetching");
        break;

      case 'News':
        if (newsContentCache){
          middleSection.innerHTML = newsContentCache;
        } else{
          middleSection.innerHTML = '<div id="newsFeed"><h2 id="newsHeader">See whats happening around the world!</h2><div id="searchContainer"><input type="text" id="newsSearchInput" placeholder="Search for news..."><select id="newsCategory"><option value="">Select Category</option><option value="technology">Technology</option><option value="education">Education</option><option value="sports">Sports</option><option value="politics">Politics</option><!-- More categories --></select><button id="searchButton" onClick=handleSearch()>Search</button></div><div id="newsArticles"></div></div>';
        }
        break;
      case 'Following':
        fetch('./follow/getUserSuggestions.php') // Adjust the path as needed
          .then(response => response.text())
          .then(html => {
              middleSection.innerHTML = html;
              attachFollowButtonListeners();
          })
          .catch(error => {
              console.error('Error:', error);
              middleSection.innerHTML = '<p>Error loading user suggestions.</p>';
          });
        break;
      case 'Profile':
        fetch('./profile/profile.php') // Adjust the path as needed
          .then(response => response.text())
          .then(html => {
              middleSection.innerHTML = html;
          })
          .catch(error => {
              console.error('Error:', error);
              middleSection.innerHTML = '<p>Error loading content.</p>';
          });
        middleSection.innerHTML = '<p>This is the Profile content.</p>';
        break;
      case 'Help':
        window.location.href = "./chatbot/chatbot.html";
        break;
    }
  }

</script>

<!-- OPEN TND CLOSE POST PANEL -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const postButton = document.querySelector('.nav-button[data-nav="Post"]');
      const modal = document.querySelector('.modal-box');
      const overlay = document.querySelector('.overlay');
      const closeButton = document.querySelector('.close-btn');

      // Function to show the modal
      function showModal() {
          modal.style.display = 'block';
          overlay.style.display = 'block';
      }

      // Function to hide the modal
      function closeModal() {
          modal.style.display = 'none';
          overlay.style.display = 'none';
      }

      // Event listener for the Post button
      postButton.addEventListener('click', showModal);

      // Event listener for the Close button
      closeButton.addEventListener('click', closeModal);

      // Optional: Close the modal when the overlay is clicked
      overlay.addEventListener('click', closeModal);
  });

</script>

<!-- ADDING FIREBASE AND POSTING A TWEET -->
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.6.0/firebase-app.js";
  import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.6.0/firebase-storage.js";

  const firebaseConfig = {
    apiKey: "AIzaSyCC88arNUwvG0DuWvh40V24BJIs1uOYIuw",
    authDomain: "test-c216b.firebaseapp.com",
    projectId: "test-c216b",
    storageBucket: "test-c216b.appspot.com",
    messagingSenderId: "710684545537",
    appId: "1:710684545537:web:3eeb3bb4ddbf52abf06600"
  };

  const app = initializeApp(firebaseConfig);
  const storage = getStorage(app);

  document.getElementById('tweetForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const file = document.getElementById('file_input').files[0];
    const tweetText = document.getElementById('tweetText').value;

    console.log("file:", file);
    console.log("tweetText:", tweetText);

    if (file) {
        const storageRef = ref(storage, 'images/' + file.name);
        try {
            const snapshot = await uploadBytes(storageRef, file);
            const downloadURL = await getDownloadURL(snapshot.ref);
            sendTweetData(tweetText, downloadURL);
            console.log("tweetText:", tweetText);
            console.log("downloadURL:", downloadURL);
        } catch (error) {
          sendTweetData(tweetText, '');
            console.error("Error uploading file:", error);
        }
    } else {
        sendTweetData(tweetText, '');
    }
});

</script>

</body>
</html>