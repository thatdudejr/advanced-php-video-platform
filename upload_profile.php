<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["uploadProfile"])) {
    $loggedInUsername = $_SESSION["username"];

    if (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] === 0) {
        $imageFileType = strtolower(pathinfo($_FILES["profileImage"]["name"], PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowedExtensions)) {
            $targetPath = "channels/$loggedInUsername/profile.jpg";
            move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetPath);
        }
    }
}

header("Location: channel.php?username=$loggedInUsername");
exit();
?>
