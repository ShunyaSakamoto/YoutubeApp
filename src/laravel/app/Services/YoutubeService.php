<?php

namespace App\Services;

use App\Repositories\Video\VideoRepository;
use DateInterval;
use Exception;
use Google_Client;
use Google_Exception;
use Google_Service_Exception;
use Google_Service_YouTube;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class YoutubeService
{
    /**
     * Googleクライアントインスタンス
     *
     * @var Google_Client
     */
    private Google_Client $client;

    /**
     * Youtubeデータアクセスインスタンス
     *
     * @var Google_Service_Youtube
     */
    private Google_Service_Youtube $youtube;

    /**
     * 動画リポジトリインスタンス
     *
     * @var VideoRepository
     */
    private VideoRepository $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->client = new Google_Client();
        $this->client->setDeveloperKey(config('services.google.key'));
        $this->youtube = new Google_Service_Youtube($this->client);
        $this->videoRepository = $videoRepository;
    }

    /**
     * 日本の急上昇動画情報を取得
     *
     * @param string $videoCategoryId
     * @param int $maxResultsCount
     * @return Collection
     */
    public function getTrendVideoList(string $videoCategoryId, int $maxResultsCount) : Collection
    {
        $parts = [
            'id',               // 動画ID
            'snippet',          // タイトル、説明、カテゴリなどの動画に関する基本情報
            'statistics',       // 高評価数やコメント数などの動画に関する統計情報
            'status',           // 動画のアップロード、処理、プライバシーのステータスに関する情報
            'contentDetails',   // 動画の長さや字幕が表示されるかなどの動画コンテンツに関する情報
        ];
        $parts = implode(',', $parts);
        $filter = [
            'chart' => 'mostPopular',
            'maxResults' => $maxResultsCount,
            'regionCode' => 'JP',
            'videoCategoryId' => $videoCategoryId,
        ];

        try {
            // 動画情報の取得
            $items = $this->youtube->videos->listVideos($parts, $filter);
            $youtubeCollection = collect($items->getItems());

            // それぞれのリソースを1つのコレクションにまとめる
            $mergedCollection = $this->_createYoutubeCollection($youtubeCollection, $maxResultsCount);
            // 取得データのログを取る
            Log::debug('Youtube Data Collection: ', $mergedCollection->toArray());

            // 動画情報をDBに保存する
            $this->videoRepository->storeTrendVideo($mergedCollection);

            return $mergedCollection;
        } catch (Google_Service_Exception $ex) {
            Log::error('Catch Google Service Exception: ' . $ex->getMessage());

            throw new Exception('Catch Google Service Exception.');
        } catch (Google_Exception $ex) {
            Log::error('Catch Google Exception: ' . $ex->getMessage());

            throw new Exception('Catch Google Exception.');
        } catch (Exception $ex) {
            Log::error('Catch Exception: ' . $ex->getMessage());

            throw new Exception('Catch Exception.');
        }
    }

    /**
     * APIで取得した各プロパティー情報を1つのコレクションに集約
     *
     * @param Collection $collection
     * @param int $maxResultsCount
     * @return Collection
     */
    private function _createYoutubeCollection(Collection $collection, int $maxResultsCount) : Collection
    {
        $videoIds = $collection->pluck('id');
        $snippets = $collection->pluck('snippet');
        $statistics = $collection->pluck('statistics');
        $statusInfos = $collection->pluck('status');
        $contentDetails = $collection->pluck('contentDetails');
        $mergedCollection = collect();

        for ($i = 0; $i < $maxResultsCount; $i++) {
            $videoId = $videoIds[$i];
            $snippet = $snippets[$i];
            $statistic = $statistics[$i];
            $statusInfo = $statusInfos[$i];
            $contentDetail = $contentDetails[$i];
            $thumbnail_url = '';

            // サムネイルURLの取得（取得件数は必ず1件）
            foreach ($snippet->thumbnails as $thumbnail) {
                $thumbnail_url = $thumbnail->url;

                break;
            }
            $mergedCollection->add([
                // id情報
                'videoId' => $videoId,
                // snippet情報
                'publishedAt' => $snippet->publishedAt,
                'channelId' => $snippet->channelId,
                'title' => $snippet->title,
                'description' => $snippet->description,
                'thumbnailUrl' => $thumbnail_url,
                'categoryId' => $snippet->categoryId,
                'channelTitle' => $snippet->channelTitle,
                // statistics情報
                'commentCount' => $statistic->commentCount,
                'dislikeCount' => $statistic->dislikeCount,
                'favoriteCount' => $statistic->favoriteCount,
                'likeCount' => $statistic->likeCount,
                'viewCount' => $statistic->viewCount,
                // status情報
                'privacyStatus' => $statusInfo->privacyStatus,
                'uploadStatus' => $statusInfo->uploadStatus,
                // contentDetails情報
                'caption' => $contentDetail->caption,
                'duration' => $this->_convertIso8601DurationToTime($contentDetail->duration),
                // 独自情報
                'videoUrl' => $this->_createVideoUrl($videoId),
            ]);
        }

        return $mergedCollection;
    }

    /**
     * ISO8601の期間を指定のフォーマットされた時間へ変換
     * 動画の長さは「PT#H#M#S」形式であるため、「H:i:s」形式へ変換する
     *
     * @param string $duration
     * @return string
     */
    private function _convertIso8601DurationToTime(string $duration) : string
    {
        return (new DateInterval($duration))->format('%H:%I:%S');
    }

    /**
     * 動画URLを生成
     *
     * @param string $videoId
     * @return string
     */
    private function _createVideoUrl(string $videoId) : string
    {
        $protocol = 'https';
        $domain = 'www.youtube.com'; 
        $parameter = http_build_query(['v' => $videoId]);

        return sprintf('%s://%s/watch?%s', $protocol, $domain, $parameter);
    }
}