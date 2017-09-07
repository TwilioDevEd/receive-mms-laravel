<?php

namespace Tests\Feature;

use Mockery;
use App;
use App\Http\Controllers\MessagingController;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class IncomingSMSTest extends TestCase
{
    // resets db for each test.
    use DatabaseMigrations;
    protected $incomingSMS;
    protected $MessagingControllerMock;
    protected $twilioNumber;

    protected function setUp()
    {
        parent::setUp();
        // get request body stub from file
        $incomingSMSJson =
        file_get_contents('./tests/fixtures/IncomingSMS.json');
        $this->incomingSMS = json_decode($incomingSMSJson, true);
        env('TWILIO_NUMBER', $this->incomingSMS['To']);
        $this->twilioNumber = env('TWILIO_NUMBER');

        $this->TwilioMock = Mockery::mock('Twilio\Rest\Client');

        // A partial mock of the controller deleteMediaFromTwilio method
        $this->MessagingControllerMock = Mockery::mock('App\Http\Controllers\MessagingController[deleteMediaFromTwilio]');
        $this->MessagingControllerMock->shouldReceive('deleteMediaFromTwilio')
        ->with([
          'mediaSid' => 'whale.jpg',
          'MessageSid' => 'MMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
        ]);
        App::instance('App\Http\Controllers\MessagingController', $this->MessagingControllerMock);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    public function testMediaIsSavedToDb()
    {
        $response = $this->post('/api/incoming', $this->incomingSMS);
        $response->assertStatus(200);

        $this->assertDatabaseHas('m_m_s_media', [
          'MessageSid' => $this->incomingSMS['MessageSid']
        ]);
    }

    public function testCorrectTwimlResponseWhenMediaIsSent()
    {
        $response = $this->post('/api/incoming', $this->incomingSMS);
        $response->assertStatus(200);

        $response->assertSee(
          "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
          "<Response><Message from=\"+1707XXXXXXX\" to=\"+1707XXXXXXX\">" .
          "<Body>Thanks for the 1 images.</Body></Message></Response>");
    }

    public function testCorrectTwimlResponseWhenNoMediaIsSent()
    {
        $this->incomingSMS['NumMedia'] = '0';
        $response = $this->post('/api/incoming', $this->incomingSMS);
        $response->assertStatus(200);

        $response->assertSee(
          "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
          "<Response><Message from=\"+1707XXXXXXX\" to=\"+1707XXXXXXX\">" .
          "<Body>Send us an image!</Body></Message></Response>");
    }

    public function testGetAllMedia()
    {
        $messageSid = $this->incomingSMS['MessageSid'];

        $this->post('/api/incoming', $this->incomingSMS);
        $response = $this->get('/api/media');
        $response->assertStatus(200);


        $this->assertDatabaseHas('m_m_s_media', [
          'MessageSid' => $messageSid
        ]);

        $response->assertJsonStructure(['*' => [
            'id',
            'created_at',
            'updated_at',
            'mediaSid',
            'MessageSid',
            'mediaUrl',
            'filename']
        ]);
    }

    public function testGetMediaFile()
    {
        $this->post('/api/incoming', $this->incomingSMS);

        $messageSid = $this->incomingSMS['MessageSid'];
        $filename = 'whale.jpg.jpeg';
        $url = "/api/media/$filename";
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    public function testGetConfig()
    {
        $response = $this->get('/api/config');

        $response->assertExactJson(['twilioNumber' => $this->twilioNumber]);
    }
}
