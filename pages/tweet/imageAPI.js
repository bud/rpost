// takes in the image of the URL
// uses chatgpt api to generate a response
// sends that response to bing api
// displays the results

function analyzeImage(imgurl) {
  const rightSection = document.querySelector('.right-section');

  const apiUrl = 'https://api.openai.com/v1/chat/completions';
  const OpenAIapiKey = '';
  let keyword = '';

  const payload = {
    model: 'gpt-4-vision-preview',
    messages: [
      {
        role: 'user',
        content: [
          {
            type: 'text',
            text: 'Please extract and mention the specific detail from the description from this image. I want to know an important object that it has. Forget about the image, what is happening. JUST GIVE ME THE IMPORTANT OBJECT. IF THE OBJECT IS VAGUE, GIVE ME VERY LITTLE INFO, NOT NECESSARY TO ALWAYS GIVE CONTEXT, BUT NEEDED WHEN NEEDED. Your response should only include the answer, NOTHING ELSE, NOTHING EXTRA, ACT LIKE YOU NEED TO TYPE THIS INTO Google to get more info.',
          },
          {
            type: 'image_url',
            image_url: {
              url: imgurl,
              detail: 'high',
            },
          },
        ],
      },
    ],
    max_tokens: 300,
  };

  fetch(apiUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${OpenAIapiKey}`,
    },
    body: JSON.stringify(payload),
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('OpenAI API is currently down');
    }
    return response.json();
  })
  .then(data => {
    // Process the response from OpenAI
    console.log(data);
    keyword = data.choices[0].message.content;
    rightSection.innerHTML = `<div id="keywords">${keyword}</div>`;

    // Bing API Call
    const apiKey = '';
    const endpoint = "https://api.bing.microsoft.com/v7.0/search";
    const headers = {
      "Ocp-Apim-Subscription-Key": apiKey,
    };
    const params = {
      q: keyword,
      responseFilter: "Webpages",
    };
    const queryString = new URLSearchParams(params).toString();
    const url = `${endpoint}?${queryString}`;

    return fetch(url, { headers: headers });
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Bing API is currently down');
    }
    return response.json();
  })
  .then(data => {
    // Process the response from Bing API
    const results = data.webPages.value;
    for (const webpage of results) {
      rightSection.innerHTML += `<div class="news-article"><h3><a href="${webpage.url}" target="_blank">${webpage.name}</a></h3><p>${webpage.snippet}</p></div>`;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    rightSection.innerHTML += `<div><h3>${error.message}</h3></div>`;
  });
}

// Count and display the number of buttons with class "analyze-button"
function countAnalyzeButtons() {
    const analyzeButtons = document.querySelectorAll('.analyze-button');

    analyzeButtons.forEach(analyzeButton => {
        analyzeButton.addEventListener('click', function() {
            // Retrieve information about the clicked tweet
            const tweetWrapper = this.closest('.tweet-wrap');
            tweetWrapper.classList.add('tweet-wrap-left-adjust');

            // checks if the image is found or not
            if (tweetWrapper.querySelector('.tweet-img') == null) {
                console.log("No image found");
                alert("You can only analyze tweets with images!");
            } else {
                console.log("Image found");
                
                const closeButton = popupContent.querySelector('.close-button');
                const leftSection = document.querySelector('.left-section');
                const rightSection = document.querySelector('.right-section');

                closeButton.addEventListener('click', function() {
                  const tweetWraps = document.querySelectorAll('.tweet-wrap');
              
                  tweetWraps.forEach(function(tweetWrap) {
                      tweetWrap.classList.remove('tweet-wrap-left-adjust');
                  });
                });

                popupContent.innerHTML = '';
                popupContent.appendChild(closeButton);
                popupContent.appendChild(leftSection);
                popupContent.appendChild(rightSection);

                // Display the popup box
                popupContent.appendChild(tweetWrapper.cloneNode(true));
                popupOverlay.style.display = 'block';

                console.log(tweetWrapper.querySelector('.tweet-img').src);
                analyzeImage(tweetWrapper.querySelector('.tweet-img').src);
            }

        });
    });
}