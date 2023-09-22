<?php
session_start();

$filename = $_GET["filename"];
$filepath = "uploads/" . $filename;

$allowedExtensions = array("mp4", "webm"); // Add more extensions if needed
$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

if (in_array($fileExtension, $allowedExtensions) && file_exists($filepath)) {
    $videosData = json_decode(file_get_contents("videos.json"), true);
    $video = null;

    foreach ($videosData as &$v) {
        if ($v["filename"] === $filename) {
            $video = &$v;
            break;
        }
    }

    if ($video) {
        // Check if the viewer has already viewed this video
        $viewedVideos = isset($_SESSION['viewed_videos']) ? $_SESSION['viewed_videos'] : [];
        if (!in_array($filename, $viewedVideos)) {
            // Increment views count only if the video hasn't been viewed before
            $video['views']++;
            // Mark this video as viewed by adding its filename to the list
            $_SESSION['viewed_videos'][] = $filename;
            file_put_contents("videos.json", json_encode($videosData));
        }

        // Handle comment submission
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["username"]) && isset($_POST["comment"])) {
            $commentText = $_POST["comment"];
            $username = $_SESSION["username"];
            $commentData = $username . '|' . $commentText . "\n";

            // Create comments directory if not exists
            $commentsDir = "comments/";
            if (!file_exists($commentsDir)) {
                mkdir($commentsDir);
            }

            // Save comment data
            $commentsFilePath = $commentsDir . $filename . ".txt";
            file_put_contents($commentsFilePath, $commentData, FILE_APPEND);

            // Update video description if submitted by creator
            if (isset($_POST["description"]) && $_SESSION["username"] === $video["username"]) {
                $video["description"] = $_POST["description"];
                file_put_contents("videos.json", json_encode($videosData));
            }
        }

        // Retrieve comments
        $comments = array();
        $commentsFilePath = "comments/" . $filename . ".txt";
        if (file_exists($commentsFilePath)) {
            $comments = file($commentsFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
    } else {
        echo 'Video not found.';
    }
} else {
    echo 'Invalid video file or file not found.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        .top-nav {
            text-align: right;
            padding: 10px;
        }

        .top-nav a {
            margin-right: 10px;
            color: #333;
            text-decoration: none;
        }

        .top-nav a:hover {
            color: #007bff;
        }

        h1 {
            margin: 20px 0;
            color: #333;
        }

        video {
            max-width: 100%;
            max-height: 80vh;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .video-details {
            margin-top: 20px;
            color: #777;
        }

        .comment-section {
            margin-top: 30px;
            width: 100%;
            max-width: 600px;
        }

        .comment-section h2 {
            color: #333;
        }

        #comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        #comment-form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        #comment-form button:hover {
            background-color: #0056b3;
        }

        .comment-list {
            list-style: none;
            padding: 0;
        }

        .comment-list li {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .comment-list li strong {
            color: #007bff;
        }
    </style>
    <title>Watch Video</title>
</head>
<body>
    <div class="top-nav">
        <a href="index.php">Home</a>
        <?php
        if (isset($_SESSION["username"])) {
            echo '<span>Hello, ' . $_SESSION["username"] . '!</span>
                  <a href="logout.php">Log Out</a>';
        } else {
            echo '<a href="login.php">Log In</a>
                  <a href="signup.php">Sign Up</a>';
        }
        ?>
    </div>
    
    <h1>Watch Video</h1>
    
    <video controls width="640" height="360">
        <source src="<?php echo $filepath; ?>" type="video/<?php echo $fileExtension; ?>">
        Your browser does not support the video tag.
    </video>
<div class="video-details">
    <?php
    $videosData = json_decode(file_get_contents("videos.json"), true);
    
    $video = null;
    foreach ($videosData as $key => $v) {
        if ($v["filename"] === $filename) {
            $video = &$videosData[$key];
            break;
        }
    }
    
    if ($video && isset($video["views"])) :
    ?>
        <p>Views: <?php echo $video["views"]; ?></p>
        
        <?php if (isset($video["description"])) : ?>
            <p>Description: <?php echo htmlspecialchars($video["description"]); ?></p>
        <?php endif; ?>
        
        <?php if (isset($_SESSION["username"]) && $_SESSION["username"] === $video["username"]) : ?>
            <form id="description-form" action="watch.php?filename=<?php echo urlencode($filename); ?>" method="POST">
                <label for="description">Update Description:</label>
                <textarea name="description" id="description"><?php echo isset($video["description"]) ? htmlspecialchars($video["description"]) : ''; ?></textarea>
                <button type="submit">Update Description</button>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["description"])) {
                $newDescription = $_POST["description"];
                $video["description"] = $newDescription;
                file_put_contents("videos.json", json_encode($videosData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
            ?>
        <?php endif; ?>
    <?php else : ?>
        <p>Video details not available.</p>
    <?php endif; ?>
</div>



    
    <div class="comment-section">
        <h2>Comments</h2>
        <?php
        if (isset($_SESSION["username"])) {
            echo '<form id="comment-form" action="watch.php?filename=' . urlencode($filename) . '" method="POST">
                <textarea name="comment" placeholder="Write a comment" required></textarea>
                <button type="submit">Post Comment</button>
            </form>';
        } else {
            echo '<p>Please <a href="login.php">log in</a> to post comments.</p>';
        }

        if (!empty($comments)) {
            echo '<ul class="comment-list">';
            foreach ($comments as $comment) {
                $commentData = explode('|', $comment, 2);
                $username = $commentData[0];
                $text = $commentData[1];
                echo '<li><strong>' . htmlspecialchars($username) . ':</strong> ' . htmlspecialchars($text) . '</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>
</body>
</html>




