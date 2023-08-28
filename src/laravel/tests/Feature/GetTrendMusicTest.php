<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GetTrendMusicTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('config:clear');
        Artisan::call('migrate:refresh --env=testing');
    }

    /**
     * 急上昇音楽動画取得テスト
     * Youtubeの急上昇動画を取得した後に、LINEに動画情報の通知が来ることを確認する
     *
     * @return void
     */
    public function test_get_trend_music() : void
    {
        ### Arrange
        // Nothing to do.
        
        ### Act
        $resultCode = Artisan::call('youtube:get-trend-music');

        ## Assert
        $this->assertSame($resultCode, 1);
    }
}
