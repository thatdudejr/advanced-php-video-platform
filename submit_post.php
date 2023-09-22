<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $content = $_POST["postContent"];
    $image = null;

    if (!empty($_FILES["postImage"]["name"])) {
        $imageFilename = $_FILES["postImage"]["name"];
        $imageUploadPath = "channels/$username/posts/" . $imageFilename;
        move_uploaded_file($_FILES["postImage"]["tmp_name"], $imageUploadPath);
        $image = $imageUploadPath;
    }

    $post = array(
        "content" => $content,
        "image" => $image
    );

    $postsData = json_decode(file_get_contents("channels/$username/posts.json"), true);
    $postsData[] = $post;
    file_put_contents("channels/$username/posts.json", json_encode($postsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    header("Location: channel.php");
    exit();
}
?>
