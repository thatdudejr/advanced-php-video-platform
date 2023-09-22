<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $filename = $data["filename"];
    $likes = $data["likes"];
    $dislikes = $data["dislikes"];

    $videosData = json_decode(file_get_contents("videos.json"), true);

    foreach ($videosData as &$video) {
        if ($video["filename"] === $filename) {
            if ($likes !== null) {
                $video["likes"] = $likes;
            }
            if ($dislikes !== null) {
                $video["dislikes"] = $dislikes;
            }
            break;
        }
    }

    file_put_contents("videos.json", json_encode($videosData));

    echo json_encode(array("success" => true));
}
?>
