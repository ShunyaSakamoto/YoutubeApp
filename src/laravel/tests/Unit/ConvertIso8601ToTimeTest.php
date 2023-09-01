<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConvertIso8601ToTimeTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }

    /**
     * ISO8601仕様の期間を指定のフォーマットされた時間へ変換
     * 「PT#H#M#S」形式の期間が「h:i:s」形式の時間へ変換されることを確認する
     *
     * @return void
     */
    public function test_convert_iso8601_to_time() : void
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
            preg_match_all('/PT(([0-9]){1,2}H)?(([0-9]{1,2})M)?(([0-9]{1,2})S)?/', $iso8601Time, $matches);
            $hour = Str::contains($iso8601Time, 'H') ? $matches[2][0] : '00';
            $minute = Str::contains($iso8601Time, 'M') ? $matches[4][0] : '00';
            $second = Str::contains($iso8601Time, 'S') ? $matches[5][0] : '00';
            $convertedTime = sprintf('%02d:%02d:%02d', $hour, $minute, $second);

            ### Assert
            $this->assertEquals(1, preg_match_all('/([0-9]{2}):([0-9]{2}):([0-9]{2})/', $convertedTime));
        }
    }
}
