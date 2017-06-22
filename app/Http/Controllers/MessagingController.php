<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\MMSMedia;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Magyarjeti\MimeTypes\MimeTypeConverter;
use Twilio\Rest\Client;
use Twilio\Twiml;

class MessagingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Messaging Controller
    |--------------------------------------------------------------------------
    |
    | This controller receives messages from Twilio and makes the media available
    | via the /images url.
    */

    protected $twilio;
    protected $accountSid;
    protected $twilioNumber;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->accountSid = env('TWILIO_ACCOUNT_SID');
        $this->twilioNumber = env('TWILIO_NUMBER');
        $authToken = env('TWILIO_AUTH_TOKEN');

        $this->twilio = new Client($this->accountSid, $authToken);
    }

    public function handleIncomingSMS(Request $request)
    {
        $converter = new MimeTypeConverter;
        $NumMedia = (int)$request->input('NumMedia');
        $FromNumber = $request->input('From');
        $MessageSid = $request->input('MessageSid');

        for ($i=0; $i < $NumMedia; $i++) {
            $mediaUrl = $request->input("MediaUrl$i");
            $MIMEType = $request->input("MediaContentType$i");
            $fileExtension = $converter->toExtension($MIMEType);
            $mediaSid = basename($mediaUrl);

            $media = file_get_contents($mediaUrl);
            $filename = "$mediaSid.$fileExtension";

            $mediaData = compact('mediaSid', 'MessageSid', 'mediaUrl', 'media', 'filename', 'MIMEType');
            $mmsMedia = new MMSMedia($mediaData);
            $mmsMedia->save();
        }

        $response = new Twiml();
        $messageBody = $NumMedia == 0 ? 'Send us an image!' : "Thanks for the $NumMedia images.";
        $response->message([
          'from' => $FromNumber,
          'to' => $FromNumber,
          'body' => $messageBody
        ]);

        return (string)$response;
    }

    public function deleteMediaFromTwilio($mediaItem)
    {
        return $this->twilio->api->accounts($this->accountSid)
            ->messages($mediaItem['MessageSid'])
            ->media($mediaItem['mediaSid'])
            ->delete();
    }

    public function allMedia()
    {
        $mediaItems = MMSMedia::all();
        return $mediaItems;
    }

    public function getMediaFile($filename, Response $response)
    {
        $media = MMSMedia::where('filename', $filename)->firstOrFail();
        $fileContents = $media['media'];
        $MessageSid = $media['MessageSid'];
        $mediaSid = $media['mediaSid'];
        $MIMEType = $media['MIMEType'];

        $media->delete();
        $this->deleteMediaFromTwilio(compact('mediaSid', 'MessageSid'));

        return response($fileContents, 200)
            ->header('Content-Type', $MIMEType);
    }

    public function config()
    {
        return ['twilioNumber' => $this->twilioNumber];
    }
}
