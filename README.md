# Xunio BBS

轻论坛程序，PHP + MySQL 后端，Bootstrap 4 + jQuery 前端。插件和主题用 hook / overwrite 扩展，不动核心代码也能改功能。

本仓库基于 **Xiuno BBS 4.0.4** 二次开发。原项目 MIT 协议，作者 axiuno@gmail.com。

## 与原版的区别

- **PHP 8 兼容**：原版在 PHP 7.4 / 8.x 下有语法和运行时错误，已逐处修正，PHP 8.2 下实测可跑。
- **全项目改名**：`xiuno` → `xunio`，函数前缀 `xn_` → `xo_`，常量与 JS 命名空间同步；数据库表前缀 `bbs_` 保持不动。
- **图标库替换**：FontAwesome 4.6.3 → Tabler Icons 3.50.0，158 个字体文件全部本地化，不走 CDN。
- **8 种语言**：简体中文、繁體中文、English、日本語、한국어、Deutsch、Français、Español。后台设置页和安装向导都能切换，语言名用各自的写法显示。
- **白色主题**：前台、后台、安装页的导航栏和页脚改为浅色。
- **滚动条处理**：视口常驻一条透明滚动条槽，页面长短不同不再左右抖动，弹窗打开时也不会多出一条。

依赖版本没动，仍是 Bootstrap 4.0.0、jQuery 3.1.0、Popper。

## 环境要求

- PHP 7.0 以上，推荐 7.4 / 8.x。需要 `pdo_mysql`、`mbstring`，上传头像和图片水印需要 `gd`。
- MySQL 5.6+ / MariaDB 10.2+。
- 缓存可选：MySQL（默认）、Redis、Memcached、Yac、XCache、APCu，在 `conf/conf.php` 的 `cache` 段切换。
- Nginx 或 Apache 均可。

## 安装

1. 上传到网站目录，确认 `conf/`、`tmp/`、`log/`、`upload/` 可写。
2. 浏览器访问 `install/`，按向导填数据库信息和管理员账号。
3. 装完会写入 `install/install.lock`，此后安装脚本一律返回 403，不会再次执行。仍建议整个删掉 `install/` 目录，里面的 `.sql` 能被直接下载。
4. 前台是站点根目录，后台入口是 `admin/`。

不配伪静态也能用。URL 形式由 `conf/conf.php` 的 `url_rewrite_on` 决定：

| 值 | 形式 | 说明 |
| --- | --- | --- |
| 0 | `?thread-1.htm` | 默认，不需要伪静态 |
| 1 | `thread-1.htm` | 需要伪静态 |
| 2 | `?thread/1` | 路径式 |
| 3 | `thread/1` | 路径式 + 伪静态 |

## 目录结构

| 路径 | 说明 |
| --- | --- |
| `index.php` | 前台入口 |
| `route/` | 前台路由，按页面拆文件 |
| `model/` | 业务函数库：用户、版块、帖子、附件、KV 缓存等 |
| `xuniophp/` | 框架本体，合并版为 `xuniophp.min.php` |
| `view/htm/` | 前台模板 |
| `view/css/` `view/js/` | 前台样式与脚本 |
| `view/font/tabler/` | Tabler 图标字体，按 unicode-range 分块 |
| `admin/` | 后台，`route/` 放路由，`view/htm/` 放模板 |
| `install/` | 安装向导，装完应删除；装过之后由 `install.lock` 锁住 |
| `lang/` | 语言包，一个目录一种语言，含 `bbs.php`、`bbs_admin.php`、`bbs_install.php`、`bbs.js` |
| `plugin/` | 插件目录，装插件时生成 |
| `conf/` | 配置，`conf.php` 由安装程序生成 |
| `tmp/` | 编译缓存，可随时清空 |
| `upload/` | 上传的附件与图片 |
| `log/` | 运行日志 |
| `tool/` | 辅助脚本：数据库迁移、重置密码、合并等 |

## 开发注意

**改完代码要清缓存。** 线上模式（`DEBUG = 0`）下，`model/*.php`、`route/*.php`、`view/htm/*.htm` 会被编译合并写入 `tmp/`，命中缓存就不再读源文件。改了源文件却没清 `tmp/`，会出现"代码改了不生效"，甚至直接 500——比如新增了函数而缓存里还没有，就会调用未定义函数。

调试时把 `index.php` 里的 `define('DEBUG', 0)` 改成 `1`，改为逐文件加载，改完立刻生效。开发完记得改回去。

**图标**用 Tabler Icons，两种写法都有效：

```html
<i class="ti ti-user"></i>
<i class="ti-user"></i>
```

第二种靠 `tabler-icons.css` 里的 `[class^="ti-"]` 兼容规则，等价于原 FontAwesome 的 `icon-xxx` 用法。

**语言**：模板里用 `lang('键名')` 取值，键定义在 `lang/zh-cn/bbs.php`。新增语言只需复制一个语言目录并翻译，后台下拉框会自动出现。语言清单由 `model/misc.func.php` 的 `lang_list()` 提供，固定顺序，缺 `bbs.php` 的目录不显示。

**插件**：放到 `plugin/` 下，通过 hook 注入、通过 overwrite 覆盖，不修改核心文件。主题改 `view/htm/` 和 `view/css/` 即可。

## 授权

MIT。可以自由修改、派生、商用，保留原作者版权信息即可。

原项目：Xiuno BBS 4.0 · axiuno@gmail.com
