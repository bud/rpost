<?php
$apiKey = '';
$endpoint = 'https://api.bing.microsoft.com/v7.0/news/search';

// Get parameters from the request
$query = isset($_GET['query']) ? $_GET['query'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$isTrending = empty($query) && empty($category);

// Headers for the API request
$headers = [
    'Ocp-Apim-Subscription-Key: ' . $apiKey
];

if ($isTrending) {
    $url = $endpoint . "/trending";
} else {
    // For specific query or category
    $searchTerm = $query ?: $category;
    $url = $endpoint . "?q=" . urlencode($searchTerm);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    // Handle error; unable to fetch news
    echo '<p>Unable to fetch news at the moment.</p>';
    exit;
}

$newsData = json_decode($response, true);

$htmlContent = '<div id="newsArticles">';

foreach ($newsData['value'] as $newsItem) {
    $htmlContent .= '<div class="news-article">';
    $htmlContent .= '<h3><a href="' . htmlspecialchars($newsItem['url']) . '" target="_blank">' . htmlspecialchars($newsItem['name']) . '</a></h3>';
    $htmlContent .= '<p>' . htmlspecialchars($newsItem['description']) . '</p>';
    $htmlContent .= '</div>';
}

$htmlContent .= '</div>';

// Return the HTML content
echo $htmlContent;
?>