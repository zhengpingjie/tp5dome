<?php

namespace app\admin\controller;

class Index extends Base
{

    public function index()
    {
        return view('index', $this->data);
    }

    public function profile()
    {
        return view('profile', $this->data);
    }

    public function update()
    {
        $avatar = $this->request->post('avatar');
        $nick_name = $this->request->post('nick_name');
        $new_password = $this->request->post('new_password');
        $re_password = $this->request->post('re_password');
        $password = $this->request->post('password');

        $validate = $this->validate([
            'password' => $password,
            'nick_name' => $nick_name,
            'pwd' => $new_password,
        ], [
            'password' => 'require',
            'nick_name' => 'max:25',
            'pwd' => 'length:6,25',
        ]);

        if ($validate !== true) {
            $this->error('密码必填');
        }

        if ($this->admin->checkPwd($password)) {
            $this->admin->nick_name = $nick_name;
            $this->admin->avatar = $avatar;

            if ($new_password) {
                if ($new_password == $re_password) {
                    $this->admin->pwd = $new_password;
                } else {
                    $this->error('两次密码不一致');
                }
            }

            if (false !== $this->admin->save()) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败');
            }
        } else {
            $this->error('密码错误');
        }
    }

    public function upload()
    {
        $file = request()->file('image');

        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $url = '/uploads/' . $info->getSaveName();
                $this->success('上传成功', null, $url);
            }else{
                $this->error('上传失败。'.$file->getError());
            }
        }
    }
}
