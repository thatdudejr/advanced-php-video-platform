<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $videoFilename = $_FILES["video"]["name"];
    $thumbnailFilename = $_FILES["thumbnail"]["name"];

    // Upload video file
    $videoUploadPath = "uploads/" . $videoFilename;
    move_uploaded_file($_FILES["video"]["tmp_name"], $videoUploadPath);

    // Upload thumbnail file
    $thumbnailUploadPath = "thumbnails/" . $thumbnailFilename;
    move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $thumbnailUploadPath);

    // Update videos.json with new video data
    $videosData = json_decode(file_get_contents("videos.json"), true);
    $newVideoData = array(
        "video_title" => $title,
        "filename" => $videoFilename,
        "thumbnail" => $thumbnailUploadPath,
        "username" => $username,
        "views" => 0,
        "likes" => 0,
        "dislikes" => 0
    );
    $videosData[] = $newVideoData;
    file_put_contents("videos.json", json_encode($videosData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    // Return a JSON response indicating success
    header("Content-Type: application/json");
    echo json_encode(["success" => true]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        #upload-form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Upload Video</h1>
    <form id="upload-form" enctype="multipart/form-data" method="POST">
        <label for="title">Video Title:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="video">Select Video:</label>
        <input type="file" id="video" name="video" accept="video/*" required>
        <br>
        <label for="thumbnail">Select Thumbnail:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" required>
        <br>
        <input type="submit" value="Upload">
    </form>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const uploadForm = document.getElementById("upload-form");
            uploadForm.addEventListener("submit", function (event) {
                event.preventDefault(); // Prevent the default form submission
                uploadVideo();
            });
        });

        function uploadVideo() {
            const form = document.getElementById("upload-form");
            const formData = new FormData(form);

            fetch("upload.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Video uploaded successfully");
                    // Redirect to dashboard after successful upload
                    window.location.href = "dashboard.php";
                } else {
                    alert("Error uploading video");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while uploading the video");
            });
        }
    </script>
</body>
</html>

