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

// Load the posts data
$postsData = [];
$postsFile = "channels/$viewedUsername/posts.json";
if (file_exists($postsFile)) {
    $postsData = json_decode(file_get_contents($postsFile), true);
}

// Handle post editing
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editPost"])) {
    $postIndex = $_POST["postIndex"];
    $newContent = $_POST["postContent"];

    if ($postIndex >= 0 && $postIndex < count($postsData)) {
        $postsData[$postIndex]["content"] = $newContent;
        
        // Handle image upload
        if (isset($_FILES["postImage"]) && $_FILES["postImage"]["error"] === 0) {
            $imageFileType = strtolower(pathinfo($_FILES["postImage"]["name"], PATHINFO_EXTENSION));
            $allowedExtensions = array("jpg", "jpeg", "png", "gif");

            if (in_array($imageFileType, $allowedExtensions)) {
                $targetPath = "channels/$viewedUsername/images/" . uniqid() . ".$imageFileType";
                move_uploaded_file($_FILES["postImage"]["tmp_name"], $targetPath);
                $postsData[$postIndex]["image"] = $targetPath;
            }
        }
        
        file_put_contents($postsFile, json_encode($postsData, JSON_PRETTY_PRINT));
    }

    header("Location: channel.php?username=$viewedUsername");
    exit();
}

