<?php

namespace Tests\Unit;

use App\Repositories\Video\VideoRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class VideoRepositoryTest extends TestCase
{
    /**
     * 動画リポジトリインスタンス
     *
     * @var VideoRepository
     */
    private VideoRepository $videoRepository;

    public function __construct()
    {
        parent::__construct();
        $this->videoRepository = new VideoRepository();
    }

    public function setUp() : void
    {
        parent::setUp();
        Artisan::call('config:clear');
        Artisan::call('migrate:refresh --env=testing');
    }

    public function test_store_trend_video()
    {
        ### Arrange
        $videoCollection = collect([
            [
                // id情報
                'videoId' => 'a',
                // snippet情報
                'publishedAt' => Carbon::now('Asia/Tokyo'),
                'channelId' => 'channel 1',
                'title' => 'jfoa;jajg;gjrea',
                'description' => 'fiuhergifehsag;erae',
                'thumbnailUrl' => 'https://sample.com/thumbnail.png',
                'categoryId' => '11',
                'channelTitle' => 'kglsjgoes',
                // statistics情報
                'commentCount' => 0,
                'dislikeCount' => 0,
                'favoriteCount' => 0,
                'likeCount' => 0,
                'viewCount' => 0,
                // status情報
                'privacyStatus' => 'gsrgjs',
                'uploadStatus' => 'ios;hsmt',
                // contentDetails情報
                'caption' => 'irgj;ses',
                'duration' => '00:05:00',
                // 独自情報
                'videoUrl' => 'https://sample.com',
            ],
            [
                // id情報
                'videoId' => 'a',
                // snippet情報
                'publishedAt' => Carbon::now('Asia/Tokyo'),
                'channelId' => 'channel 2',
                'title' => 'jfoa;jajg;gjrea',
                'description' => 'fiuhergifehsag;erae',
                'thumbnailUrl' => 'https://sample.com/thumbnail.png',
                'categoryId' => '11',
                'channelTitle' => 'kglsjgoes',
                // statistics情報
                'commentCount' => 0,
                'dislikeCount' => 0,
                'favoriteCount' => 0,
                'likeCount' => 0,
                'viewCount' => 0,
                // status情報
                'privacyStatus' => 'gsrgjs',
                'uploadStatus' => 'ios;hsmt',
                // contentDetails情報
                'caption' => 'irgj;ses',
                'duration' => '00:05:00',
                // 独自情報
                'videoUrl' => 'https://sample.com',
            ],
            [
                // id情報
                'videoId' => 'a',
                // snippet情報
                'publishedAt' => Carbon::now('Asia/Tokyo'),
                'channelId' => 'channel 3',
                'title' => 'jfoa;jajg;gjrea',
                'description' => 'fiuhergifehsag;erae',
                'thumbnailUrl' => 'https://sample.com/thumbnail.png',
                'categoryId' => '11',
                'channelTitle' => 'kglsjgoes',
                // statistics情報
                'commentCount' => 0,
                'dislikeCount' => 0,
                'favoriteCount' => 0,
                'likeCount' => 0,
                'viewCount' => 0,
                // status情報
                'privacyStatus' => 'gsrgjs',
                'uploadStatus' => 'ios;hsmt',
                // contentDetails情報
                'caption' => 'irgj;ses',
                'duration' => '00:05:00',
                // 独自情報
                'videoUrl' => 'https://sample.com',
            ],
        ]);
        
        ### Act
        $this->videoRepository->storeTrendVideo($videoCollection);

        ### Assert
        $this->assertDatabaseCount('videos', 3);
        $this->assertDatabaseCount('channels', 3);
    }
}
