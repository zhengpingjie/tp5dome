<?php

namespace app\service\controller;
use think\Request;
use think\Config;
use app\admin\logic\User as userlogic;

class User extends Base
{
    protected $apiuser;

    public function __construct()
    {
        $this->request = Request::instance();
        $this->apiuser = new userlogic();
    }


    public function login()
    {
        $return = array(
            "code"=>0,
            "data"=>"",
            "msg"=>""
        );
//        if($this->is_test){
//               $data=array(
//                    "open_id"=>"123456",
//                    "nick_name"=>"jiejie",
//                    "face"=>"454545.png",
//                    "sign"=>'a6fa5c3246784c4e899ed1f2abbbef59'
//              );
//            $cols = array(
//                "ts"
//            );
//        }else{
            $data = $this->request->param();
//            $data = json_decode($data,true);
//        }
        $cols = array("ts");
        $key = Config::get("key");
        $data["reg_ip"] = $this->request->ip();
        $data["nip"] = ip2long($data["reg_ip"]);
        $content = $this->createSign($data,$cols,$key);
//        dump($content);dump($data);exit();
        if($content['sign'] != $data["sign"]){
            $return['msg'] = "签名错误";
            echo json_encode($return);exit();
        }

        $appid = Config::get("appid");
        $appSecret = Config::get("appSecret");

        if($content["user_id"] > 0){
            $info = $this->apiuser->Info($content["user_id"]);
        }

        if(!$info){
            $open_id = $this->get_open_id($content["code"],$appid,$appSecret);
//            $open_id = "1234567";
            $where["open_id"] = $open_id;
            $info = $this->apiuser->infoBy($where,'id');
        }

        if(!$info){
            $content["open_id"] = $open_id;
            $info = $this->apiuser->add($content);
            if(!$info){
                $return["msg"]=$this->apiuser->getError();
                echo json_encode($return);exit();
            }
        }

        $return["code"] = 1;
        $return["data"]["user_id"] = $info["id"];;
        echo json_encode($return);
        exit();
    }


    public function get_open_id($code,$appid,$appSecret)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appSecret}&js_code={$code}&grant_type=authorization_code";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);
        $json_obj = json_decode($res,true);
        return $json_obj["openid"];

    }



}
