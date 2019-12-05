<?php

namespace App\Services\MediaMessageService;

class MediaMessageService implements IMediaMessageService
{
    public function getMediaContent($mediaUrl)
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $mediaUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      // Option to follow the redirects, otherwise it will return an XML
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      $media = curl_exec($ch);
      curl_close($ch);
      return $media;
    }
}
