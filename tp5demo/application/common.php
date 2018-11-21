<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Route;

//Route::domain('admin','admin');
error_reporting(E_ERROR | E_PARSE );
/**
*逻辑层操作方法
*@name 模块名称
*/
function logic($name)
{
    $is_exists = strpos($name, "admin/") === 0;
    if(!$is_exists)
    {
        $name = "admin/".$name;
    }
    return think\Loader::model($name, "logic", false);
}
/**
*服务层操作方法
*@name 模块名称
*/
function service($name)
{
    $is_exists = strpos($name, "admin/") === 0;
    if(!$is_exists)
    {
        $name = "admin/".$name;
    }
    return think\Loader::model($name, "service", false);
}

function model($name)
{
    $is_exists = strpos($name, "admin/") === 0;
    if(!$is_exists)
    {
        $name = "admin/".$name;
    }
    return think\Loader::model($name, "model", false);
}


/**
*将对象转换成数组
*@obj 对象
*/
function object_to_array($obj) 
{  
    if(is_object($obj)) 
    {  
        $array = (array)$obj;  
    } 
    if(is_array($obj)) 
    {  
        foreach($obj as $key=>$value) 
        {  
            $array[$key] = object_to_array($value);  
        }  
     }  
     return $array;  
}

function curl_get()
{
    $UserAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);  //0表示不输出Header，1表示输出
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $data = curl_exec($curl); 
    curl_close($curl);  
    //echo $data;
    return $data;
}

function curl_post()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// rsa 加密
function rsa_encode($data, $pub_key)
{
    
    // 加密分片长度
    $rsa_len = (int)C('RSA_LEN');
    $max = $rsa_len / 8 - 11;
    
    $pu_key = openssl_pkey_get_public($pub_key);
    $arr = array();
    $len = strlen($data);
    if( $len > $max ){
        while( strlen($data) > $max ){
            $pick = substr($data, 0, $max);
            $data = substr($data, $max);
            openssl_public_encrypt($pick, $crypted, $pu_key);
            
            $arr[] = $crypted;
        }
    }
    openssl_public_encrypt($data, $crypted, $pu_key);
    $arr[] = $crypted;
    
    $crypted = implode('', $arr);
    
    return gzencode(base64_encode($crypted),9);
}

// rsa解密
function rsa_decode($data, $private_key){
    $data = gzinflate(substr($data,10,-8));
    //api_log($data,'rsa_decode',false);
    $data = base64_decode($data);
    //解密分片长度
    $rsa_len = (int)C('RSA_LEN');
    $max = $rsa_len / 8 ;
    
    $check = false;
    // 获取到请求流数据，需要判断解密是否正确
    if( $data ){
        $check = true;
    }
    
    $pi_key=openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id 
    $arr = array();
    $len = strlen($data);
    if( strlen($data) > $max ){
        while( strlen($data) > $max ){
            $pick = substr($data, 0, $max);
            $data = substr($data, $max);
            openssl_private_decrypt($pick,$decrypted,$pi_key);
            
            $arr[] = $decrypted;
        }
    }
    openssl_private_decrypt($data,$decrypted,$pi_key);
    $arr[] = $decrypted;
    
    $decrypted = implode('',$arr);
    
    // 数据流有效，解密出错，公钥不正确
    if( $check && strlen($decrypted) == 0 ){
        return json_encode(array('error'=>'public_key_error'));
    }
    
    return $decrypted;
}

//压缩并加密
function compression($str) {
    $arr = fixedArr();
    $str = base64_encode($str);
    $str = encode($str,$arr);
    return gzencode($str,9);
}

//解压并解密
function decompression($str) {
    $arr = fixedArr();
    $tmp = gzinflate(substr($str,10,-8));
    $tmp = decode($tmp,$arr);
    return base64_decode($tmp);
}

