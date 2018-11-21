<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class AppDetail extends BaseModel
{
    //新增和更新自动完成
    protected $auto = [];
    protected $insert = [];
    protected $update = [];

    protected function initialize($model='',$class='')
    {
        $arr = explode("\\", __CLASS__);
        //$model = "admin/".$arr[count($arr)-1];//获取当前模型
        $model = $arr[count($arr)-1];//获取当前模型
        parent::initialize($model,__CLASS__);
    }

    public function updd($data,$where){
        foreach ($data as $key=>$val){
            if(empty($val)){
                unset($data[$key]);
            }
        }
        $info = parent::infoBy($where);
        if(empty($info)){
            $data['app_id'] = $where['app_id'];
            $res=parent::add($data);
        }else{
            $res= parent::updBy($data,$where);
        }

        return $res;
    }

    public  function addd($data){
        foreach ($data as $key=>$val){
            if(empty($val)){
                unset($data[$key]);
            }
        }
        $res= parent::add($data);
        return $res;
    }


}