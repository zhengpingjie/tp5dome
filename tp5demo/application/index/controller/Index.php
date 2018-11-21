<?php
namespace app\index\controller;
use base\payment\Pay;
class Index
{
    public function index()
    {
        /*$data["id"] = 1;
        $data["name"] = "shing";
        $data["sex"] = "nan";
        $data["sign"] = md5("id=1&name=shing&key=".config("myconfig.md5_key"));
        if(!chk_md5_sign($data,["id","name"],config("myconfig.md5_key")))
        {
            echo '<br>error';
            exit();
        }
        else
        {
            echo 'succ';
            exit();
        }
        exit();*/

    	$data["goods_ids_nums"] = [
    		["id"=>1,"num"=>2],
    		["id"=>2,"num"=>3],
    	];
    	//$data["goods_nums"] = [2,3];
    	$data["user_id"] = 1;
    	$data["app_id"] = 1;
    	$data["payway_name"] = "wxpay";
    	$data["imeil"] = "imeil_123456";
    	$data["coupon_money"] = 1;
    	$res = logic("orders")->pay($data);
    	if(!$res)
    	{
    		echo logic("orders")->getError();
    	}
    	dump($res);
        /*
        $res = logic("orders")->notifySucc("201801162131338347");
    	if(!$res)
    	{
    		echo logic("orders")->getError();
    	}
    	dump($res);*/

    	/*$pay = new Pay("wxpay");
    	$order_info = logic("orders")->infoByOrdersn("201801162114315343");
    	$pay->init($order_info);
        $mydata = $pay->native();
        $mydata = $pay->notifyUrl();*/
    }

    public function notity()
    {
    }
}
