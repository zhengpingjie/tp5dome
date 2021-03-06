<?php
namespace app\admin\model;

use think\Model;
use think\Db;
class News extends BaseModel
{
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
        $sql = "select distinct a.id from bhp_news as a left join bhp_news_flag as b on a.id=b.news_id where 1" ;
        $sql_count = "select count(distinct a.id) as count from bhp_news as a left join bhp_news_flag as b on a.id=b.news_id where 1" ;
        
        $sql_where = "";
        if($search["title"])
        {
            $sql_where .= " and title = ?" ;
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
            $sql_order= " order by id desc " ;
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
            $sql = "select distinct a.* from bhp_news as a left join bhp_news_flag as b on a.id=b.news_id where a.id in (".$str_ids.")" ;
            $sql .= $sql_order;
            $list = Db::query($sql);
        }
        if($list)
            $list = collection($list)->toArray();
        return $list ;
    }

    public function viewInfo($id,$field='',$order='')
    {
        $where["a.id"] = $id;
        $res = $this->viewInfoBy($where,$field,$order);
        return $res;
    }

    public function viewInfoBy($where,$field='',$order='')
    {
        $res    = Db::view('bhp_news a','*')
                ->view('bhp_news_detail b','body','a.id=b.id')
                ->where($where)
                ->find();
        return $res;
    }

    public function viewListByIds($ids,$field='',$limit=100,$order='id desc')
    {
        $where["a.id"] = array('in',$ids);
        $res =  $this->viewListBy($where,$field,$limit,$order);
        return $res;
    }

    public function viewListBy($where=null,$field='',$limit=100,$order='id desc')
    {
        $res    = Db::view('bhp_news a','*')
                ->view('bhp_news_detail b','body','a.id=b.id')
                ->where($where)
                ->select();
        return $res;
    }


    public function upd($data){
        return parent::upd($data,function($data){
            if($_FILES){
                foreach($_FILES as $key=>$value){
                    $file = $this->upload($key);
                    $data[$key] = $file[0]['url'];
                }
            }
            return $data;
        });
    }
}