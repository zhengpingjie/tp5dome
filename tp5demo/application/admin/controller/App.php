<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
use think\Config;
class App extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $search["name"] = input("param.name","") ;
        $search["type_id"] = input("param.type_id") ;
        $page = input("param.p",1) ;
        $page_size =10;
        $list = logic("app")->search($search,"id desc",$page,$page_size,$count) ;

        $type_list = logic("app_type")->listBy() ;
        if(!empty($type_list) && !empty($list)){
            foreach ($type_list as $tl){
                foreach ($list as &$li){
                    if($li['type_id'] == $tl["id"]){
                        $li['type_name'] =  $tl['name'];
                    }
                }
            }
        }
        $page_html = get_page_html($count,$page_size);
        $this->data["page_html"] = $page_html;

        $this->data["list"] = $list;
        $this->data["type_list"] = $type_list;
        $this->data["search"] = $search;
        return view("index", $this->data);
    }

    public function edit()
    {
        $id = input('param.id','0');
        if($id)
        {
            $info = logic("app")->info($id);
            if(!empty($info)){
                $this->model = "AppDetail";
                $where['app_id'] = $id;
                $infoDetail = logic("AppDetail")->infoby($where);
                if(!empty($infoDetail)){
                    unset($infoDetail['id']);
                    $info = array_merge($info,$infoDetail);
                }
            }
            $this->data["info"] = $info;
        }
        $app_list = logic("app")->listBy();
        $type_list = logic("app_type")->listBy();
        $this->data["app_list"] = $app_list;
        $this->data["type_list"] = $type_list;
        return view("edit", $this->data);
    }

    public function updd(){
        $ico = $this->upload("ico");
        $qrcode = $this->upload("qrcode");
        $data['imgs'] = $this->upload("imgs");
        $data['imgone'] = $this->upload("imgone");
        $data['imgtwo'] = $this->upload("imgtwo");
        $data['imgthree'] = $this->upload("imgthree");
        $data['imgfour'] = $this->upload("imgfour");
        $data['imgfive'] = $this->upload("imgfive");
        $id = input('post.id','0') ;
        $data['app_id'] = $id;
        $data['desp'] = input('post.desp','0') ;
        if($id)
        {
           $res= model($this->model)->updd($ico,$qrcode);
            if($res !== false)
            {
                if(!empty($data)){
                    $this->model = "AppDetail";
                    $where['app_id'] = $data['app_id'];
                    $res= model($this->model)->updd($data,$where);
                }
                if($res){
                    $this->success('修改成功');
                }
            }
            else
            {
                $msg = model($this->model)->getError() ;
                $this->error($msg);
            }
        }
        else
        {
            $res = model($this->model)->addd($ico,$qrcode);
            if($res)
            {
                if(!empty($data)){
                    $this->model = "AppDetail";
                    $data['app_id'] = $res['id'];
                    $res= model($this->model)->addd($data);
                    if($res){
                        $this->success("添加成功",url("index"));
                    }

                }

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
                $url =  Config::get("host").'/uploads/' . $info->getSaveName();
            }else{
                $this->error('上传失败。'.$file->getError());
            }
        }

        return $url;
    }
}