<?php
namespace Controller\admin;
use Respect\Validation\Validator as v;

class NewsController extends AdminController {
	//表名
	protected $table = 'slim_news';
	//主键
	protected $pk = 'id';

	//注册
	public function create() {
		$data = $_POST;
		$data['title'] = htmlspecialchars(trim($_POST['title']));
		$data['create_time'] = time();
		$data['home'] = 1;
		$data['status'] = 1;
		return json_encode($this->add($data));
	}

	//查询
	public function inquire($req, $res, $args) {
		if (isset($args['id'])) {
			$id = $args['id'];
			if (v::numeric()->validate($id)) {
				return JSON($this->query(array('id' => $id, 'status' => 1)));
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
		$data['title'] = htmlspecialchars(trim($_POST['title']));
		$data['create_time'] = time();
		$data['home'] = 1;
		$data['status'] = 1;
		return json_encode($this->add($data));
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
