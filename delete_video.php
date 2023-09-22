<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$filename = $_GET["filename"];
$videosData = json_decode(file_get_contents("videos.json"), true);

foreach ($videosData as $key => $video) {
    if ($video["filename"] === $filename && $video["username"] === $_SESSION["username"]) {
        // Remove video entry from array
        unset($videosData[$key]);

        // Save updated array to videos.json
        file_put_contents("videos.json", json_encode(array_values($videosData)));

        // Delete the video file from uploads folder
        $filepath = "uploads/" . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        break;
    }
}

header("Location: dashboard.php");
exit();
?>
