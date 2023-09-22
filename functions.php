<?php

function getChannelVideos($videosData, $channelUsername) {
    return array_filter($videosData, function ($video) use ($channelUsername) {
        return $video["username"] === $channelUsername;
    });
}

?>
