<?php

namespace DreamTeam\Base\Supports;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Youtube
{
    public static function getYoutubeVideoEmbedURL(string $url): string
    {
        $url = rtrim($url, '/');

        if (Str::contains($url, 'watch?v=')) {
            $url = str_replace('watch?v=', 'embed/', $url);
        } else {
            $exploded = explode('/', $url);

            if (count($exploded) > 1) {
                $videoID = str_replace('embed', '', str_replace('watch?v=', '', Arr::last($exploded)));

                $url = 'https://www.youtube.com/embed/' . $videoID;
            }
        }

        return $url;
    }

    public static function getYoutubeWatchURL(string $url): string
    {
        $url = rtrim($url, '/');

        if (Str::contains($url, 'embed/')) {
            $url = str_replace('embed/', 'watch?v=', $url);
        } else {
            $exploded = explode('/', $url);

            if (count($exploded) > 1) {
                $videoID = str_replace('embed', '', str_replace('watch?v=', '', Arr::last($exploded)));

                $url = 'https://www.youtube.com/watch?v=' . $videoID;
            }
        }

        return $url;
    }

    public static function getYoutubeVideoID(string $url): ?string
    {
        $regExp = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/';

        preg_match($regExp, $url, $matches);

        if ($matches && strlen($matches[7]) == 11) {
            return $matches[7];
        }

        return null;
    }

    public static function isYoutubeURL(string $url): bool
    {
        $regExp = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';

        return preg_match($regExp, $url);
    }
}
