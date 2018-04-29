<?php
namespace Controller\admin;

use Respect\Validation\Validator as v;

class AccessController extends AdminController
{
    //表名
    protected $table = 'slim_auth_rule';
    //主键
    protected $pk = 'id';

    //添加规则
    public function register()
    {

        if (count(explode('/', $_POST['name'])) == 3) {
            $data['name'] = trim($_POST['name']);
        } else {
            return JSON(array('result' => false, 'message' => "请输入正确的访问路径"));
        }

        $data['title'] = trim($_POST['title']);

        if (v::intVal()->between(0, 1)->validate($_POST['type'])) {
            $data['type'] = trim($_POST['type']);
        } else {
            $data['type'] = 1;
        }

        $_POST['pid'] = isset($_POST['pid']) ? $_POST['pid'] : 0;
        if (v::intVal()->validate($_POST['pid'])) {
            $data['pid'] = $_POST['pid'];
        } else {
            $data['pid'] = 0;
        }

        $data['status'] = 1;

        return JSON($this->add($data));
    }

    //获得规则
    public function inquire()
    {
        $list = $this->query(array('status' => 1),'id,title');
        return JSON($list);
    }


}
