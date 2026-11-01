<?php

/*

	Xunio BBS 4.0 配置文件
	支持多台 DB，主从配置好以后，xo 会自动根据 SQL 读写分离。
	支持各种 cache，本机 apc/xcache, 网络: redis/memcached/mysql
	支持 CDN，如果前端开启了 CDN 请设置 cdn_on=>1, 否则获取 IP 会不准确 
	支持临时目录设置，独立 Linux 主机，可以设置为 /dev/shm 通过内存加速
*/
return array (
	'db' => array (
		'type' => 'mysql',	
		'mysql' => array (
			'master' => array (
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'root',
				'name' => 'test',
				'tablepre' => 'bbs_',
				'charset' => 'utf8mb4',
				'engine' => 'innodb',
			),
			'slaves' => array (),
		),
		'pdo_mysql' => array (
			'master' => array (
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'root',
				'name' => 'test',
				'tablepre' => 'bbs_',
				'charset' => 'utf8mb4',
				'engine' => 'innodb',
			),
			'slaves' => array (),
		),
	),
	// 密码摘要参数。算法优先 Argon2id（环境不支持时回落 bcrypt），此处只调开销：
	// memory_cost 单位为 KiB，65536 即 64 MiB；time_cost 为迭代轮数；threads 为并行度。
	// 密码哈希参数。留空即按当前环境自动选档：
	// Argon2id 默认 46 MiB / 1 轮（约 70ms），memory_limit 低于 96M 时自动降到 19 MiB / 2 轮。
	// 需要固定参数时填 memory_cost / time_cost / threads，memory_cost 单位为 KiB。
	// 并发登录较多或内存吃紧的机器可下调 memory_cost（不低于 8192），安全性随之下降。
	'password' => array (
		'cost' => 10,		// 回落 bcrypt 时的 cost
	),
	
	'cache' => array (
		'enable' => true,
		'type' => 'mysql',
		'memcached' => array (
			'host' => 'localhost',
			'port' => '11211',
			'cachepre' => 'bbs_',
		),
		'redis' => array (
			'host' => 'localhost',
			'port' => '6379',
			'cachepre' => 'bbs_',
		),
		'xcache' => array (
			'cachepre' => 'bbs_',
		),
		'yac' => array (
			'cachepre' => 'bbs_',
		),
		'apc' => array (
			'cachepre' => 'bbs_',
		),
		'mysql' => array (
			'cachepre' => 'bbs_',
		),
	),
	'tmp_path' => './tmp/',		// 可以配置为 linux 下的 /dev/shm ，通过内存缓存临时文件
	'log_path' => './log/',		// 日志目录
	
	// -------------------> xunio bbs 4.0 配置

	'view_url' => 'view/',		// 可以配置单独的 CDN 域名：比如：http://static.domain.com/view/
	'upload_url' => 'upload/',	// 可以配置单独的 CDN 域名：比如：http://upload.domain.com/upload/
	'upload_path' => './upload/',	// 物理路径，可以用 NFS 存入到单独的文件服务器
	
	'logo_mobile_url' => 'view/img/logo.png',		// 手机的 LOGO URL
	'logo_pc_url' => 'view/img/logo.png',			// PC 的 LOGO URL
	'logo_water_url' => 'view/img/water-small.png',		// 水印的 LOGO URL
	
	'sitename' => 'Xunio BBS',
	'sitebrief' => 'Site Brief',
	'timezone' => 'Asia/Shanghai',	// 时区，默认中国
	'lang' => 'zh-cn',
	'runlevel' => 5,		// 0: 站点关闭; 1: 管理员可读写; 2: 会员可读;  3: 会员可读写; 4：所有人只读; 5: 所有人可读写
	'runlevel_reason' => 'The site is under maintenance, please visit later.',
	
	'cookie_domain' => '',
	'cookie_path' => '',
	'auth_key' => '',		// 加密密钥，安装时自动生成，禁止写死
	'site_id' => '',		// 站点指纹，向官方插件服务标识本站，生成一次后固定
	
	'pagesize' => 20,
	'postlist_pagesize' => 100,
	'cache_thread_list_pages' => 10,
	'online_update_span' => 60,	// 在线数字的刷新间隔（秒），大站设置的长一些
	'online_hold_time' => 900,	// 会员多少秒内没有动作即视为离线，只影响在线数字
	'session_gc_time' => 86400,	// 会话数据保留时间，超过才清理；须大于 online_hold_time
	'session_delay_update' => 0,
	'upload_image_width' => 927,	// 上传图片自动缩略的最大宽度
	'order_default' => 'lastpid',
	'attach_dir_save_rule' => 'Ym',	// 附件存放规则，附件多用：Ymd，附件少：Ym
	
	'update_views_on' => 1,
	'user_create_email_on' => 0,
	'user_create_on' => 1,
	'user_resetpw_on' => 0,

	 'nav_2_on' => 1,				// 是否开启二级导航
	 'nav_2_forum_list_pc_on' => 0,			// 是否开启 PC 二级导航版块列表
	 'nav_2_forum_list_mobile_on' => 0,		// 是否开启 Mobile 二级导航版块列表
	 

	
	'admin_bind_ip' => 0,		// 后台是否绑定 IP
	
	'cdn_on' => 0,
	
	/* 支持多种 URL 格式：
		0: ?thread-create-1.htm
		1: thread-create-1.htm
		2: ?/thread/create/1  不支持
		3: /thread/create/1   不支持
	*/
	'url_rewrite_on' => 0,
	
	// 禁止插件
	'disabled_plugin' => 0, 
	// 插件保护：插件触发致命错误（白屏）时自动禁用该插件并恢复站点。1 开启，0 关闭。
	'plugin_protect_on' => 1, 
	  
	'version' => '4.0.4',
	'static_version' => '?1.0',
	'installed' => 0,
);
?>