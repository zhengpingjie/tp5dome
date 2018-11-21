<?php

namespace app\admin\controller;

use app\admin\logic\Authentication;
use think\Controller;
use think\Request;

class BaseController extends Base
{
	
	public $model;
    public $page_size = 20;
    function __construct()
    {
        $this->model = request()->controller();
        parent::__construct();
    }

    public function index()
    {
        $list = logic($this->model)->search($search) ;
        $this->data["list"] = $list;
        return view("index", $this->data);
    }

    public function edit()
    {
        $id = input('param.id','0') ;
        if($id)
        {
            $info = logic($this->model)->info($id);

            $this->data["info"] = $info;
        }
        return view("edit", $this->data);
    }

    public function upd()
    {
        $id = input('post.id','0') ;
        if($id)
        {

            $res = logic($this->model)->upd() ;
            if($res !== false)
            {
                $this->success('修改成功');
            }
            else
            {
                $msg = logic($this->model)->getError() ;
                $this->error($msg);
            }
        }
        else
        {
            $res = logic($this->model)->add() ;
            if($res)
            {
                //$urls["回列表页"] = url("news/index");
                //$urls["继续添加"] = url("news/edit");
                $this->success("添加成功",url("index"));
            }
            else
            {
                $this->error(logic($this->model)->getError());
            }
        }
    }

    public function set_flag()
    {
        $ids = input('id/a');
        $flag = input('flag_set',0);
        if(!$ids)
        {
            $this->success('请选择要设置的内容');
            exit();
        }
        if(!$flag)
        {
            $this->error('请选择操作');
            exit();
        }
        $res = logic($this->model)->setFlag($ids,$flag);
        if($res)
        {
            $this->success('设置成功');   
        }
        else
        {
            $this->error('设置失败');   
        }
    }
    public function unset_flag()
    {
        $ids = input('id/a');
        $flag = input('flag_unset',0);
        if(!$ids)
        {
            $this->success('请选择要取消设置的内容');
            exit();
        }
        if(!$flag)
        {
            $this->error('请选择操作');
            exit();
        }

        $res = logic($this->model)->unsetFlag($ids,$flag);
        if($res !== false)
        {
            $this->success('取消设置成功');   
        }
        else
        {
            $this->error('取消设置失败');   
        }
    }

    public function del()
    {
        $ids = input('id/a');
        $res = logic($this->model)->del($ids);
        if($res)
        {
            $this->success('删除成功');   
        }
        else
        {
            $this->error('删除失败');   
        }
    }
    public function upd_sort()
    {
        $sorts = input('sort/a');
        $res = logic($this->model)->updSort($sorts);
        if($res)
        {
            $this->success('排序成功');
        }
        else
        {
            $this->error('排序失败');
        }
    }
    public function upd_status()
    {
        $ids = input('post.id/a');
        $status = input('post.status_op',0);
        if(!$ids)
        {
            $this->success('请选择要设置的内容');
            exit();
        }
        if(!$status)
        {
            $this->success('请选择操作');
            exit();
        }
        $res = logic($this->model)->updAttr($ids,"status",$status);
        if($res)
        {
            $this->success('操作成功');
        }
        else
        {
            $this->error('操作失败');
        }
    }

}