<?php

namespace App\Service;

class YoutubeVideoCheck
{
    public function isAYouTubeVideo($video) {
        $videoHttpsUrl = substr($video, 0, 24); // https://www.youtube.com/
        $videoWwwUrl = substr($video, 0, 16); // www.youtube.com/ 
        $videoSimpleUrl = substr($video, 0, 12); // youtube.com/ 

        if ( $videoHttpsUrl == 'https://www.youtube.com/' ) {
            $resp = substr($video, -11);
        } else if ( $videoWwwUrl == 'www.youtube.com/' ) {
            $resp = substr($video, -11);
        } else if ( $videoSimpleUrl == 'youtube.com/' )  {
            $resp = substr($video, -11);
        } else {
            $resp = "Veuillez importer une vidéo depuis lien YouTube entier";
        }
        return $resp;
    }
}