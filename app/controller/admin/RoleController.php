<?php
namespace Controller\admin;

use Respect\Validation\Validator as v;

class RoleController extends AdminController
{
    //表名
    protected $table = 'slim_auth_role';
    //主键
    protected $pk = 'id';

    //添加角色
    public function register()
    {
        $data = $this->validate_data($_POST);
        return JSON($this->add($data));
    }

    //获得角色列表 / 单个角色（权限）
    public function inquire($req, $res, $args)
    {
        if (isset($args['id'])) {
            $list  = $this->query(array('status' => 1, 'id' => $args['id']), '*', '', 0, 1);
            $rules = '';
            foreach (explode(',', $list[0]['rules']) as $value) {
                $rules = $rules . "'" . $value . "',";
            }
            $list[0]['rules'] = $this->db->selectAll('slim_auth_rule', " id IN (" . $rules . "'')", 'id,title');
        } else {
            $list = $this->query(array('status' => 1), 'id,title');
        }
        return JSON($list);
    }

    //修改角色
    public function modify()
    {
        if (isset($_POST['id'])) {
            $data = $this->validate_data($_POST);
            $data['id'] = $_POST['id'];
            return JSON($this->add($data));
        }else{
            return JSON(array('result' => false, 'message' => "参数错误"));
        }
    }

    //过滤数据
    private function validate_data($resouce)
    {
        $data['title']  = htmlspecialchars($resouce['title']);
        $data['status'] = 1;
        $rules          = '';
        //权限列只允许存在数字 例如 1,2,3,4,5  ID代表auth_rule表 id字段
        foreach (explode(',', $resouce['rules']) as $item) {
            if (v::intVal()->validate($item)) {
                $rules .= $item . ',';
            }
        }
        $data['rules'] = $rules;
        return $data;
    }

}
