<?php

function convertYoutubeDuration($duration)
{
    $date = new DateTime('00:00');
    $date->add(new DateInterval($duration));
    // Return 00:00:00 foramt
    return $date->format('H:i:s');
}