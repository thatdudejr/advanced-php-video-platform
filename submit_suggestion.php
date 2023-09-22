<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

// Load existing suggestions
$suggestionsFile = "suggestions.json";
$suggestions = [];
if (file_exists($suggestionsFile)) {
    $suggestions = json_decode(file_get_contents($suggestionsFile), true);
}

// Get the submitted suggestion text
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$suggestionText = $data["text"];

// Get the username of the commenter
$username = $_SESSION["username"];

// Add the new suggestion to the array
$newSuggestion = [
    "username" => $username,
    "text" => $suggestionText,
    "timestamp" => time()
];
$suggestions[] = $newSuggestion;

// Save the updated suggestions to the file
file_put_contents($suggestionsFile, json_encode($suggestions, JSON_PRETTY_PRINT));

// Return a JSON response indicating success
header("Content-Type: application/json");
echo json_encode(["success" => true]);
