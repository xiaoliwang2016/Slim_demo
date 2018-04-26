<?php
namespace Controller\admin;

use \interop\Container\ContainerInterface;

class UserController extends BaseController {
	//表名
	protected $table = 'eko_member';
	//主键
	protected $pk = 'uid';

	/**
	 * [初始化]
	 * @param ContainerInterface $ci [description]
	 */
	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
	}

	//注册
	public function register() {
		var_dump($this->check_null($_POST));
	}
}
