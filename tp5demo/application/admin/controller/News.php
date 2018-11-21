<?php
namespace app\admin\controller;

use app\admin\logic\Authentication;
use think\Controller;
use think\Request;
use base\struct\Tree;

class News extends BaseController
{
	public function index()
	{
		$search["title"] = input("get.title") ;
		$search["id"] = input("get.id") ;
        $search['start_time'] = input("get.start_time");
        $search['end_time'] = input("get.end_time");
        $page = input("param.p",1) ;
        $page_size =2;
        $list = logic("news")->search($search,"id desc",$page,$page_size,$count) ;
        $page_html = get_page_html($count,$page_size);
        $this->data["list"] = $list;
        $this->data["search"] = $search;
        $this->data["page_html"] = $page_html;  

        $app_list = logic("app")->listBy() ;
        $news_type_list = logic("news_type")->listBy() ;
        $tree = new Tree($news_type_list);
        $news_type_list = $tree->getArrTreeSortAdorn();

        $this->data["app_list"] = $app_list;
        $this->data["news_type_list"] = $news_type_list;
        if(!empty($search) && is_array($search)){
            $this->data = array_merge($this->data,$search);
        }
		return view("index", $this->data);
	}

	public function edit()
    {
    	$id = input('param.id','0') ;
        if($id)
        {
            $info = logic($this->model)->viewInfo($id);
            $this->data["info"] = $info;
        }
        $app_list = logic("app")->listBy() ;
        $news_type_list = logic("news_type")->listBy() ;
        $tree = new Tree($news_type_list);
        $news_type_list = $tree->getArrTreeSortAdorn();

        $this->data["app_list"] = $app_list;
        $this->data["news_type_list"] = $news_type_list;
        return view("edit", $this->data);
    }

    public function ajax_title(){
        $title = input('param.title','');
        $where = array('title'=>array('like','%'.$title.'%'));
        $list = logic('news')->listby($where);
        $res = array_column($list, 'title');
        return json($res);
    }

}