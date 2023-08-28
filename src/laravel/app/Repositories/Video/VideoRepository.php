<?php

namespace App\Repositories\Video;

use App\Models\Channel;
use App\Models\Video;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VideoRepository
{
    /**
     * 急上昇動画情報の保存
     * チャンネル情報の登録も併せて実施している
     *
     * @param Collection $collection
     * @return void
     */
    public function storeTrendVideo(Collection $collection) : void
    {
        $videoCollection = collect();
        $channelCollection = collect();
        foreach ($collection as $videoInfo) {
            $videoCollection->add([
                'video_id' => $videoInfo['videoId'],
                'title' => $videoInfo['title'],
                'category_id' => $videoInfo['categoryId'],
                'channel_id' => $videoInfo['channelId'],
                'description' => $videoInfo['description'],
                'duration' => $videoInfo['duration'],
                'caption' => $videoInfo['caption'],
                'published_at' => (new Carbon($videoInfo['publishedAt']))->timezone('Asia/Tokyo')->__toString(),
                'upload_status' => $videoInfo['uploadStatus'],
                'privacy_status' => $videoInfo['privacyStatus'],
                'video_url' => $videoInfo['videoUrl'],
                'thumbnail_url' => $videoInfo['thumbnailUrl'],
                'view_count' => null,
                'like_count' => null,
                'dislike_count' => null,
                'favorite_count' => $videoInfo['favoriteCount'],
                'comment_count' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $channelCollection->add([
                'channel_id' => $videoInfo['channelId'],
                'name' => $videoInfo['channelTitle'],
                'title' => '',
                'description' => '',
                'channel_url' => '',
                'thumbnail_url' => '',
            ]);
        }

        DB::beginTransaction();
        try {
            // 動画情報の登録
            Video::insert($videoCollection->toArray());
            // チャンネル情報の登録または更新
            Channel::upsert($channelCollection->toArray(), ['channelId'], ['name', 'title']);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();

            throw new Exception($ex->getMessage());
        }
    }
}