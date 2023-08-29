<?php

namespace App\Console\Commands;

use App\Services\LineService;
use App\Services\YoutubeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetTrendLatest extends Command
{
    /** 動画情報取得上限数 */
    private const MAX_RESULTS_COUNT = 3;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:get-trend-latest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get trend latest videos without video category on Youtube';

    /**
     * Youtubeサービスインスタンス
     *
     * @var YoutubeService
     */
    private YoutubeService $youtubeService;

    /**
     * LINEサービスインスタンス
     *
     * @var LineService
     */
    private LineService $lineService;

    public function __construct(YoutubeService $youtubeService, LineService $lineService)
    {
        parent::__construct();
        $this->youtubeService = $youtubeService;
        $this->lineService = $lineService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        $videoCategoryId = config('services.youtube.video_category_id.latest');

        try {
            Log::info('Trying to get trend video list on Youtube starts.');
            // 最新急上昇動画の取得
            $youtubeCollection = $this->youtubeService->getTrendVideoList($videoCategoryId, self::MAX_RESULTS_COUNT);
            Log::info('Trying to get trend video list on Youtube ends.');

            if ($youtubeCollection->isEmpty()) {
                $this->alert('Youtube data is nothing.');
            }

            // LINEメッセージの作成
            $message = $this->lineService->createLineMessageForTrendVideo($videoCategoryId, $youtubeCollection);
            Log::info('Trying to send LINE message starts.');
            // LINEメッセージの送信
            $this->lineService->sendMessage($message);
            Log::info('Trying to send LINE message ends.');

            $this->info('The command was successful.');
    
            return Command::SUCCESS;
        } catch (Exception $ex) {
            $this->error('The command was failure.');

            return Command::FAILURE;
        }
    }
}
