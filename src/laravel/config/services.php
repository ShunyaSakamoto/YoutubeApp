<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'key' => env('GOOGLE_API_KEY'),
    ],

    'youtube' => [     
        'order' => [
            // 作成日の降順
            'date' => 'date',
            // 高評価順
            'rating' => 'rating',
            // 検索クエリとの関連性に基づいてソート
            'relevance' => 'relevance',
            // タイトルのアルファベット順
            'title' => 'title',
            // アップロード動画数の多い順
            'video_count' => 'videoCount',
            // 再生回数の多い順
            'view_count' => 'viewCount',
        ],
        
        'type' => [
            'video' => 'video',
            'channel' => 'channel',
            'playlist' => 'playlist',
            'all' => 'video,channel,playlist',
        ],

        // サポートされていない動画カテゴリーはコメントアウトしている
        'video_category_id' => [
            'movie_and_anime' => '1',
            'bicycle_and_vehicle' => '2',
            'music' => '10',
            'pets_and_animal' => '15',
            'sports' => '17',
            // 'travel_and_event' => '19',
            'game' => '20',
            'blog' => '22',
            'comedy' => '23',
            'entertainment' => '24',
            'news_and_government' => '25',
            'how_to_style' => '26',
            // 'education' => '27',
            'science_and_technology' => '28',
            // 'social_activity' => '29',
            // 'animation' => '30',
            // 'entertainment_news' => '43',
            // 'trailer' => '44',
        ],
        
    ],

    'line' => [
        'developer' => [
            'user_id' => env('LINE_DEVELOPER_USER_ID'),
        ],

        'message' => [
            'channel_id'=>env('LINE_MESSAGE_CHANNEL_ID'),
            'channel_secret' => env('LINE_MESSAGE_CHANNEL_SECRET'),
            'access_token' => env('LINE_MESSAGE_CHANNEL_TOKEN'),
        ],

    ],

];
