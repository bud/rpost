function sendTweetData(tweetText, imageUrl) {
    console.log("Sending tweet data...");
    var xhr = new XMLHttpRequest();
    xhr.open('POST', './tweet/posttweet.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        // Handle response
        if (xhr.status === 200) {
            console.log("Tweet posted successfully");
            window.location.href = "homepage.php"; // Redirect back to your main page
        } else {
            console.error("Error posting tweet:", xhr.responseText);
        }
    };
    console.log("end of the function");
    xhr.send('tweetText=' + encodeURIComponent(tweetText) + '&imageUrl=' + encodeURIComponent(imageUrl));
}