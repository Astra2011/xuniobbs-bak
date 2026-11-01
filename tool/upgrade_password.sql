# 密码摘要由 md5 改为 Argon2id
# Argon2id 摘要约 97 字符（bcrypt 60 字符），char(32) 会把新摘要截断
# 必须先执行本脚本再切换代码：否则老用户登录时自动升级写回会被截断，新密码直接失效
# 执行后用户下次登录成功即自动换成 Argon2id，不需要逐个重置
ALTER TABLE `bbs_user` MODIFY COLUMN `password` varchar(255) NOT NULL DEFAULT '';
