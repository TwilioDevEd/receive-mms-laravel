<?php

namespace Tests\Unit;
use Mockery;
use App;
use App\Http\Controllers\MessagingController;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class MessageControllerTest extends TestCase
{
    protected $MediaMessageServiceMock;

    protected function setUp()
    {
        parent::setUp();
        $this->TwilioMock = Mockery::mock('Twilio\Rest\Client');

        $this->MediaMessageServiceMock = Mockery::mock('App\Services\MediaMessageService\MediaMessageService[getMediaContent]');
        $this->MediaMessageServiceMock->shouldReceive('getMediaContent')
            ->andReturn('Content string of a media file');
        App::instance('App\Services\MediaMessageService\MediaMessageService', $this->MediaMessageServiceMock);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHandleIncomingSMSWhenMediaIsSent()
    {
        $messageController = new MessagingController();
        // get request body stub from file
        $incomingSMSJson =
        file_get_contents('./tests/fixtures/IncomingSMS.json');
        $request = new Request(json_decode($incomingSMSJson, true));
        $fakeResponse = $messageController->handleIncomingSMS($request, $this->MediaMessageServiceMock);
        
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
        "<Response><Message from=\"+1707XXXXXXX\" to=\"+1707XXXXXXX\">" .
        "<Body>Thanks for the 1 images.</Body></Message></Response>\n", $fakeResponse);
    }

    public function testHandleIncomingSMSWhenNoMediaIsSent()
    {
        $messageController = new MessagingController();
        // get request body stub from file
        $incomingSMSJson =
        file_get_contents('./tests/fixtures/IncomingSMS.json');
        $request = new Request(json_decode($incomingSMSJson, true));
        $request['NumMedia'] = '0';
        $fakeResponse = $messageController->handleIncomingSMS($request, $this->MediaMessageServiceMock);
        
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
        "<Response><Message from=\"+1707XXXXXXX\" to=\"+1707XXXXXXX\">" .
        "<Body>Send us an image!</Body></Message></Response>\n", $fakeResponse);
    }
}
