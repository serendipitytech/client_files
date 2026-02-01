<?php
$url = "https://geocoding.geo.census.gov/geocoder/locations/addressbatch";
$filename = 'data/address.csv';

// Ensure the file exists
if (!file_exists($filename)) {
    die("File not found: $filename");
}

// Initialize cURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, true); // Enable headers in response
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_VERBOSE, true); // Debugging info

// Use CURLFile for file upload
$file = new CURLFile($filename, 'text/csv', basename($filename));
$postFields = [
    'addressFile' => $file,
    'benchmark' => '4'
];

curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

// Execute request
$result = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// Check for errors
if ($result === false) {
    die('Curl error: ' . curl_error($curl));
}

curl_close($curl);

// Display HTTP status and response
echo "HTTP Response Code: $httpCode\n";
echo "Response from API:\n";
echo $result;

?>