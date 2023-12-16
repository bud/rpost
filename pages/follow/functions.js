function attachFollowButtonListeners() {
    const followButtons = document.querySelectorAll('.follow-btn');
    followButtons.forEach(followButton => {
        followButton.addEventListener('click', function() {
            const rcsidToFollow = this.getAttribute('data-rcsid');
            console.log('Follow button clicked for RCSID:', rcsidToFollow);

            // Change the button text to "Following"
            this.textContent = 'Following';
            this.disabled = true;

            // Send AJAX request to follow the user
            var xhr = new XMLHttpRequest();
            xhr.open('POST', './follow/follow_user.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Follow action successful');
                } else {
                    console.log('Error in follow action');
                }
            };
            xhr.send('follow=' + encodeURIComponent(rcsidToFollow));
        });
    });
}