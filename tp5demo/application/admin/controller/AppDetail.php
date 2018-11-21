<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class AppDetail extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $search["name"] = input("param.name","") ;
        $page = input("param.p",1) ;
        $page_size =10;
        $list = logic("app_detail")->search($search,"id desc",$page,$page_size,$count) ;

        $app_list = logic("app")->listBy() ;
        if(!empty($app_list) && !empty($list)){
            foreach ($app_list as $al){
                foreach ($list as &$li){
                    if($li['app_id'] == $al["id"]){
                        $li['app_name'] =  $al['name'];
                    }
                }
            }
        }

        $page_html = get_page_html($count,$page_size);
        $this->data["page_html"] = $page_html;

        $this->data["list"] = $list;
        $this->data["search"] = $search;
        return view("index", $this->data);
    }

    public function applist(){
        $search["type_id"] = input("param.type_id","") ;
        $list = logic("app")->search($search) ;
        echo json_encode($list);exit();
    }

    public function edit()
    {
        $id = input('param.id','0') ;
        if($id)
        {
            $where["id"] = $id;
            $info = logic("app_detail")->infoby($where);
            if(!empty($info)){
                $app_info =  logic("app")->info($id);
                $info['name'] = $app_info['name'];
                $type_id = $app_info["type_id"] ?  $app_info["type_id"] :'1';
                $wheres['type_id'] = $type_id;
                $app_list=logic("app")->search($wheres);
            }

            $this->data["info"] = $info;
        }
        $type_list = logic("app_type")->listBy() ;
        $this->data["type_list"] = $type_list;
        $this->data["app_list"] = $app_list;
        return view("edit", $this->data);
    }



    public function updd(){
        $imgs = $this->upload("imgs");
        $id = input('post.id','0') ;
        if($id)
        {

            $res= model($this->model)->updd($imgs);
            if($res !== false)
            {
                $this->success('修改成功');
            }
            else
            {
                $msg = model($this->model)->getError() ;
                $this->error($msg);
            }
        }
        else
        {
            $res = model($this->model)->addd($imgs);
            if($res)
            {
                $this->success("添加成功",url("index"));
            }
            else
            {
                $this->error(model($this->model)->getError());
            }
        }


    }

    public function upload($img)
    {
        $file = request()->file($img);
        $url = "";
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $url = '/uploads/' . $info->getSaveName();
            }else{
                $this->error('上传失败。'.$file->getError());
            }
        }

        return $url;
    }
}