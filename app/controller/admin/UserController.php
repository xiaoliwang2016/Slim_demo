<?php
namespace Controller\admin;
use Respect\Validation\Validator as v;

class UserController extends AdminController {
	//表名
	protected $table = 'eko_member';
	//主键
	protected $pk = 'uid';

	//注册
	public function register() {
		$data = $_POST;
		$data['name'] = trim($_POST['name']);
		if (v::stringType()->length(3, 15)->validate($data['name'])) {
			return JSON(array('result' => false, 'message' => '用户名太短'));
		}
		$data['password'] = md5($data['password']);
		$data['last_login_time'] = time();
		$data['last_login_ip'] = getRealIp();
		$data['status'] = 1;
		return JSON($this->add($data));
	}

	//查询
	public function inquire($req, $res, $args) {
		if (isset($args['id'])) {
			$id = $args['id'];
			if (v::numeric()->validate($id)) {
				return JSON($this->query(array('uid' => $id, 'status' => 1)));
			}
			return JSON(array('result' => false, 'message' => '参数错误'));
		} else {
			return JSON($this->query(array('status' => 1)));
		}
	}

	//修改
	public function modify() {
		$data = $_POST;
		if (!isset($data[$this->pk])) {
			return JSON(array('result' => false, 'message' => '参数错误'));
		}
		$data['name'] = trim($_POST['name']);
		$data['password'] = md5($data['password']);
		$data['last_login_time'] = time();
		$data['last_login_ip'] = getRealIp();
		$data['status'] = 1;
		return JSON($this->add($data));
	}

	//删除
	public function delete($req, $res, $args) {
		if (isset($args['id'])) {
			$id = $args['id'];
			if (v::numeric()->validate($id)) {
				return JSON($this->soft_delete($id));
			}
			return JSON(array('result' => false, 'message' => '参数错误'));
		} else {
			return JSON(array('result' => false, 'message' => '参数错误'));
		}
	}

}
