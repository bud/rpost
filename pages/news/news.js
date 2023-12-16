var newsContentCache=null;

function handleSearch() {
    const queryInput = document.getElementById('newsSearchInput');
    const categorySelect = document.getElementById('newsCategory');

    const query = queryInput.value;
    const category = categorySelect.value;

    // type of search
    let url = './news/fetchnews.php';
    if (query) {
        url += '?query=' + encodeURIComponent(query);
    } else if (category) {
        url += '?category=' + encodeURIComponent(category);
    }

    console.log("url:", url);

    // Make the API call
    fetch(url)
        .then(response => response.text())
        .then(html => {
            document.getElementById('newsArticles').innerHTML = html;
            newsContentCache = '<div id="newsFeed"><h2 id="newsHeader">See whats happening around the world!</h2><div id="searchContainer"><input type="text" id="newsSearchInput" placeholder="Search for news..."><select id="newsCategory"><option value="">Select Category</option><option value="technology">Technology</option><option value="education">Education</option><option value="sports">Sports</option><option value="politics">Politics</option><!-- More categories --></select><button id="searchButton" onClick=handleSearch()>Search</button></div></div>' + html; // Store the fetched news content
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('newsArticles').innerHTML = '<p>Error loading news content.</p>';
            newsContentCache = null;
        });
    // Clear both inputs
    queryInput.value = "";
    categorySelect.value = "";
}