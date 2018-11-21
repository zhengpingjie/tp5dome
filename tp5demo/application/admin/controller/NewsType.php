<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use base\struct\Tree;
class NewsType extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
	{
		$search["name"] = input("param.name","") ;
		$search["app_id"] = input("param.app_id") ;

        $list = logic("news_type")->search($search,"id desc",$page,$page_size,$count) ;

        $app_list = logic("app")->listBy() ;
        $news_type_list = logic("news_type")->listBy() ;

        $this->data["list"] = $list;
        $this->data["app_list"] = $app_list;
        $this->data["news_type_list"] = $news_type_list;
        $this->data["search"] = $search;
		return view("index", $this->data);
	}

	public function edit()
    {
    	$id = input('param.id','0') ;
        if($id)
        {
            $info = logic("news_type")->info($id);

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

}