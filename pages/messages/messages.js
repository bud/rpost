function reloadMessages(recieverRcsid) {
    setTimeout(function() {
        // AJAX request to PHP script
        fetch(`./messages/load_messages.php?receiver=${receiverRcsid}`)
        .then(response => response.text())
        .then(data => {
            
            // Assuming you have a div with ID 'messages-container' to display messages
            document.getElementById('messages-container').innerHTML = "<a href='#' onclick='loadUserList();'> Go back </a>" + data;
            document.getElementById('typeMessage').value = '';
        })
        .catch(error => console.error('Error:', error));
    }, 100);
    
    
}

// Function to reload all clickable links
function loadLinks() {
    const messageInputContainer = document.querySelector(".message-input-container");
    document.querySelectorAll('.receiver-link').forEach(item => {
        item.addEventListener('click', function (e) {
            isLoadMessage = true;
            e.preventDefault();

            receiverRcsid = this.getAttribute('data-rcsid');
            document.getElementById('receiver-id-input').value = receiverRcsid;

            // AJAX request to PHP script
            fetch(`./messages/load_messages.php?receiver=${receiverRcsid}`)
                .then(response => response.text())
                .then(data => {
                    
                    // Assuming you have a div with ID 'messages-container' to display messages
                    document.getElementById('messages-container').innerHTML = "<a href='#' onclick='loadUserList();'> Go back </a>" + data;
                })
                .catch(error => console.error('Error:', error));

                messageInputContainer.style.display = "block";
        });
    });
}

// Function to load and display the list of users
function loadUserList() {
    isLoadMessage = false;
    const messagesContainer = document.getElementById("messages-container");
    const messageInputContainer = document.querySelector(".message-input-container");

    messageInputContainer.style.display = "none";
    // Perform an AJAX request to load the list of users
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Display the list of users in the messages-container div
            messagesContainer.innerHTML = xhr.responseText;
            
            loadLinks();
        }
    };
    xhr.open('GET', './messages/load_user_list.php', true); // Open PHP file to load the user list
    xhr.send();
}



document.addEventListener("DOMContentLoaded", function () {
    loadUserList();

    const messagePanel = document.querySelector(".message-panel");
    const toggleButton = document.getElementById("toggle-button");
    const arrowIcon = document.getElementById("arrow-icon");
    const messagesContainer = document.querySelector(".message-content");
    const messageInputContainer = document.querySelector(".message-input-container");
    const sendButton = document.getElementById("send-button");


    // Initially hide the messages container
    messagesContainer.style.display = "none";
    // messageInputContainer.style.display = "none";

    // open up the message bar
    toggleButton.addEventListener("click", function () {
        if (messagePanel.style.height === "500px") {
            //This branch closes the message panel
            messagePanel.style.height = "60px";
            arrowIcon.classList.remove("collapsed");
            arrowIcon.innerHTML = '<span>&#8657;</span>'; // Change to up arrow
            // Hide the messages container when the up arrow is clicked
            messagesContainer.style.display = "none";
            messageInputContainer.style.display = "none";
        } else {
            messagePanel.style.height = "500px";
            arrowIcon.classList.add("collapsed");
            arrowIcon.innerHTML = '<span>&#8659;</span>'; // Change to down arrow
            // Show the messages container when the down arrow is clicked
            
            loadLinks();

            messagesContainer.style.display = "block";
            if (isLoadMessage) {
                messageInputContainer.style.display = "block";
            }
            
        }
    });

    arrowIcon.addEventListener("click", function () {
        toggleButton.click(); // Trigger the click event of the button when the arrow is clicked
    });
});
