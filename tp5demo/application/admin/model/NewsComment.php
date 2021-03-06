<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class NewsComment extends BaseModel
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

    public function search($search,$order_by,$page,$page_size,&$count)
    {
        $params = array();
        $sql = "select a.id from bhp_news_comment as a ,bhp_news as b where a.news_id=b.id " ;
        $sql_count = "select count(1) from bhp_news_comment as a ,bhp_news as b where a.news_id=b.id  " ;
        
        $sql_where = "";
        if($search["title"])
        {
            $sql_where .= " and a.title = ?" ;
            $params[] = $search["title"];
        }

        $sql .= $sql_where;
        $sql_count .= $sql_where;

        if($order_by)
        {
            $sql_order = " order by " . $order_by ;
        }
        else
        {
            $sql_order= " order by a.id desc " ;
        }
        $sql .= $sql_order;
        $count = 0;
        if($page>0 && $page_size>0)//如果要分页
        {
            $info = Db::query($sql_count,$params);
            $count =$info[0]["count"];
            $offset=$page_size*($page - 1);
            $sql .= " limit $offset,$page_size" ;  
        }

        $id_list = Db::query($sql,$params);
        $id_arr = array_column($id_list,"id");
        $str_ids = implode(",",$id_arr) ; ;

        if($str_ids)
        {
            $sql = "select a.*,b.title as news_title from bhp_news_comment as a ,bhp_news as b where a.news_id=b.id and a.id in (".$str_ids.")" ;
            $sql .= $sql_order;
            $list = Db::query($sql);
        }
        if($list)
            $list = collection($list)->toArray();
        return $list ;
    }
}