<?php

namespace app\service\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
    public $is_test = false;

    public function createSign($data,$cols,$key){
//        $data=array('aa'=>"111","bb"=>'222','cc'=>"333");
//        $cols= ['aa','bb'];
//        过滤数据
        $cols_arr=$this->filter_arr($data,$cols);

        $sign = "";
        if(!empty($cols_arr)){
            ksort($cols_arr);
            foreach ($cols_arr as $k => $val){
                $sign .= $k.'='.$val."&";
            }
        }
        $sign = $sign.'key='.$key;
        $sign = md5(strtolower(trim($sign)));
        $data['sign'] = $sign;
        return $data;
    }


    public function filter_arr($data,$cols){
        $cols_arr = [];
        foreach ($cols as $val){
            $cols_arr[$val] = $data[$val];
        }

        return $cols_arr;
    }




}
