<?php

namespace Tests\Unit;

use DateInterval;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ConvertIso8601DurationToTimeTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    /**
     * ISO8601の期間を指定のフォーマットされた時間へ変換
     * 「PT#H#M#S」形式の期間が「H:i:s」形式の時間へ変換されることを確認する
     *
     * @return void
     */
    public function test_convert_iso8601_duration_to_time() : void
    {
        ### Arrange
        $iso8601Times = [];
        // 時、分、秒のみ
        array_push($iso8601Times, 'PT1H', 'PT5M', 'PT10S');
        // 時、分、秒のうち2種類を含む
        array_push($iso8601Times, 'PT1H10M', 'PT15H1S', 'PT3M20S');
        // 時、分、秒全てを含む
        array_push($iso8601Times, 'PT1H10M5S', 'PT2H1M10S', 'PT10H20M30S');

        ### Act
        foreach ($iso8601Times as $iso8601Time) {
            $convertedTime = (new DateInterval($iso8601Time))->format('%H:%I:%S');

            ### Assert
            $this->assertEquals(1, preg_match_all('/([0-9]{2}):([0-9]{2}):([0-9]{2})/', $convertedTime));
        }
    }
}
