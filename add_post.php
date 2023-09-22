<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["username"])) {
    $viewedUsername = $_GET["username"];
} else {
    header("Location: index.php");
    exit();
}

$loggedInUsername = $_SESSION["username"];

// Load the existing posts data
$postsData = [];
$postsFile = "channels/$viewedUsername/posts.json";
if (file_exists($postsFile)) {
    $postsData = json_decode(file_get_contents($postsFile), true);
}

// Handle adding a new post
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addPost"]) && $loggedInUsername === $viewedUsername) {
    $newContent = $_POST["postContent"];

    $newPost = [
        "content" => $newContent,
    ];

    // Handle image upload
    if (isset($_FILES["postImage"]) && $_FILES["postImage"]["error"] === 0) {
        $imageFileType = strtolower(pathinfo($_FILES["postImage"]["name"], PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            $targetPath = "channels/$viewedUsername/images/" . uniqid() . ".$imageFileType";
            move_uploaded_file($_FILES["postImage"]["tmp_name"], $targetPath);
            $newPost["image"] = $targetPath;
        }
    }

    // Add the new post to the existing posts data
    $postsData[] = $newPost;

    // Save the updated posts data
    file_put_contents($postsFile, json_encode($postsData, JSON_PRETTY_PRINT));

    header("Location: channel.php?username=$viewedUsername");
    exit();
}

// Redirect if not logged in or viewing the wrong channel
header("Location: index.php");
exit();
?>
