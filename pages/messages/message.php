<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/styles.css" />
    <title>Messages | RPost</title>
    <title>Chat</title>
    <style>
        .chat-container {
            margin-top: 100px;
            display: flex;
            width: 90%; 
            height: 90%;
        }

        .left-column {
            flex: 2;
            background-color: #f2f2f2;
            padding: 20px;
            overflow-y: scroll;
        }

        .right-column {
            flex: 5;
            padding: 20px;
        }

        .chat-item {
            margin-bottom: 20px;
            padding: 10px;
            width: fit-content;
            max-width: 50%;
            word-wrap: break-word;
        }

        .user-list {
            list-style: none;
            padding: 0;
        }

        .user-list-item {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .user-list-item:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="topnav">
        <img id="cornerLogo" src="../../css/pic.png" alt="RPost logo">
        <a href="../php/logout.php">Logout</a>
        <a href="../chatbot/chatbot.php">Chatbot</a>
        <a href="../details/dashboard.php">Profile</a>
        <a href="../messages/message.php">Messages</a>
        <a href="../tweets/tweet_form.php">Post</a>
        <a href="../main/homepage.php">Home</a>
    </div>

    <div class="chat-container">
        <div class="left-column">
            <ul class="user-list">
                <?php
                    session_start();

                    if (!isset($_SESSION['rcsid'])) {
                        header("Location: ../login/login.html");
                        exit();
                    }

                    include("../php/db_connection.php");

                    $senderRcsid = $_SESSION['rcsid'];

                    $sql = "SELECT rcsid FROM users WHERE rcsid != '$senderRcsid'";
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $receiverRcsid = $row['rcsid'];
                            echo '<li class="user-list-item" onclick="loadMessages(\'' . $receiverRcsid . '\')">' . $receiverRcsid . '</li>';
                        }
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }

                    mysqli_close($conn);
                ?>
            </ul>
        </div>

        <div class="right-column">
            <h1 id="chatPerson">Select someone to talk to!</h1>
            <div id="chat-messages" class="chat-messages">
                
            </div>
            
            <form id="message-form" class="border" action="send_message.php" method="POST">
                    <input id='typeMessage' type='text' name='message' id='message' placeholder='Type your message...' required>
                    <input type='hidden' name='sender' value='<?php echo $senderRcsid; ?>'>
                    <input type='hidden' name='receiver' id='receiver' value=''>
                    <input id='sendMessage' type='submit' value='Send'>
            </form>
        </div>
    </div>

    <script src="messages.js"></script>
</body>
</html>