<?php
namespace Controller\admin;

use \interop\Container\ContainerInterface;

class BaseController {
	//Slim对象
	protected $app;

	//数据库对象
	protected $db;

	//表名
	protected $table;

	//主键
	protected $pk;

	/**
	 * [初始化]
	 * @param ContainerInterface $ci [注入容器对象]
	 */
	public function __construct(ContainerInterface $ci) {
		$this->app = $ci;
		$this->db = \Lib\DB::getIntance($ci->get('settings')['db']);
	}

	/**
	 * 检查非空字段是否有值
	 * @param  [array] $data [需要检查的字段]
	 * @return [array]  array  [数组]
	 */
	public function check_null($data) {
		$fields = $this->db->getField_notNull($this->table);
		if (is_array($data)) {
			foreach ($fields as $field) {
				//如果是主键，则跳过判断
				if ($field == $this->pk) {
					continue;
				}
				if (!array_key_exists($field, $data)) {
					return array('result' => false, 'message' => $field . "字段不能为空");
				}
			}
		}
	}
	/**
	 * 过滤数据表中不存在的字段
	 * @param  [array] $data [过滤前数据]
	 * @return [array]       [过滤后的数据]
	 */
	public function filter_field($data) {
		$columns = $this->db->getFields($this->table);
		$fields = array();
		foreach ($columns as $value) {
			$fields[] = $value['COLUMN_NAME'];
		}
		$data_after = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $fields)) {
				$data_after[$key] = $value;
			}
		}
		return $data_after;
	}

	public function add($data) {

	}

}
