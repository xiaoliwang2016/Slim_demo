<?php
namespace Controller\admin;

use \interop\Container\ContainerInterface;

class AdminController extends BaseController
{

    /**
     * [初始化]
     * @param ContainerInterface $ci [description]
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);

        //获得当前请求控制器和方法  http://localhost/user/inquire/1  => /user/inquire
        $request_arr = explode('/', $_SERVER['REQUEST_URI']);
        $request_url = '/' . implode('/', array($request_arr[1], $request_arr[2]));

        //如果操作路径在免登陆选项中 则跳过登陆验证
        $visit_free = $this->app->get('settings')['visit_free'];
        if (!in_array($request_url, $visit_free)) {
            //获得当前用户ID
            $uid = $this->check_login();

            //如果操作路径在免验证选项中 则不进行身份验证
            $auth_free = $this->app->get('settings')['auth_free'];
            if (!in_array($request_url, $auth_free)) {
                if (!($this->check_authorization($uid, $request_url))) {
                    echo "您不具备权限操作";
                    die();
                }
            }
            
        }

    }

    /**
     * 检查是否登录
     * @return [type] [description]
     */
    private function check_login()
    {
        if ($uid = is_login()) {
            return $uid;
        } else {
            echo "请登录";
            die();
        }
    }

    /**
     * 根据用户ID 判断是否有权限操作当前请求
     * @param  [int] $uid [用户ID]
     * @param  [string] $url [请求路径]
     * @return [type]      [description]
     */
    private function check_authorization($uid, $url)
    {
        $request_rule_id = ($this->db->selectAll('slim_auth_rule', array('name' => $url), 'id'))[0]['id'];
        if (!$request_rule_id) {
            echo "当前路径不存在权限控制";
            die();
        }
        //获得当前用户具有角色ID（二维数组）
        $role_id_arr = $this->db->selectAll('slim_auth_role_access', array('uid' => $uid), 'group_id');
        if ($role_id_arr) {
            $role_ids = '';
            foreach ($role_id_arr as $role_id) {
                $role_ids = $role_ids . "'" . $role_id['group_id'] . "',";
            }
            //获得当前用户具有权限ID（字符串数组）
            $rules_arr = $this->db->selectAll('slim_auth_role', " id IN (" . $role_ids . "'')", 'rules');
            $rules     = array();
            foreach ($rules_arr as $rule) {
                $rules = array_unique(array_merge($rules, explode(',', $rule['rules'])));
            }
            $rules = array_filter($rules, function ($var) {
                return $var !== '';
            });
            if (in_array($request_rule_id, $rules)) {
                return true;
            }
            return false;
        } else {
            echo "当前用户未分配角色";
            die();
        }

    }
}
