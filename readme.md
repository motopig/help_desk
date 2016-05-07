##帮助文档

先执行 `composer install` 安装

###更改数据库信息

***app/config/database.php***
加前缀：'prefix' => 'no_',
-----

###安装命令，初始化数据库:

	php artisan migrate
	php artisan db:seed

-----

###初始化类：（git更新后请执行）

	php artisan dump-autoload

-----

###更改文件夹权限.

    chmod -R 775 app/storage

不能执行、尝试：

    chmod -R 777 app/storage

-----

配置邮件服务器
***app/config/mail.php***
'host' => 'smtp.mailgun.org', // 企业邮箱服务端口
'username' => 'postmaster@no', // 企业邮箱账户名
'password' => '**********', // 企业邮箱密码

-----

绑定cron定时脚本
工单邮件发送脚本(每分钟执行)
http://xxx.com/public/api/v1/emailrequest?byname=xxx
工单邮件接收脚本(每分钟执行)
http://xxx.com/public/api/v1/emailresponse?byname=xxx
工单邮件附件接收脚本(每分钟执行)
http://xxx.com/public/api/v1/emailattachment?byname=xxx
byname:创建公司似有域名后的随机别名，数据库查询获得

-----
