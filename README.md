# YoutubeApp

# 概要
Youtube Data Api v3を使用し、Youtubeから動画情報を取得してLINEに通知させます。<br>

スケジュールの設定を行っているため、Laravelのコマンドスケジューラによる実行、<br>
またはcronを利用した定期実行が可能です。

# 事前準備

### YouTube Data API v3の取得
1. Google Cloudコンソールにアクセスする
2. プロジェクトを作成する
3. YouTube Data API v3を有効化する
4. APIキーを発行する（APIキーを控えておく）
5. 適宜、API用途の制限を設定する

ガイドライン: https://developers.google.com/youtube/v3/getting-started?hl=ja

### Messaging APIの取得
1. LINE Developersコンソールにログインする
2. プロバイダーを作成する
3. チャネルを作成する
4. チャネル情報の下記項目を控えておく
 - チャネルアクセストークン
 - チャネルID
 - チャネルシークレット
 - あなたのユーザーID
5. 適宜、API用途の制限を設定する

ガイドライン: https://developers.line.biz/ja/docs/messaging-api/getting-started/#using-console

# 使用方法
1. Laravelプロジェクトフォルダ内の.envファイルに下記項目を設定する
 - GOOGLE_API_KEY → YouTube Data APIキー
 - LINE_DEVELOPER_USER_ID → あなたのユーザーID
 - LINE_MESSAGE_CHANNEL_ID → チャネルID
 - LINE_MESSAGE_CHANNEL_SECRET → チャネルシークレット
 - LINE_MESSAGE_CHANNEL_TOKEN → チャネルアクセストークン

2. コンソールでArtisanコマンドを使用し、Youtube上の動画情報を取得する

```
# コマンド例
$ php artisan youtube:get-trend-music
```