// Handle profile/banner image upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["uploadProfile"]) && $loggedInUsername === $viewedUsername) {
    if (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] === 0) {
        $imageFileType = strtolower(pathinfo($_FILES["profileImage"]["name"], PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            $targetPath = "channels/$viewedUsername/profile.jpg";
            move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetPath);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["uploadBanner"]) && $loggedInUsername === $viewedUsername) {
    if (isset($_FILES["bannerImage"]) && $_FILES["bannerImage"]["error"] === 0) {
        $imageFileType = strtolower(pathinfo($_FILES["bannerImage"]["name"], PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            $targetPath = "channels/$viewedUsername/banner.jpg";
            move_uploaded_file($_FILES["bannerImage"]["tmp_name"], $targetPath);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $viewedUsername; ?>'s Channel</title>
    <style>
/* Reset default browser styles */
body, h1, h2, p, ul, li, a, form, input, button, img {
    margin: 0;
    padding: 0;
    border: none;
    outline: none;
    font-family: Arial, sans-serif;
    text-decoration: none;
    list-style: none;
}

/* Global styles */
body {
    background-color: #f8f8f8;
    font-size: 16px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header styles */
.header {
    background-color: #007bff;
    color: #fff;
    padding: 10px;
    text-align: center;
}

/* Channel banner styles */
.channel-banner {
    background-color: #007bff;
    color: #fff;
    text-align: center;
    padding: 20px;
}

.channel-banner img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
}

/* Profile picture styles */
.profile-picture {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin-bottom: 10px;
    overflow: hidden;
}

.profile-picture img {
    max-width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Post form styles */
.post-form {
    margin-bottom: 20px;
}

.post-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    resize: vertical;
}

.post-form button[type="submit"] {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.post-form button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Post list styles */
.post-list {
    display: grid;
    grid-gap: 20px;
}

.post {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
}

.post .post-content {
    margin-bottom: 10px;
}

.post .post-image img {
    max-width: 100%;
    height: auto;
}

/* Edit form styles */
.edit-form {
    background-color: #f4f4f4;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
}

.edit-form h2 {
    margin-bottom: 10px;
}

.edit-form input[type="file"] {
    display: block;
    margin-bottom: 10px;
}

.edit-form button[type="submit"] {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.edit-form button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Footer styles */
.footer {
    text-align: center;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
}

/* Add more styles as needed */

    </style>


</head>
<body>
    <div class="channel-banner">
        <?php
        if (file_exists("channels/$viewedUsername/banner.jpg")) {
            echo '<img src="channels/' . $viewedUsername . '/banner.jpg" alt="Channel Banner">';
        } else {
            echo '<h1>' . $viewedUsername . "'s Channel</h1>";
        }
        ?>
        <p>Edit <?php echo $viewedUsername; ?>'s Channel</p>
    </div>
    <div class="container">
<div class="profile-picture">
    <?php
    if (file_exists("channels/$viewedUsername/profile.jpg")) {
        echo '<img src="channels/' . $viewedUsername . '/profile.jpg" alt="Profile Picture">';
    } else {
        echo '<img src="default_profile.jpg" alt="Profile Picture">';
    }
    ?>
</div>
<?php
if ($loggedInUsername === $viewedUsername) {
    echo '<div class="edit-form">';
    echo '<h2>Edit Profile and Banner</h2>';
    echo '<form action="upload_profile.php" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="profileImage" accept="image/*">';
    echo '<button type="submit" name="uploadProfile">Upload Profile Picture</button>';
    echo '</form>';

    echo '<form action="upload_banner.php" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="bannerImage" accept="image/*">';
    echo '<button type="submit" name="uploadBanner">Upload Banner</button>';
    echo '</form>';
    echo '</div>';
}
?>
               <?php
        if ($loggedInUsername === $viewedUsername) {
            echo '<div class="edit-form">';
            echo '<h2>Edit Posts</h2>';
            echo '<ul class="post-list">';
            foreach ($postsData as $postKey => $post) {
                echo '<li class="post">';
                echo '<div class="post-content">';
                echo '<form action="edit_post.php?username=' . $viewedUsername . '&postIndex=' . $postKey . '" method="post" enctype="multipart/form-data">';
                echo '<textarea name="postContent" rows="4">' . nl2br(htmlspecialchars($post["content"])) . '</textarea>';
                echo '<input type="hidden" name="postIndex" value="' . $postKey . '">';
                echo '<input type="file" name="postImage" accept="image/*">';
                echo '<button type="submit" name="editPost">Save Changes</button>';
                echo '</form>';
                echo '</div>';
                if (isset($post["image"])) {
                    echo '<div class="post-image"><img src="' . htmlspecialchars($post["image"]) . '" alt="Post Image"></div>';
                }
                echo '</li>';
            }

            // Add the "Add Post" section here
            echo '<li class="post">';
            echo '<div class="post-content">';
            echo '<form action="add_post.php?username=' . $viewedUsername . '" method="post" enctype="multipart/form-data">';
            echo '<textarea name="postContent" rows="4" placeholder="Write your new post..."></textarea>';
            echo '<input type="file" name="postImage" accept="image/*">';
            echo '<button type="submit" name="addPost">Add Post</button>';
            echo '</form>';
            echo '</div>';
            echo '</li>';

            echo '</ul>';
            echo '</div>';
        } else {
            echo '<h2>Posts</h2>';
            echo '<ul class="post-list">';
            foreach ($postsData as $post) {
                echo '<li class="post">';
                if (isset($post["content"])) {
                    echo '<div class="post-content">' . nl2br(htmlspecialchars($post["content"])) . '</div>';
                }
                if (isset($post["image"])) {
                    echo '<div class="post-image"><img src="' . htmlspecialchars($post["image"]) . '" alt="Post Image"></div>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>
    <h2>Videos</h2>
    <div class="video-list">
        <?php
        $videosFile = "channels/$viewedUsername/videos.json";

        if (file_exists($videosFile)) {
            $videosData = json_decode(file_get_contents($videosFile), true);

            foreach ($videosData as $video) {
                echo '<div class="video">';
                echo '<h3>' . htmlspecialchars($video["video_title"]) . '</h3>';
                echo '<video controls width="320" height="180">';
                echo '<source src="uploads/' . $video["filename"] . '" type="video/mp4">';
                echo 'Your browser does not support the video tag.';
                echo '</video>';
                echo '<p>Views: ' . $video["views"] . '</p>';
                // Add more video details here as needed
                echo '</div>';
            }
        } else {
            echo '<p>No videos available.</p>';
        }
        ?>
    </div>
</body>
</html>
