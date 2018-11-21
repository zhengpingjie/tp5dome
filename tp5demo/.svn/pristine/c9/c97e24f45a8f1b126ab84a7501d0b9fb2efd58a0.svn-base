<?php
namespace app\admin\controller;
use think\Controller;
use base\Lists;
use base\RedisCache;
use struct\Tree;
class NewsComment extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
	{
		$search["title"] = input("get.title") ;
		$search["id"] = input("get.id") ;
        $page = input("param.p",1) ;
        $page_size =2;
        $list = logic("news_comment")->search($search,"",$page,$page_size,$count) ;

        $page_html = get_page_html($count,$page_size);

        $this->data["list"] = $list;
        $this->data["search"] = $search;
        $this->data["page_html"] = $page_html;  

		return view("index", $this->data);
	}
}