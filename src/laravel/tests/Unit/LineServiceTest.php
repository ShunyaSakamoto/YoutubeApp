<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineServiceTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    public function test_send_line_message() : void
    {
        ### Arrange
        $http_client = new CurlHTTPClient(config('services.line.message.access_token'));
        $bot = new LINEBot($http_client, ['channelSecret' => config('services.line.message.channel_secret')]);
        $message = "LINE送信テスト";
        $textMessageBuilder = new TextMessageBuilder($message);

        ### Act
        $response = $bot->pushMessage(config('services.line.developer.user_id'), $textMessageBuilder);

        ## Assert
        $this->assertNotNull($http_client);
        $this->assertNotNull($bot);
        $this->assertNotNull($textMessageBuilder);
        $this->assertTrue($response->isSucceeded());
    }
}