//加密
function encryption($str) {
    $arr = fixedArr();
    $str = base64_encode($str);
    return encode($str,$arr);
}
function fixedArr() {
    $arr = array('0', '1', '2', '3', '4', '5', '6', '7', '8','9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 
        'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 
        'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 
        'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 
        'Z', '*', '!'
    );

    return $arr;
}
/**
 * 
 * 加密函数
 *
 * $str 加密的字符串
 * $arr 固定数组
 */
function encode($str,$arr) 
{
    if ($str == null) {
      return "";
    }
    
    $rsstr = "x";
    $toarr = str_split($str);
    $arrlenght = count($arr);
    for ($i=0;$i<count($toarr);$i++) {
        $string = ord($toarr[$i]) + ord($arr[$i % $arrlenght]);
        $rsstr .= $string."_";
    }

    $rsstr = substr($rsstr,0,-1);
    $rsstr .= "y";
    return $rsstr;
}

/**
 * 
 * 解密函数
 *
 * $str 解密的字符串
 * $arr 固定数组
 */
function decode($str,$arr) {
    if ($str == '') {
      return '';
    }

    $first = substr($str,0,1);
    $end = substr($str,-1);

    if ($first == 'x' && $end == 'y') {
        $str = substr($str,1,-1);
        $toarr = explode("_",$str);
        $arrlenght = count($arr);
        $rsstr = '';
        for ($i=0;$i<count($toarr);$i++) {
            $string = $toarr[$i] - ord($arr[$i % $arrlenght]);
            $rsstr .= chr($string);
        }

        return $rsstr;
    } else {
        return "";
    }
}

function mylog($data,$filename, $json = true)
{
    $log_path = C('LOG_PATH').'../'.date('y_m_d').'_'.$filename.'.log';
    if($json)
    {
        $data = json_encode($data);
    }
    \Think\Log::write($data,'Log','',$log_path);
}

/**
*复制文件夹
*src 源文件夹
*dst 目标文件夹
*/
function copy_dir($src,$dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) )
    {
        if (( $file != '.' ) && ( $file != '..' )) 
        {
            if ( is_dir($src . '/' . $file) ) 
            {
                copy_dir($src . '/' . $file,$dst . '/' . $file);
                continue;
            }
            else
            {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

/**
*描述：把数组元素组合为字符串
*$char：规定数组元素之间放置的内容
*$arr：数组
*$is_unique：是否过滤重复的内容
*/
function myimplode($char,$arr,$is_unique=true)
{
    if($arr)
    {
        if($is_unique)
        {
            $arr = array_unique($arr);
        }
        $res = implode($char,$arr);
    }
    return $res;
}

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */
/**
 *  基础分页的相同代码封装，使前台的代码更少
 * @param $m 模型，引用传递
 * @param $where 查询条件
 * @param int $pagesize 每页查询条数
 * @return 分页的html代码
 */
function get_page_html($count, $pagesize = 10) 
{
    $p = new \base\Page($count, $pagesize);
    $p->lastSuffix = false;
    $p->setConfig('header', '<span class="current">共%TOTAL_ROW%条&nbsp;共%TOTAL_PAGE%页</span>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $p->parameter = input("param.");
    return $p->show();
}


function get_colval($list,$column_key,$other_column_key,$other_column_val)
{
    if($list)
    {
        foreach ($list as $row) 
        {
            if($row[$other_column_key] == $other_column_val)
            {
                $res = $row[$column_key];
                break;
            }
        }
    }
    return $res;
}

function chk_md5_sign($data,$sel_col_keys,$md5_key)
{
    foreach($data as $key => $value)
    {
        if(in_array($key,$sel_col_keys))
        {
            $mydata[$key] = $value;
        }
    }
    if(!$mydata)
        return false;
    ksort($mydata);
    $str = to_url_params($mydata);
    if(!$str)
        return false;
    $str = $str."&key=".$md5_key;
    $str = md5($str);
    if(strtolower($data["sign"]) == strtolower($str))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function to_url_params($data)
{
    $buff = "";
    if($data)
    {
        foreach($data as $key => $value) 
        {
            $buff .= $key . "=" . $value . "&";
        }
    }
    $buff = trim($buff, "&");
    return $buff;
}
