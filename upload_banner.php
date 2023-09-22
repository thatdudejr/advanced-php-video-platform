<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["uploadBanner"])) {
    $loggedInUsername = $_SESSION["username"];

    if (isset($_FILES["bannerImage"]) && $_FILES["bannerImage"]["error"] === 0) {
        $imageFileType = strtolower(pathinfo($_FILES["bannerImage"]["name"], PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            $targetPath = "channels/$loggedInUsername/banner.jpg";
            move_uploaded_file($_FILES["bannerImage"]["tmp_name"], $targetPath);
        }
    }
}

header("Location: channel.php?username=$loggedInUsername");
exit();
?>
