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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - <?php echo $viewedUsername; ?>'s Channel</title>
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .channel-banner {
            /* Channel banner styles */
        }

        .profile-picture {
            /* Profile picture styles */
        }

        .edit-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }

        .edit-form button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-form button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .post-list {
            display: grid;
            grid-gap: 20px;
        }

        .post {
            /* Post styles */
        }

        .post .post-content {
            margin-bottom: 10px;
        }

        .post .post-image img {
            max-width: 100%;
            height: auto;
        }

        /* Add more styles as needed */
    </style>
</head>
<body>
    <div class="channel-banner">
        <!-- Display banner and edit forms for banner/profile -->
        <?php
        if (file_exists("channels/$viewedUsername/banner.jpg")) {
            echo '<img src="channels/' . $viewedUsername . '/banner.jpg" alt="Channel Banner">';
        } else {
            echo '<h1>' . $viewedUsername . "'s Channel</h1>";
        }
        ?>
        <p>Welcome to <?php echo $viewedUsername; ?>'s Channel!</p>
    </div>
    <div class="container">
        <div class="profile-picture">
            <!-- Display profile picture and edit forms for profile picture -->
            <?php
            if (file_exists("channels/$viewedUsername/profile.jpg")) {
                echo '<img src="channels/' . $viewedUsername . '/profile.jpg" alt="Profile Picture">';
            } else {
                echo '<img src="default_profile.jpg" alt="Profile Picture">';
            }
            ?>
        </div>
        <div class="edit-form">
            <?php
            if ($loggedInUsername === $viewedUsername) {
                echo '<h2>Edit Posts</h2>';
                echo '<ul class="post-list">';
                foreach ($postsData as $postKey => $post) {
                    echo '<li class="post">';
                    echo '<div class="post-content">';
                    echo '<form action="edit_post.php?username=' . $viewedUsername . '" method="post" enctype="multipart/form-data">';
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
                echo '</ul>';
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
    </div>
</body>
</html>
