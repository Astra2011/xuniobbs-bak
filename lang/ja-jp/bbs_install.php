<?php

return array(
	'installed_tips' => 'プログラムはインストール済みです。再インストールするには conf/conf.php と install/install.lock を削除してください！',
	'please_set_conf_file_writable' => 'conf/conf.php ファイルを書き込み可能に設定してください！',
	'evn_not_support_php_mysql' => '現在のPHP環境は mysql と pdo_mysql をサポートしていません。インストールを続行できません。',
	'dbhost_is_empty' => 'データベースホストを空にすることはできません',
	'dbhost_is_invalid' => 'データベースホストの形式が正しくありません。英数字、ドット、ハイフン、アンダースコア、ポートのみ使用できます',
	'dbname_is_empty' => 'データベース名を空にすることはできません',
	'dbname_is_invalid' => 'データベース名の形式が正しくありません。英数字、ハイフン、アンダースコアのみ、64 文字以内です',
	'dbuser_is_empty' => 'ユーザー名を空にすることはできません',
	'dbuser_is_invalid' => 'データベースユーザー名の形式が正しくありません',
	'dbtype_is_invalid' => 'データベースタイプが正しくありません',
	'adminuser_is_empty' => '管理者ユーザー名を空にすることはできません',
	'adminpass_is_empty' => '管理者パスワードを空にすることはできません',
	'adminemail_is_empty' => '管理者メールアドレスを空にすることはできません',
	'conguralation_installed' => 'おめでとうございます、インストールに成功しました！安全のため install ディレクトリを削除してください。',
	
	'step_1_title' => '一、インストール環境チェック',
	'runtime_env_check' => 'サイト実行環境チェック',
	'required' => '必要',
	'current' => '現在',
	'check_result' => 'チェック結果',
	'passed' => '合格',
	'not_passed' => '合格',
	'not_the_best' => '最適な環境ではありません',
	'dir_writable_check' => 'ディレクトリ / ファイルの権限チェック',
	'writable' => '書き込み可',
	'unwritable' => '書き込み不可',
	'check_again' => '再チェック',
	'os' => 'オペレーティングシステム',
	'unix_like' => 'Unix系',
	'php_version' => 'PHPバージョン',
	
	'step_2_title' => '二、データベース設定',
	'db_type' => 'データベースタイプ',
	'db_engine' => 'データベースエンジン',
	'db_host' => 'データベースサーバー',
	'db_name' => 'データベース名',
	'db_user' => 'データベースユーザー名',
	'db_pass' => 'データベースパスワード',
	'step_3_title' => '三、管理者情報',
	'admin_email' => '管理者メールアドレス',
	'admin_username' => '管理者ユーザー名',
	'admin_pw' => '管理者パスワード',
	'installing_about_moment' => 'インストール中です。約1分かかります',
	'license_title' => 'Xunio BBS 4.0 中国語版ライセンス契約',
	'license_content' => 'Xunio BBS 4.0 をお選びいただきありがとうございます。Xunio BBS 4.0 は国産の、コンパクトで安定した、大量データ下でも高い負荷能力を維持できる軽量掲示板です。テーブルは20数個のみ、ソースコードは圧縮後1M程度、実行速度は非常に速く、単一リクエストの処理は0.01秒レベル、APC・Yac・XCache環境下では0.00x秒まで高速化できます。サードパーティライブラリへの依存は極めて少なく、フロントエンドは jquery.js に依存するのみです。作者はこれを、純粋に手作りされたフェラーリのようだと考えています。パワフルで無駄がなく、デプロイとメンテナンスが容易であり、二次開発の非常に優れた基盤です。
	
Xunio BBS 4.0 は Bootstrap 4 + JQuery 3 をフロントエンドライブラリとして採用し、モバイルブラウザを全面的にサポートしています。バックエンドの XunioPHP 4.0 は NoSQL 方式で各種データベースを操作できるようになり、本バージョンは大きな飛躍です。
	
Xunio の発音は「修罗（シュラ）」、英語では Shura です。仏教では六道の一つ「修羅道」で、人道と天道の間に位置します。

Xunio BBS 4.0 は MIT ライセンスで公開されています。自由に改変・派生バージョン作成・商用利用が可能で、法律上のリスクを心配する必要はありません（改変後は元の著作権情報を保持してください）。',
	'license_date' => '公開日：2018年1月22日',
	'agree_license_to_continue' => '契約に同意してインストールを続行',
	'install_title' => 'Xunio BBS 4.0 インストールウィザード',
	'install_guide' => 'インストールウィザード',
	
	'function_check' => '関数依存チェック',
	'supported' => 'サポート',
	'not_supported' => '非サポート',
	'function_glob_not_exists' => 'バックエンドのプラグイン機能はこの関数に依存しています。php.ini を設定し、disabled_functions = ; に設定してこの関数の制限を解除してください',
	'function_gzcompress_not_exists' => 'バックエンドのプラグイン機能はこの関数に依存しています。Linuxホストではコンパイルパラメータ --with-zlib を追加し、Windowsホストでは php.ini の extension=php_zlib.dll のコメントを解除してください',
	'function_mb_substr_not_exists' => 'システムはこの関数に依存しています。Linuxホストではコンパイルパラメータ --with-mbstring を追加し、Windowsホストでは php.ini の extension=php_mbstring.dll のコメントを解除してください',
	'function_password_hash_not_exists' => 'パスワード保存にはこの2つの関数が必要です（PHP 5.5+ に標準搭載）。現在の環境にはありません。PHP をアップグレードするか、php.ini の disabled_functions を確認してください',
	'argon2_not_exists' => 'この PHP では Argon2 が有効になっていません。パスワードは bcrypt で保存されます。動作に支障はありませんが、強度はやや劣ります',
	'argon2_fallback' => 'bcrypt を使用',
	
	// hook lang_ja_jp_bbs_install.php

);

?>
