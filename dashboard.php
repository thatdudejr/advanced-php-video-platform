



<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Handle channel creation form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["createChannel"])) {
    $channelName = $_POST["channelName"];
    
    // Assuming you have a directory structure for channels
    $channelDirectory = "channels/$username/$channelName";
    if (!is_dir($channelDirectory)) {
        mkdir($channelDirectory, 0777, true);
        file_put_contents("$channelDirectory/channel_name.txt", $channelName);
        // Redirect to the channel customization page
        header("Location: customize_channel.php?channel=" . urlencode($channelName));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            margin: 0;
            padding: 0;
        }

        h2 {
            margin-top: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        .delete-link {
            color: #ff0000;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>Dashboard</h1>
    <p>Welcome, <?php echo $username; ?>!</p>

    <h2>Your Uploaded Videos</h2>
    <ul>
        <?php
        $videosData = json_decode(file_get_contents("videos.json"), true);

        foreach ($videosData as $video) {
            if ($video["username"] === $username) {
                echo '<li>';
                echo '<a href="watch.php?filename=' . urlencode($video["filename"]) . '">' . $video["filename"] . '</a>';
                echo '<span>Views: ' . $video["views"] . '</span>';
                echo '<span>Likes: ' . $video["likes"] . '</span>';
                echo '<span>Dislikes: ' . $video["dislikes"] . '</span>';
                echo '<a href="delete_video.php?filename=' . urlencode($video["filename"]) . '">Delete</a>';
                echo '</li>';
            }
        }
        ?>
    </ul>

    <a href="upload.php">Upload New Video</a>
    <br>
    <a href="logout.php">Log Out</a>
    <br>
    <a href="index.php">Home</a>
    <br>
    <a href="create_channel.php">Create Channel</a>
</body>
</html>
