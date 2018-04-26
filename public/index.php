<?php
//导入自动加载
require '../vendor/autoload.php';

//导入配置文件
$setting = require '../config/config.php';

//创建实例,传入配置项
$app = new \Slim\App($setting);

$app->post('/user/register', '\Controller\admin\UserController:register');

$app->run();
