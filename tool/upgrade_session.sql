# 在线统计与会话表调整
# 老站的 session.url 是 char(32)，带查询串的地址会被截断，严格模式下还可能写入失败；
# 会员在线数按 DISTINCT uid 统计，靠 (uid,last_date) 索引，不需要额外加索引。
ALTER TABLE `bbs_session` MODIFY COLUMN `url` varchar(255) NOT NULL DEFAULT '';
