<?php
namespace Controller\admin;

use \interop\Container\ContainerInterface;

class AdminController extends BaseController {

	/**
	 * [初始化]
	 * @param ContainerInterface $ci [description]
	 */
	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);

		//获得当前用户ID
		$uid = $this->check_login();

		//获得当前请求控制器和方法  http://localhost/user/inquire/1  => /user/inquire
		$request_arr = explode('/', $_SERVER['REQUEST_URI']);
		$request_url = '/' . implode('/', array($request_arr[1], $request_arr[2]));
	}

	/**
	 * 检查是否登录
	 * @return [type] [description]
	 */
	private function check_login() {
		if ($uid = is_login()) {
			return $uid;
		} else {
			echo "请登录";
			die();
		}
	}
}
