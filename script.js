   document.addEventListener("DOMContentLoaded", function () {
    loadVideos();
});

function loadVideos() {
    fetch("videos.json")
        .then(response => response.json())
        .then(videos => {
            const videoList = document.getElementById("video-list");
            videoList.innerHTML = "";

            videos.forEach(video => {
                // Remove "thumbnails/" prefix if present
                const thumbnail = video.thumbnail.replace("thumbnails/", "");

                const videoElement = document.createElement("div");
                videoElement.innerHTML = `
                    <div class="video-card">
                        <img src="thumbnails/${thumbnail}" alt="${video.video_title} Thumbnail">
                        <div class="video-info">
                            <h2>${video.video_title || "No Title"}</h2>
                            <p>Views: ${video.views}</p>
                            <p><a href="watch.php?filename=${encodeURIComponent(video.filename)}">Watch</a></p>
                        </div>
                    </div>
                `;
                videoList.appendChild(videoElement);
            });
        });
}