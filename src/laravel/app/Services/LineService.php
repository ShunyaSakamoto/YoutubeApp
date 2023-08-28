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
     * 急上昇動画専用のLINEメッセージを作成
     *
     * @param Collection $collection
     * @return string
     */
    public function createLineMessageForTrendVideo(Collection $collection) : string
    {
        $rankingCount = 1;
        $message = '今日の急上昇ミュージックビデオはこちら';

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
            $message .= <<<EOM
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

        return $message;
    }
}