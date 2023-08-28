<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineService
{
    /**
     * HTTPクライアントインスタンス
     *
     * @var CurlHTTPClient
     */
    private CurlHTTPClient $httpClient;

    /**
     * LINEBotインスタンス
     *
     * @var LINEBot
     */
    private LINEBot $bot;

    public function __construct()
    {
        $this->httpClient = new CurlHTTPClient(config('services.line.message.access_token'));
        $this->bot = new LINEBot($this->httpClient, ['channelSecret' => config('services.line.message.channel_secret')]);
    }

    /**
     * LINEメッセージを送信
     * MessagingAPIを使用してユーザーにメッセージを送信する
     *
     * @param string $message
     * @return void
     */
    public function sendMessage(string $message = '') : void
    {
        $textMessageBuilder = new TextMessageBuilder($message);

        try {
            $response = $this->bot->pushMessage(config('services.line.developer.user_id'), $textMessageBuilder);
            if (!$response->isSucceeded()) {
                throw new Exception('Message was not sent with any errors.');
            }
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            
            throw new Exception();
        }
    }

    /**
     * 急上昇動画専用のLINEメッセージを生成
     * 
     * @param string $videoCategoryId
     * @param Collection $collection
     * @return string
     */
    public function createLineMessageForTrendVideo(string $videoCategoryId, Collection $collection) : string
    {
        $messageTitle = $this->_createLineMessageTitle($videoCategoryId);
        $messageBody = '';
        $message = '';
        $rankingCount = 1;

        foreach ($collection as $videoInfo) {
            $videoInfoCollection = collect($videoInfo);
            // 指定した要素が含まれているか
            if (
                !$videoInfoCollection->has([
                    'channelTitle',
                    'title',
                    'commentCount',
                    'likeCount',
                    'viewCount',
                    'videoUrl',
                ])) {
                $message = 'Creating LINE Message was failed because invalid property is existed';
                Log::alert($message);

                return $message;
            }
            $privateText = '非公開';
            $channelTitleText = $videoInfoCollection->get('channelTitle');
            $titleText = $videoInfoCollection->get('title');
            // コメント数、高評価数、視聴回数を非公開設定している場合はnullになる
            $commentCount = $videoInfoCollection->get('commentCount');
            $commentCountText = !is_null($commentCount) ? number_format($commentCount) : $privateText;
            $likeCount = $videoInfoCollection->get('likeCount');
            $likeCountText = !is_null($likeCount) ? number_format($likeCount) : $privateText;
            $viewCount = $videoInfoCollection->get('viewCount');
            $viewCountText = !is_null($viewCount) ? (number_format($viewCount) . ' 回') : $privateText;
            $urlText = $videoInfoCollection->get('videoUrl');

            // メッセージを組み立てる
            $messageBody .= <<<EOM
            \n
            -- 第 $rankingCount 位 --
            チャンネル: $channelTitleText
            動画タイトル: $titleText
            視聴回数: $viewCountText
            高評価数: $likeCountText
            コメント数: $commentCountText
            動画URL: $urlText
            ------------
            EOM;
            $rankingCount++;
        }
        $message = sprintf('%s%s', $messageTitle, $messageBody);

        return $message;
    }

    /**
     * LINEメッセージのタイトルを生成
     * 動画カテゴリーIDから動画カテゴリー名を取得して、メッセージタイトルを生成する
     *
     * @param string $videoCategoryId
     * @return string
     */
    private function _createLineMessageTitle(string $videoCategoryId) : string
    {
        $videoCategoryName = '';
        switch (true) {
            case $videoCategoryId === config('services.youtube.video_category_id.latest') :
                $videoCategoryName = '最新';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.movie_and_anime') :
                $videoCategoryName = '映画・アニメ';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.bicycle_and_vehicle') :
                $videoCategoryName = '乗り物';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.music') :
                $videoCategoryName = '音楽';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.pets_and_animal') :
                $videoCategoryName = 'ペット・動物';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.sports') :
                $videoCategoryName = 'スポーツ';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.game') :
                $videoCategoryName = 'ゲーム';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.blog') :
                $videoCategoryName = 'ブログ';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.comedy') :
                $videoCategoryName = 'お笑い';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.entertainment') :
                $videoCategoryName = 'エンタメ';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.news_and_government') :
                $videoCategoryName = 'ニュース';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.how_to_style') :
                $videoCategoryName = 'ハウツー';
                break;
            case $videoCategoryId === config('services.youtube.video_category_id.science_and_technology') :
                $videoCategoryName = 'テクノロジー・化学';
                break;
            default :
                $videoCategoryName = '???';
                break;
        }

        return sprintf('今日の急上昇%s動画はこちら', $videoCategoryName);
    }
}