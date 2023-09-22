<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = $_POST["filename"];
    $comment = $_POST["comment"];

    if (!empty($filename) && !empty($comment)) {
        $comments = array();

        if (file_exists("comments.json")) {
            $comments = json_decode(file_get_contents("comments.json"), true);
        }

        if (!isset($comments[$filename])) {
            $comments[$filename] = array();
        }

        array_push($comments[$filename], $comment);

        file_put_contents("comments.json", json_encode($comments));

        // Redirect back to watch.php
        header("Location: watch.php?filename=" . urlencode($filename));
        exit();
    }
}
?>
