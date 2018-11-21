<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class App extends BaseModel
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

    public function updd($ico,$qrcode){
        $data = $_POST;
        if(!empty($ico)){
            $data['ico'] = $ico;
        }

        if(!empty($qrcode)){
            $data['qrcode'] = $qrcode;
        }
        $res= parent::upd($data);
        return $res;
    }

    public  function addd($ico,$qrcode){
        $data = $_POST;
        if(!empty($ico)){
            $data['ico'] = $ico;
        }

        if(!empty($qrcode)){
            $data['qrcode'] = $qrcode;
        }
        $res= parent::add($data);
        return $res;
    }

}