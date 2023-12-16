function connectPostButtons() {
    //Liking Posts
    const heartButtons = document.querySelectorAll('.feather-heart');
    heartButtons.forEach(heartButton => {
        heartButton.addEventListener('click', function() {
            changeLikesComments(this.id, "l");
        });       
    });

    //Commenting Posts
    const commentButtons = document.querySelectorAll('.feather-message-circle');
    commentButtons.forEach(commentButton => {
        commentButton.addEventListener('click', function() {
            showComments(this.id);
            linkCommentButtons();
        });       
    });

}

function linkCommentButtons() {
    //Button to Submit Comments
    const submitButtons = document.querySelectorAll('.submit-button');
    submitButtons.forEach(submitButton => {
        submitButton.addEventListener('click', function() {
            var buttons = document.querySelectorAll(".commentSection");

            for (let i = 0; i < buttons.length; i++) {
                if (buttons[i].id === this.id) {

                    for (let i = 0; i < buttons.length; i++) {
                        if (buttons[i].id === this.id) {
                            if (buttons[i].value.includes("ª")) { alert("There is an invalid character, please try again."); }
                            else if (buttons[i].value.length <= 0) { alert("Please type a comment first."); }
                            else { changeLikesComments(this.id, "c", buttons[i].value); }
                            break;
                        }
                    }

                }
            }
        });       
    });
}

function changeLikesComments(postID, mode, comment) {
    console.log("Changing data...");
    var xhr = new XMLHttpRequest();
    xhr.open('POST', './tweet/postLikesComments.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        // Handle response
        if (xhr.status === 200) {
            console.log("Changed Successfully!");
            window.location.href = "homepage.php";
        } else {
            console.error("Error changing tweet:", xhr.responseText);
        }
    };
    console.log("end of the function");
    xhr.send('postID=' + encodeURIComponent(postID) + '&mode=' + encodeURIComponent(mode) + '&comment=' + encodeURIComponent(comment));
}

function showComments(theID) {
    const commentSection = document.querySelectorAll('.tweet-write-comment');
    
    commentSection.forEach(comment => {
        // Get the computed style of the element
        const computedStyle = window.getComputedStyle(comment);

        // Check the display property
        if (comment.id === theID) {
            if (computedStyle.display === "block" || computedStyle.display === "inline-block") {
                comment.style.display = "none";
                return false;
            } else {
                comment.style.display = "inline-block";
                return false;
            }
        }
    });
}