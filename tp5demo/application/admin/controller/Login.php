<?php

namespace app\admin\controller;

use app\admin\logic\Authentication;
use think\Controller;
use think\Request;

class Login extends Controller
{
    protected $request;

    public function __construct()
    {
        $this->request = Request::instance();
    }

    public function index()
    {
        if (Authentication::checkLogin()) {
            return $this->redirect('/home');
        }
        return view('login');
    }

    public function login()
    {
        $login = Authentication::login($this->request->post('name'), $this->request->post('password'));
        if ($login === 0) {
            return $this->error('用户名或密码错误');
        } elseif ($login === -1) {
            return $this->error('用户不存在或被禁用');
        } else {
            return $this->success('登录成功', '/home');
        }
    }

    public function logout()
    {
        Authentication::logout();
        $this->redirect('/login');
    }
}
