## アプリケーション名
coachtechフリマ

## 環境構築
```
リポジトリからダウンロード
git clone git@github.com:yurin0617/yurin-mogi1.git

ダウンロードしたディレクトリの中にあるsrcディレクトリにある
「.env.example」をコピーして「.env」を作成し DBの設定を変更
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

dockerコンテナを構築
docker-compose up -d --build

phpコンテナにログインしてLaravelをインストール
docker-compose exec php bash
composer install

アプリケーションキーを作成
php artisan key:generate

DBのテーブルを作成
php artisan migrate

DBのテーブルにダミーデータを投入
php artisan db:seed

"The stream or file could not be opened"エラーが発生した場合
srcディレクトリにあるstorageディレクトリに権限を設定
chmod -R 777 storage

アップロードした画像をみれるようにするためシンボリックリンクを作成
php artisan storage:link
```

## テスト環境
```
testDB作成
PHPコンテナにログインし、.envをコピーして.env.testingというファイルを作成
.env.testingの作成
cp .env .env.testing

ファイルの作成ができたたら、.env.testingファイルの文頭部分にあるAPP_ENVとAPP_KEYを編集
APP_ENV=test
APP_KEY=
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root

先ほど「空」にしたAPP_KEYに新たなテスト用のアプリケーションキーを加えるために
php artisan key:generate --env=testingを実行
php artisan config:clearキャッシュの削除のため実行

php artisan migrate --env=testing
php artisan db:seed --env=testingの実行

テストの実行
php  artisan test
```

## 使用技術(実行環境)
```
PHP：8.1.34
Laravel：8.83.8
MySQL：8.0.26
Ngnix：1.21.1
MailHog：1.0.1
phpMyAdmin：5.2.3
```

## URL
```
ユーザー登録画面：http://localhost/register
ログイン画面：http://localhost/login
商品一覧画面：http://localhost
```

## ER図
![ER図](ER.drawio.png)
