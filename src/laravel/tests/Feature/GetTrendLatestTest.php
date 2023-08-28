<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GetTrendLatestTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('config:clear');
        Artisan::call('migrate:refresh --env=testing');
    }

    /**
     * 最新急上昇動画取得テスト
     * Youtubeの急上昇動画を取得した後に、LINEに動画情報の通知が来ることを確認する
     *
     * @return void
     */
    public function test_get_trend_latest() : void
    {
        ### Arrange
        // Nothing to do.
        
        ### Act
        $resultCode = Artisan::call('youtube:get-trend-latest');

        ## Assert
        $this->assertSame($resultCode, 1);
    }
}
