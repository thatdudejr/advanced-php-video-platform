<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeeTube</title>
    <style>
        /* Your CSS styles here */
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

        .video-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .video-card img {
            max-width: 160px;
            margin-right: 10px;
        }

        .video-card .video-info {
            flex: 1;
        }

        .video-card h2 {
            margin: 0;
        }

        .video-card p {
            margin: 5px 0;
        }

        .video-card a {
            color: #007bff;
            text-decoration: none;
        }

        #video-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .channel-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
        }

        .channel-card h3 {
            margin: 0;
        }

        .channel-card p {
            margin: 5px 0;
        }

        .legal-rules {
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            background-color: #f5f5f5;
        }

        .legal-rules h2 {
            margin-top: 0;
        }

        .suggestions {
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            background-color: #f5f5f5;
        }

        .suggestions h2 {
            margin-top: 0;
        }

        .suggestion {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }

        .suggestion p {
            margin: 5px 0;
        }

        .timestamp {
            font-size: 12px;
            color: #777;
        }

        .suggestion-form textarea {
            width: 100%;
            padding: 5px;
        }

        .suggestion-form button {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .suggestion-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VeeTube</h1>
        <?php
        if (isset($_SESSION["username"])) {
            echo '<p>Welcome, ' . $_SESSION["username"] . '!</p>';
            echo '<p><a href="dashboard.php">Go to Dashboard</a></p>';
            echo '<p><a href="logout.php">Log Out</a></p>';
            echo '<p><a href="DM/index.php">DM</a></p>';
            echo '<p><a href="upload.php">Upload a Video</a></p>';
        } else {
            echo '<p>Welcome to VeeTube!</p>';
        }
        ?>
    </div>
    <div class="container">
        <h2>Featured Channels</h2>
        <?php
        $channelFiles = scandir("channels/");
        foreach ($channelFiles as $channelFile) {
            if ($channelFile !== "." && $channelFile !== "..") {
                $channelName = pathinfo($channelFile, PATHINFO_FILENAME);
                echo '<div class="channel-card">';
                echo '<h3>' . $channelName . '</h3>';
                echo '<p>Description of ' . $channelName . '</p>';
                echo '<a href="channel.php?username=' . $channelName . '">Visit Channel</a>';
                echo '</div>';
            }
        }
        ?>

        <h2>Latest Videos</h2>
        <div id="video-list"></div>

        <!-- Legal Rules Disclaimer -->
        <div class="legal-rules">
            <h2>Legal Rules</h2>
            <p>No piracy, 18+ content, or illegal activities are allowed on this platform.</p>
            <p>Bullying and harassment are strictly prohibited.</p>
            <p>Any violation of these rules may result in a ban.</p>
            <p>If you believe any content violates these rules, please contact us at oosterhuisjulian0@gmail.com.</p>
        </div>

        
    <!-- Suggestions Section -->
    <div class="suggestions">
        <h2>Suggestions</h2>
        <div id="suggestion-list">
            <?php
            $suggestionsData = json_decode(file_get_contents("suggestions.json"), true);
            foreach ($suggestionsData as $suggestion) {
                if (isset($suggestion["username"]) && isset($suggestion["text"]) && isset($suggestion["timestamp"])) {
                    echo '<div class="suggestion">';
                    echo '<p><strong>' . $suggestion["username"] . ':</strong> ' . $suggestion["text"] . '</p>';
                    echo '<p class="timestamp">' . date("Y-m-d H:i:s", $suggestion["timestamp"]) . '</p>'; // Use date() function to format timestamp
                    echo '</div>';
                }
            }
            ?>
        </div>
        <?php
        if (isset($_SESSION["username"])) {
            echo '
            <div class="suggestion-form">
                <h3>Submit a Suggestion</h3>
                <textarea id="suggestion-text" placeholder="Enter your suggestion"></textarea>
                <button id="submit-suggestion">Submit</button>
            </div>';
        }
        ?>
    </div>
    <script>
    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString();
    }
        document.addEventListener("DOMContentLoaded", function () {
            loadVideos();
            <?php if (isset($_SESSION["username"])) { ?>
                const submitButton = document.getElementById("submit-suggestion");
                submitButton.addEventListener("click", function () {
                    submitSuggestion();
                });
            <?php } ?>
        });
    function loadVideos() {
        fetch("videos.json")
            .then(response => response.json())
            .then(videos => {
                const videoList = document.getElementById("video-list");
                videoList.innerHTML = "";

                videos.forEach(video => {
                    const videoElement = document.createElement("div");
                    videoElement.className = "video-card";
                    videoElement.innerHTML = `
                        <a href="watch.php?filename=${encodeURIComponent(video.filename)}">
                            <img src="${video.thumbnail}" alt="${video.video_title} Thumbnail">
                        </a>
                        <h2>${video.video_title || "No Title"}</h2>
                        <p>Views: ${video.views}</p>
                    `;
                    videoList.appendChild(videoElement);
                });
            });

        loadSuggestions();
    }

    function loadSuggestions() {
        fetch("suggestions.json")
            .then(response => response.json())
            .then(suggestions => {
                const suggestionsList = document.getElementById("suggestions-list");
                suggestionsList.innerHTML = "";

                suggestions.forEach(suggestion => {
                    const suggestionElement = document.createElement("div");
                    suggestionElement.className = "suggestion-card";
                    suggestionElement.innerHTML = `
                        <p>${suggestion.text}</p>
                        <p class="suggestion-info">By: ${suggestion.username} | ${formatTimestamp(suggestion.timestamp)}</p>
                    `;
                    suggestionsList.appendChild(suggestionElement);
                });
            });
    }

    function submitSuggestion() {
        const suggestionText = document.getElementById("suggestion-text").value;
        const suggestion = {
            text: suggestionText,
            username: "<?php echo $_SESSION["username"]; ?>",
            timestamp: Date.now()
        };

        fetch("submit_suggestion.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(suggestion)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Suggestion submitted successfully");
                loadSuggestions(); // Refresh suggestions list
            } else {
                alert("Error submitting suggestion");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while submitting the suggestion");
        });
    }
    </script>
</body>
</html>

