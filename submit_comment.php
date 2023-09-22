<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["postId"]) && isset($_POST["commentContent"])) {
    $postId = $_POST["postId"];
    $commentContent = $_POST["commentContent"];

    $viewedUsername = $_SESSION["viewedUsername"]; // Make sure this session variable is set
    $postsFile = "channels/$viewedUsername/posts.json";

    if (file_exists($postsFile)) {
        $postsData = json_decode(file_get_contents($postsFile), true);

        if (is_array($postsData)) {
            foreach ($postsData as &$post) {
                if (isset($post["id"]) && $post["id"] === $postId) {
                    if (!isset($post["comments"])) {
                        $post["comments"] = [];
                    }
                    $comment = [
                        "username" => $_SESSION["username"],
                        "content" => $commentContent,
                        "timestamp" => date("Y-m-d H:i:s")
                    ];
                    $post["comments"][] = $comment;
                    break;
                }
            }
            // Save the updated posts data
            file_put_contents($postsFile, json_encode($postsData, JSON_PRETTY_PRINT));
        }
    }

    // Redirect back to the channel page
    header("Location: channel.php?viewedUsername=" . urlencode($viewedUsername));
    exit();
}
?>
