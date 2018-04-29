<?php
//导入自动加载
require '../vendor/autoload.php';

//导入配置文件
$setting = require '../config/config.php';

//创建实例,传入配置项
$app = new \Slim\App($setting);

//用户操作 增删改查
$app->post('/user/register', '\Controller\admin\UserController:register');
$app->get('/user/inquire[/{id}]', '\Controller\admin\UserController:inquire');
$app->post('/user/modify', '\Controller\admin\UserController:modify');
$app->delete('/user/delete/{id}', '\Controller\admin\UserController:delete');
//新闻操作
$app->post('/news/create', '\Controller\admin\NewsController:create');
$app->get('/news/inquire[/{id}]', '\Controller\admin\NewsController:inquire');
$app->post('/news/modify', '\Controller\admin\NewsController:modify');
$app->delete('/news/delete/{id}', '\Controller\admin\NewsController:delete');
//规则管理
$app->post('/access/register', '\Controller\admin\AccessController:register');
$app->get('/access/inquire', '\Controller\admin\AccessController:inquire');
//角色管理	
$app->post('/role/register', '\Controller\admin\RoleController:register');
$app->post('/role/modify', '\Controller\admin\RoleController:modify');
$app->get('/role/inquire[/{id}]', '\Controller\admin\RoleController:inquire');

$app->post('/login', '\Controller\admin\LoginController:login');

$app->run();
