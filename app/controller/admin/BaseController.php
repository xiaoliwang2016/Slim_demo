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
		\Lib\Session::start();
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
	/**
	 * 增加/修改 一条数据
	 * @param  [array] $data [需要插入数据]
	 * @return [array] $data [返回TRUE或者FALSE  并返回插入ID 或者报错信息]
	 */
	public function add($data) {
		//检查非空
		if ($res = $this->check_null($data)) {
			return $res;
		}
		//过滤字段
		$data = $this->filter_field($data);

		//如果有主键则为修改
		if (isset($data[$this->pk])) {
			$id = $data[$this->pk];
			return $this->db->update($this->table, $data, array("$this->pk" => $id));
		}
		//没有则为新增
		if ($id = $this->db->insert($this->table, $data)) {
			return array('result' => true, 'id' => $id);
		}

		return array('result' => false, 'message' => '插入失败');
	}
	/**
	 * 查询
	 * @param  mix $where  [查询条件]
	 * @param  string  $fields
	 * @param  string  $order
	 * @param  integer $skip
	 * @param  integer $limit
	 * @return [array]          [二维数组]
	 */
	public function query($where = 1, $fields = '*', $order = '', $skip = 0, $limit = 100) {
		return $this->db->selectAll($this->table, $where, $fields, $order, $skip, $limit);
	}
	/**
	 * 软删除
	 * @param  [type] $id
	 * @param  string $identifier
	 * @return [type]
	 */
	public function soft_delete($id, $identifier = 'status') {
		if (is_numeric($id)) {
			if ($this->db->update($this->table, array("$identifier" => 0), array("$this->pk" => $id))) {
				return array('result' => true, 'message' => '删除成功');
			}
		}
		return array('result' => false, 'message' => '删除失败，参数错误');
	}

}
