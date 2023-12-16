<?php

if (!isset($_SESSION['rcsid'])) {
  header("Location: ../index.html");
  exit();
}

include("./php/db_connection.php");
$rcsid = $_SESSION['rcsid'];

// Fetch user details from the database
$query = "SELECT first_name, last_name, profile_picture FROM account_details WHERE rcsid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rcsid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Default image URL in case the user doesn't have a profile picture
$defaultImageUrl = "https://static.vecteezy.com/system/resources/thumbnails/009/734/564/small/default-avatar-profile-icon-of-social-media-user-vector.jpg";
$profileImageUrl = $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : $defaultImageUrl;

// renders the navbar
function renderNavBar($profileImageUrl,$rcsid){
  echo' <div class = "first">
    <div id="nav-bar">
      <input id="nav-toggle" type="checkbox"/>
      <div id="nav-header"><a id="nav-title"target="_blank">RPOST</a>
        <hr/>
      </div>
      <div id="nav-content">
        <div class="nav-button" data-nav="Home"><i class="fas fa-palette"></i><span>Home</span></div>
        <div class="nav-button show-modal" data-nav="Post"><i class="fas fa-users"></i><span>Post</span></div>
        <hr/>
        <div class="nav-button" data-nav="Following"><i class="fas fa-heart"></i><span>Connect</span></div>
        <div class="nav-button" data-nav="News"><i class="fas fa-chart-line"></i><span>News</span></div>
        <hr/>
        <div class="nav-button" data-nav="Help"><i class="fas fa-robot"></i><span>Chatbot</span></div>
        <div class="nav-button" data-nav="Profile"><i class="fas fa-magic"></i><span>Profile</span></div>

        <div id="nav-content-highlight"></div>
      </div>
      <input id="nav-footer-toggle" type="checkbox"/>
      <div id="nav-footer">
        <div id="nav-footer-heading">
          <div id="nav-footer-avatar"><img src="' . $profileImageUrl . '"/></div>
          <div id="nav-footer-titlebox"><a id="nav-footer-title">' . $rcsid . '</a><span id="nav-footer-subtitle">User</span></div>
          <label for="nav-footer-toggle"><i class="fas fa-caret-up"></i></label>
        </div>
        <div id="nav-footer-content">
          <a href="./php/logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>';
}

// renders all the files needed for the head section
function renderHeadSection(){
  echo '<head>
  <meta charset="UTF-8">
  <title>RPOST</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./css/index.css">
  <link rel="stylesheet" href="./css/info.css">
  <link rel="stylesheet" href="./css/popup.css">
  <link rel=stylesheet hred="./css/followers.css">
  <link rel="stylesheet" href="./css/navbar.css">
  <link rel=stylesheet href="./css/messages.css">
  <link rel=stylesheet href="./css/news.css">
  <link rel="stylesheet" href="./css/tweets.css">
  <link rel="stylesheet" href="./css/box.css">
</head>';
}

// information bar on the right
function renderInfoBar(){
  echo '<div class="last">
          <section class="left-border">
          <div id="bwOutput"></div>
          <script type="text/javascript" src="https://events.rpi.edu/approots/calfeedrsrc.MainCampus/default/default/theme/javascript/eventListWidget.js"> </script>
          <script type="text/javascript" src="https://events.rpi.edu/feeder/main/eventsFeed.do?f=y&sort=dtstart.utc:asc&fexpr=(categories.href!=%22/public/.bedework/categories/Ongoing%22)%20and%20(entity_type=%22event%22%20or%20entity_type=%22todo%22)&skinName=list-json&setappvar=objName(bwObject)&count=5"> </script>
          <script type="text/javascript">
          var bwJsWidgetOptions = {
          title: "Upcoming Events",
          showTitle: true,
          displayDescription: false,
          calendarServer: "https://events.rpi.edu",
          resourcesRoot: "https://events.rpi.edu/approots/calfeedrsrc.MainCampus/default/default/theme",
          limitList: false,
          limit: 10,
          displayStartDateOnlyInList: true,
          displayTimeInList: true,
          displayLocationInList: true,
          listMode: "byTitle",
          };
          insertBwEvents("bwOutput",bwObject,bwJsWidgetOptions);
          </script>
        </section>  
      </div>
    ';
  }

  