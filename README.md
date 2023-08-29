# YoutubeApp

## 概要

YouTube Data Api v3を使用し、YouTubeから動画情報を取得してLINEにプッシュ通知させます。<br>

スケジュールの設定を行っているため、Laravelのコマンドスケジューラによる実行、<br>
またはcronを利用した定期実行が可能です。

## 事前準備

> [!NOTE]
> Docker、Laravelにおける初回の環境設定についてはSettings.mdファイルを参照

### YouTube Data API v3の取得

1. Google Cloudコンソールにアクセスする
2. プロジェクトを作成する
3. YouTube Data API v3を有効化する
4. APIキーを発行する（APIキーを控えておく）
5. 適宜、API用途の制限を設定する

ガイドライン: https://developers.google.com/youtube/v3/getting-started?hl=ja

---

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

---

### Laravel側の設定

1. .envファイルの下記項目を設定する

|キー|値|
|:-----|:-----|
|GOOGLE_API_KEY|YouTube Data API v3キー|
|LINE_DEVELOPER_USER_ID|あなたのユーザーID|
|LINE_MESSAGE_CHANNEL_ID|Dockerの.チャネルID|
|LINE_MESSAGE_CHANNEL_SECRET|チャネルシークレット
|LINE_MESSAGE_CHANNEL_TOKEN|チャネルアクセストークン

2. 下記コマンドを実行する

``` terminal
$ php artisan cache:clear
```

---

## 使用方法（ローカル環境）

1. Laravelプロジェクトディレクトリに移動し、下記コマンドを実行する

``` terminal
# コマンド例
$ php artisan youtube:get-trend-latest
```

## YouTubeデータ取得コマンド一覧
- 動画のカテゴリーを問わず、最新の急上昇動画を取得

```
$ php artisan youtube:get-trend-latest
```

- 音楽の急上昇動画を取得

```
$ php artisan youtube:get-trend-music
```
