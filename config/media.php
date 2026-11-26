<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Image Uploads
    |--------------------------------------------------------------------------
    |
    | Default constraints for ValidImage. max_filesize is in kilobytes;
    | max_width and max_height are in pixels.
    |
    */

    'image' => [
        'max_filesize' => 1024,
        'max_width'    => 4096,
        'max_height'   => 4096,
        'mimes'        => ['jpg', 'jpeg', 'png', 'webp', 'avif'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Uploads
    |--------------------------------------------------------------------------
    |
    | Default constraints for ValidVideo. max_filesize is in kilobytes.
    |
    */

    'video' => [
        'max_filesize' => 10240,
        'mimes'        => ['mp4', 'webm', 'mov'],
    ],

];
