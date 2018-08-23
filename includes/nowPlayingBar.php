<?php
    //randomly grab 10 songs' id from database
    $songQuery = mysqli_query($con,"SELECT id FROM songs ORDER BY RAND() LIMIT 10");
    $resultArray = array();
    while($row = mysqli_fetch_array($songQuery)) {
        array_push($resultArray, $row['id']);
    }
    //return ids in form of JSON
    $jsonArray = json_encode($resultArray);

?>

<script>

    $(document).ready(function() {
        //Get JSON data of song ids
        currentPlaylist = <?php echo $jsonArray ?>;
        //Create a new audio element
        audioElement = new Audio();
        //console.log(typeof(currentPlaylist[0]));

        //Set the track with id
        setTrack(currentPlaylist[0], currentPlaylist, false);
        //Set Volume Progress bar
        updateVolumeProgressBar(audioElement.audio);

        $(".playbackBar .progressBar").mousedown(function() {
            mouseDown = true;
        });
        $(".playbackBar .progressBar").mousemove(function(e) {
            if(mouseDown) {
                timeFromOffset(e, this);
            }
        });
        $(".playbackBar .progressBar").mouseup(function(e) {
            timeFromOffset(e, this);
        });


        $(".volumeBar .progressBar").mousedown(function() {
            mouseDown = true;
        });
        $(".volumeBar .progressBar").mousemove(function(e) {
            if(mouseDown) {
                var percentage = e.offsetX / $(this).width();
                if(percentage >= 0 && percentage <= 1) {
                    audioElement.audio.volume = percentage; 
                }   
            }
        });
        $(".volumeBar .progressBar").mouseup(function(e) {
            var percentage = e.offsetX / $(this).width();
            if(percentage >= 0 && percentage <= 1) {
                    audioElement.audio.volume = percentage; 
            }   
        });

        $(document).mouseup(function(e) {
            mouseDown = false;
        });
    });

    function timeFromOffset(mouse, progressBar) {
        var percentage = (mouse.offsetX / $(progressBar).width()) * 100;
        var seconds = audioElement.audio.duration * (percentage / 100);
        audioElement.setTime(seconds);
    }

    function setTrack(trackId, newPlaylist, play) {
        // Query song's data in database
        // Then fill in the song name, artist name and the artwork
        // Then set the music file path
        // Finally play the song
        $.post('includes/handlers/ajax/getSongJson.php', { songId: trackId }, function(data) {
            var songData = JSON.parse(data);
            console.log(songData);
            
            $(".trackName span").text(songData.title);

            //Query the artist name using song's artist id
            var artistId = songData.artist;
            $.post('includes/handlers/ajax/getArtistJson.php', { artistId : artistId }, function(data) {
                var artistData = JSON.parse(data);
                //console.log(artistData);
                $(".artistName span").text(artistData.name);
            });

            //Query the album artwork using song's album id
            var albumId = songData.album;
            $.post('includes/handlers/ajax/getAlbumJson.php', { albumId : albumId}, function(data) {
                var albumData = JSON.parse(data);
                //console.log(albumData);
                $(".albumLink img").attr('src', albumData.artworkPath);
            })
            
            audioElement.setTrack(songData);
            //playSong();
        });

    }

    function playSong() {
        $('.controlButton.play').hide();
        $('.controlButton.pause').show();
        if(audioElement.audio.currentTime == 0) {
            $.post('includes/handlers/ajax/updatePlays.php', { songId : audioElement.currentlyPlaying.id });
        }
        audioElement.play();

    }

    function pauseSong() {
        $('.controlButton.pause').hide();
        $('.controlButton.play').show();
        audioElement.pause();
    }

</script>

<div id="nowPlayingBarContainer">
    <div id="nowPlayingBar">

        <div id="nowPlayingLeft">
            <div class="content">
                <span class="albumLink">
                    <img src="https://i.ytimg.com/vi/rb8Y38eilRM/maxresdefault.jpg" class="albumArtwork">
                </span>

                <div class="trackInfo">

                    <span class="trackName">
                        <span></span>
                    </span>

                    <span class="artistName">
                        <span></span>
                    </span>

                </div>



            </div>
        </div>

        <div id="nowPlayingCenter">

            <div class="content playerControls">

                <div class="buttons">

                    <button class="controlButton shuffle" title="Shuffle button">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle">
                    </button>

                    <button class="controlButton previous" title="Previous button">
                        <img src="assets/images/icons/previous.png" alt="Previous">
                    </button>

                    <button class="controlButton play" title="Play button" onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt="Play">
                    </button>

                    <button class="controlButton pause" title="Pause button" style="display: none;" onclick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="Pause">
                    </button>

                    <button class="controlButton next" title="Next button">
                        <img src="assets/images/icons/next.png" alt="Next">
                    </button>

                    <button class="controlButton repeat" title="Repeat button">
                        <img src="assets/images/icons/repeat.png" alt="Repeat">
                    </button>

                </div>


                <div class="playbackBar">

                    <span class="progressTime current">0.00</span>

                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                    </div>

                    <span class="progressTime remaining">0.00</span>


                </div>


            </div>


        </div>

        <div id="nowPlayingRight">
            <div class="volumeBar">

                <button class="controlButton volume" title="Volume button">
                    <img src="assets/images/icons/volume.png" alt="Volume">
                </button>

                <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>