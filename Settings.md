# Docker、Laravelの環境設定（初回のみ）

## Docker

1. .env.exampleファイルがあるディレクトリで下記コマンドを実行する（.envファイル作成）

``` terminal
$ cp .env.example .env
```

2. .envファイルの下記項目を設定する

|キー|値|
|:-----|:-----|
|DB_NAME | DB名|
|DB_USER | ユーザー名|
|DB_PASSWORD | ユーザーパスワード|
|DB_ROOT_PASSWORD | ルートパスワード|
|PMA_ARBITRARY | 1|
|PMA_HOST | mysql|

3. docker-compose.ymlファイルがあるディレクトリで下記コマンドを実行する（コンテナ起動）

``` yml
$ docker-compose up -d --build
```

## Laravel
1. プロジェクトディレクトリで下記コマンドを実行する（パッケージ導入）

``` terminal
$ composer install
```

2. .env.exampleファイルがあるディレクトリで下記コマンドを実行する（.envファイル作成）

``` terminal
$ cp .env.example .env
```

3. .envファイルの下記項目を設定する

|キー|値|
|:-----|:-----|
|DB_DATABASE|Dockerの.envで設定したDB名|
|DB_USERNAME|Dockerの.envで設定したユーザー名|
|DB_PASSWORD|Dockerの.envで設定したパスワード|

4. Laravelプロジェクトのディレクトリで下記コマンドを実行する（アプリキー発行、設定反映）

``` terminal
$ php artisan key:generate
$ php artisan config:clear
$ php artisan cache:clear
```
