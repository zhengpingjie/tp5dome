<?php

namespace app\service\controller;
use Think\Exception;
use think\Request;
use think\Config;
use app\admin\logic\App as AppLogic;
use app\admin\logic\AppType;
use app\admin\model\AppDetail;
use app\admin\model\UserApp;

class App extends Base
{
    protected $apiapp;

    public function __construct()
    {
        $this->request = Request::instance();
        $this->apiapp = new AppLogic();
        $this->apiAppType = new AppType();
//        $this->apiAppDetail = new AppDetail();
//        $this->userApp = new UserApp();
    }

    //获取分类列表
    public function getAppType()
    {
        $return = array(
            'code'=>0,
            'msg'=>"",
            'data'=>""
        );
        $typeList =  $this->apiAppType ->listBy(null,'*',null);
        $arr=[];
        foreach ($typeList as $key=> $tl){
            $where['type_id'] = $tl['id'];
            $cnt=$this->apiapp->countBy($where,'type_id');
            $arr[]=$cnt;

            if($cnt<=0){
                array_splice($typeList,$key,1);
            }
        }

        if(!empty($typeList)){
            $return["code"] = 1;
            $return["data"]["info"] =  $typeList;
        }
        echo json_encode($return);exit();
    }

  //    获取分类列表
    public function getAppTypeList(){
        $return = array(
            'code'=>0,
            'msg'=>"",
            'data'=>""
        );


        $data = $this->request->param();

        $cols = ["ts"];
        $type_id = $data["type_id"] ?  $data["type_id"] :'1';
        $page = $data["page"] ? $data["page"] : "1";
        $key = Config::get("key");
        $content = $this->createSign($data,$cols,$key);
        if($content["sign"] != $data["sign"]){
            $return['msg'] = "签名错误";
            echo json_encode($return);exit();
        }
        $pagesize = 100;
//        $where['type_id'] = $type_id;
        $list=$this->apiapp->search(null,"star desc,view_num desc,id desc",$page,$pagesize,$count);
        $typeList = $this->apiAppType ->listBy(null,'id,name',null);
        $arr=[];
        $applist = [];
        if(!empty($list)){
            foreach ($typeList as $tl){
                    foreach ($list as &$li){
                        if($tl["id"] == $li["type_id"] ){
                            $li["type_name"] =  $tl["name"];
                            if(!empty($li['tags'])){
                                $li['tags'] = explode(',',$li['tags']);
                            }
                            $applist['type'.$tl["id"]][]=$li;

                        }

                    }

            }
            $return["code"] = 1;
            $return["data"]= $applist;
        }
        echo json_encode($return);exit();

    }

    //获取列表详情
    public function getAppDetail(){
        $return = array(
            'code'=>0,
            'msg'=>"",
            'data'=>""
        );
        $data = $this->request->param();
        $cols = ["ts"];

        $app_id = $data["app_id"];
        $user_id = $data["user_id"];

        if(empty($app_id)){
            $return['msg'] = "请求失败";
            echo json_encode($return);exit();
        }
        $key = Config::get("key");
        $content = $this->createSign($data,$cols,$key);
        if($content["sign"] != $data["sign"]){
            $return['msg'] = "签名错误";
            echo json_encode($return);exit();
        }

        $where["app_id"] = $app_id;
        $appDetail = new AppDetail();
        $app_detail =  $appDetail->infoBy($where);
        if(!empty($app_detail)){
            $wheres["id"] = $app_id;
            $info = $this->apiapp->setInc($wheres,'view_num');
            if(!$info){
                $return['msg'] = $this->apiapp->getError();
            }
        }

        if(!empty($info)){
            $this->userApp=new UserApp();
            $where['user_id'] = $user_id;
            $info=$this->userApp->infoBy($where,'id');
            if(!empty($info)){
                $info=$this->userApp->setInc($where,'view_num');
                if(!$info){
                    $return['msg'] = $this->apiapp->getError();
                }
            }else{
                $data = [];
                $data = $where;
                $data['view_num'] = 1;
                $info = $this->userApp->add($data);
               if(!$info){
                   $return['msg'] =  $this->userApp->getError();
               }

            }
        }

        if(!empty($info)){
            $return["code"] = 1;
            $return["data"]["info"] = $app_detail;
        }

        echo json_encode($return);exit();

    }



}
