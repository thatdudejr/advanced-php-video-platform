<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newUsername = $_POST["username"];
    $newPassword = $_POST["password"];

    $usersData = json_decode(file_get_contents("users.json"), true);

    foreach ($usersData as $user) {
        if ($user["username"] === $newUsername) {
            $signupError = true;
            break;
        }
    }

    if (!isset($signupError)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the password

        $usersData[] = array("username" => $newUsername, "password" => $hashedPassword); // Store hashed password
        file_put_contents("users.json", json_encode($usersData));

        session_start();
        $_SESSION["username"] = $newUsername;

        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .error {
            color: #dc3545;
            margin-top: 10px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }

        /* Add more styles as needed */
    </style>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (isset($signupError)) : ?>
            <p class="error">Username already taken. Please choose a different username.</p>
        <?php endif; ?>
        <form action="signup.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Sign Up</button>
        </form>
        <a href="login.php">Log In</a>
    </div>
</body>
</html>


