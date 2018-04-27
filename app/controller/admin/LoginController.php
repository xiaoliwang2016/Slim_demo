<?php
namespace Controller\admin;

class LoginController extends BaseController {
	//表名
	protected $table = 'eko_member';
	//主键
	protected $pk = 'uid';
	/**
	 *  用户登录
	 * @return [type] [description]
	 */
	public function login() {
		if (isset($_POST['name'])) {
			$user = $this->query(array('name' => $_POST["name"]));
			if ($user = $user[0]) {
				if ($user['password'] == md5($_POST['password'])) {
					\Lib\Session::set('userinfo', $user);
					return JSON(array("result" => true, "message" => "登录成功"));
				}
				return JSON(array("result" => false, "message" => "密码不正确"));
			}
			return JSON(array("result" => false, "message" => "用户名不存在"));
		}
	}

}
