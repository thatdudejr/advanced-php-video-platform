<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Handle channel creation form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["createChannel"])) {
    $channelName = $username; // Use the username as the channel name
    
    // Assuming you have a directory structure for channels
    $channelDirectory = "channels/$username/$channelName";
    if (!is_dir($channelDirectory)) {
        mkdir($channelDirectory, 0777, true);
        file_put_contents("$channelDirectory/channel_name.txt", $channelName);
        // Redirect to the channel customization page or wherever needed
        header("Location: dashboard.php"); // Change this to your desired destination
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Channel</title>
</head>
<body>
    <h1>Create Channel</h1>
    <form method="post">
        <input type="submit" name="createChannel" value="Create Channel">
    </form>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

