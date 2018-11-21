<?php
namespace app\admin\logic;
use Think\Request;
use think\Db;
use think\Model;

class User extends BaseLogic
{
    /*******************************基础配置开始*******************************/
    
    public $flag_opt = array();
    public $id_alias = "user_id";//主表与其他表关联的字段
    private $flag_model = "";//标记模型
    private $slave_model = "";//从表模型
    private $slave_relation_field = "";//从表和主表关联字段

    /*******************************基础配置结束*******************************/

    function __construct()
    {
        $arr = explode("\\", __CLASS__);
        $model = $arr[count($arr)-1];//获取当前模型
        $mp["id_alias"] = $this->id_alias;
        $mp["slave_model"] = $this->slave_model;
        $mp["slave_relation_field"] = $this->slave_relation_field;
        $mp["flag_model"] = $this->flag_model;
        $mp["flag_opt"] = $this->flag_opt;
        parent::__construct($model,$mp);
    }

    public function infoByOpenid($open_id)
    {
        $where["open_id"] = $open_id;
        return parent::infoBy($where);
    }
    public function infoByName($name)
    {
        $where["name"] = $name;
        return parent::infoBy($where);
    }

    public function updMoney($id,$old_money,$now_money)
    {
        $data["money"] = $now_money;
        $where["id"] = $id;
        $where["money"] = $old_money;
        return parent::updBy($data,$where);
    }

    public function reg($data)
    {
        $res = parent::add($data);
        return $res;
    }

    public function login($data)
    {
        $info = $this->infoByName($data["name"]);
        if(!$info)
        {
            $this->error = "用户名不存在";
            return false;
        }
        if($info["status"] != 1)
        {
            $this->error = "您的账号被封，请联系客服！" ;
            return false;
        }
        $curr_pwd = strtolower(auth_code($pwd,"ENCODE",C('USER_AUTH_KEY')));
        if(strtolower($info["pwd"]) == $curr_pwd)
        {
            $data["user_id"] = $info["id"];
            $data["agent_id"] = $info["agent_id"];
            $data["agent_pid"] = $info["agent_pid"];
            $data["ip"] = get_client_ip();
            $data["nip"] = get_client_ip();
            $data["add_time"] = time();
            $data["add_date"] = date("Ymd",time()) ;
            $res = logic("user_login_log")->add($data);
            return $res ;
        }
        else
        {
            $this->error = "密码不正确！" ;
            return false;
        }
        return false;
    }

    /**   
    * 初始化用户（如果用户存在，返回用户信息，如果用户不存在，则注册并返回信息） 
    * 
    * @access public 
    * @param $data array 用户数据
    * @return array 用户信息
    */
    public function initUser($data)
    {
        if(!$data["name"])
        {
            $this->error = "用户名不能为空";
            return false;
        }
        $info = $this->infoByName($data["name"]);
        if(!$info)
        {
            $info = $this->reg($data);
            if(!$info)
            {
                $this->error = "注册失败";
                return false;
            }
        }
        else
        {
            $this->login($data);
        }
        return $info;
    }

    /**
     * view_num（统计点击数量）
     *
     * @access public
     * @param $data array 用户数据
     * @return 修改id
     */

    public function viewNum($data){
        if(!$data['app_id']){
            $this->error = "id不能为空";
        }
        model();
    }


}