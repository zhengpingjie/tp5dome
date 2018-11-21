<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
Route::get('login', 'admin/Login/index');
Route::post('login', 'admin/Login/login');
Route::get('logout', 'admin/Login/logout');


// Home & profile
Route::get('home', 'admin/Index/index');
Route::get('profile', 'admin/Index/profile');
Route::post('profile', 'admin/Index/update');
Route::post('upload', 'admin/Index/upload');

// Manager
Route::resource('manager','admin/Manager');


// Group
Route::resource('group','admin/Group');

// Menu
Route::resource('menu', 'admin/Menu');
Route::get('menu/pid/:pid','admin/Menu/index');
Route::get('menu/create/pid/:pid','admin/Menu/create');
Route::get('menu/clear', 'admin/Menu/clear');



//service