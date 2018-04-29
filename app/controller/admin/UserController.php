<?php
namespace Controller\admin;

use Respect\Validation\Validator as v;

class UserController extends AdminController
{
    //表名
    protected $table = 'slim_member';
    //主键
    protected $pk = 'uid';

    /**
     * 注册用户
     * @return [array] array('result' => true, 'message' => '添加成功')
     */
    public function register()
    {
        $result = $this->validate_data($_POST);
        if ($result['result']) {
            $id = ($this->add($result['data']))['id'];
            if ($id) {
                foreach ($result['data']['roles'] as $role_id) {
                    $this->db->insert('slim_auth_role_access', array('uid' => $id, 'group_id' => $role_id));
                }
                return JSON(array('result' => true, 'message' => '添加成功'));
            }
            return JSON(array('result' => true, 'message' => '添加失败'));
        }
        return JSON($result);
    }

    /**
     * 查询用户
     * @param  [type] $req
     * @param  [type] $res
     * @param  [type] $args
     * @return [array] 格式：{"uid":"5","name":"marry","last_login_time":"1525016728","roles":[{"id":"1","title":"超级管理员"},{"id":"2","title":"普通管理员"}]}
     */
    public function inquire($req, $res, $args)
    {
        if (isset($args['id'])) {
            $uid = $args['id'];
            if (v::numeric()->validate($uid)) {
                $roles            = $this->search_roles_by_uid($uid);
                $user             = $this->query(array('uid' => $uid, 'status' => 1), 'uid,name,last_login_time');
                $user[0]['roles'] = $roles;
                return JSON($user[0]);
            }
            return JSON(array('result' => false, 'message' => '参数错误'));
        }
        $users     = $this->query(array('status' => 1), 'uid,name,last_login_time');
        $users_new = array();
        foreach ($users as $user) {
            $user['roles'] = $this->search_roles_by_uid($user['uid']);
            $users_new[]   = $user;
        }
        return JSON($users_new);
    }

    //修改
    public function modify()
    {
        if (!isset($_POST[$this->pk])) {
            return JSON(array('result' => false, 'message' => '参数错误'));
        }
        $result = $this->validate_data($_POST);
        if ($result['result']) {
            $data            = $result['data'];
            $data[$this->pk] = $_POST[$this->pk];
            $res             = $this->add($data);
            //修改用户成功后建立角色关系
            if ($res['result']) {
                //添加角色-用户关系前先删除旧的
                $this->db->deleteAll('slim_auth_role_access', array('uid' => $data[$this->pk]));

                foreach ($data['roles'] as $role_id) {
                    $this->db->insert('slim_auth_role_access', array('uid' => $data[$this->pk], 'group_id' => $role_id));
                }
                return JSON(array('result' => false, 'message' => '修改成功'));
            } else {
                return JSON($res);
            }
        }
        return JSON($result);

    }

    //删除
    public function delete($req, $res, $args)
    {
        if (isset($args['id'])) {
            $uid = $args['id'];
            if (v::numeric()->validate($uid)) {
            	$this->db->deleteAll('slim_auth_role_access', array('uid' => $uid));
                return JSON($this->soft_delete($uid));
            }
            return JSON(array('result' => false, 'message' => '参数错误'));
        } else {
            return JSON(array('result' => false, 'message' => '参数错误'));
        }
    }

    /**
     * 自动验证 完成数据
     * @param  [array] $resouce [description]
     * @return [array]          [description]
     */
    private function validate_data($resouce)
    {
        if (!v::stringType()->length(3, 15)->validate($resouce['name'])) {
            return array('result' => false, 'message' => '用户名太短');
        }
        $data['name'] = trim($resouce['name']);
        if (!v::alnum()->validate($resouce['password'])) {
            return array('result' => false, 'message' => '密码只能包含数字和字母');
        }
        $data['password'] = md5($resouce['password']);
        if (!$resouce['roles']) {
            return array('result' => false, 'message' => '请分配角色');
        }
        if ($resouce['email'] == '') {
            $data['email'] = '未知邮箱';
        } else {
            $data['email'] = $resouce['email'];
        }
        $data['roles']           = explode(',', $resouce['roles']);
        $data['last_login_time'] = time();
        $data['last_login_ip']   = getRealIp();
        $data['status']          = 1;
        return array('result' => true, 'data' => $data);
    }

    /**
     * 根据用户ID查找对应的角色ID
     * @param  [int] $uid [用户ID]
     * @return [array]      [角色ID数组]
     */
    private function search_roles_by_uid($uid)
    {
        //当前管理员对应的角色ID （数组）
        $role_id_arr = $this->db->selectAll('slim_auth_role_access', array('uid' => $uid), 'group_id');
        $role_ids    = '';
        foreach ($role_id_arr as $role_id) {
            $role_ids = $role_ids . "'" . $role_id['group_id'] . "',";
        }
        //当前管理员对应角色（对象数组）
        $roles = $this->db->selectAll('slim_auth_role', " id IN (" . $role_ids . "'')", 'id,title');
        return $roles;
    }

}
