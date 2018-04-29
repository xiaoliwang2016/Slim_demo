<?php
//配置文件
return array(
	'settings' => [
		'displayErrorDetails' => true,

		//开启debug模式
		'debug' => true,

		//定义日志文件
		'logger' => [
			'name' => 'slim-app',
			'level' => Monolog\Logger::DEBUG,
			'path' => __DIR__ . '/../logs/app.log',
		],

		//数据库配置文件
		'db' => [
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'pass' => '123',
			'db' => 'test',
		],
		//免权限验证项
		'auth_free' => [
			'/user/register',
			'/user/inquire',
		],
		//免登陆操作项
		'visit_free' => [
			'/user/register',
		]

	],
);
