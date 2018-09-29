<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Exception;
use think\Session;
use app\index\model\User as UserModel;

class User extends Controller
{
    public function login()
    {
        // 用户登录界面
        $this->assign('page', '用户登录');
        $this->assign('act_login', 'active');
        return $this->fetch();
    }

    public function do_login(Request $request)
    {
        // 用户登录
        $username  = $request->param('username');
        $passwd    = $request->param('password');
        $captcha   = $request->param('captcha');
        $record    = $request->param('record');
        $user_info = array($username, $passwd);
        // 保存用户信息报错前的表单输入
        Session::set('login_name', $username);

        $data = [
            'username'    => $username,
            'passwd'      => $passwd,
            'captcha'     => $captcha,
            'legal_login' => $user_info,
        ];
        // 要在验证数据后面进行加密, 否则密码加密后的长度过长
        $passwd = sha1($passwd);
        try{
            $result = $this->validate($data, 'User.login');
            if(true !== $result){
                throw new Exception($result);
            }

            Session::delete('login_name');
            $user = UserModel::get($username);
            Session::set('username', $username);
            Session::set('fullname', $user->fullname);
            Session::set('job', $user->job);
            if($record != ''){
                cookie('username', $username, 3600*24*7);
            }
            $this->success('登录成功', 'notice/index', '', 1);
        }catch(Exception $e){
            $this->error($e->getMessage(), 'user/login','', 1);
        }

    }

    public function register()
    {
        // 用户注册界面
        $this->assign('page', '用户注册');
        $this->assign('act_register', 'active');
        return $this->fetch();
    }

    public function do_register(Request $request)
    {
        // 注册用户
        try{
            // 实例化模型
            $user = new UserModel;
            // 获取表单输入并赋值给模型属性
            $user->username = $request->param('username');
            $user->fullname = $request->param('fullname');
            $user->job      = $request->param('job');
            $passwd1 = $request->param('passwd');
            $passwd2 = $request->param('passwd2');
            $passwds = array($passwd1, $passwd2);
            $user->password = sha1($passwd1);  // 使用sha1对密码加密

            // 保存用户信息报错前的表单输入
            Session::set('username', $user->username);
            Session::set('name', $user->fullname);
            Session::set('job', $user->job);

            // 需要验证的数据
            $data = [
                'username' => $user->username, 
                'fullname' => $user->fullname, 
                'passwd'   => $passwd1,
                'passwds'  => $passwds,
                'user'     => $user->username,
            ];

            // 注册用户
            $result = $user->allowField(true)->validate('User.register')->save($data);
            if(false === $result){
                throw new Exception($user->getError());
            }else{
                session(null);
                $this->success('注册成功', 'user/login');
            }
        }catch(Exception $e){
            $this->error($e->getMessage(), 'user/register', '', 1);
        }

    }

    public function change_passwd()
    {
        // 修改密码界面
        $this->assign('page', '修改密码');
        return $this->fetch();
    }

    public function do_change_passwd(Request $request)
    {
        // 修改密码
        $old_passwd  = $request->param('old_passwd');
        $new_passwd1 = $request->param('new_passwd1');
        $new_passwd2 = $request->param('new_passwd2');
        $username = session('username');  // 用于验证用户名和密码 
        $passwds =array($new_passwd1, $new_passwd2);
        $user_info = array($username, $old_passwd);

        // 验证数据, 表单是否填写正确
        $data = [
            'passwd'  => $old_passwd,
            'passwd'  => $new_passwd1,
            'passwd'  => $new_passwd2,
            'passwds' => $passwds,
            'legal_login' => $user_info,
        ];
        try{
            $user = UserModel::get($username);
            $user->password = sha1($new_passwd1);
            // 验证, 更新密码
            $result = $user->allowField(true)->validate('User.update')->save($data);
            if(false === $result){
                throw new Exception($user->getError());
            }else{
                session(null);
                $this->success('修改成功, 请重新登录～', 'user/login', '', 1);
            }
        
        }catch(Exception $e){
            $this->error($e->getMessage(), 'user/change_passwd', '', 1);
        }
    }

    public function logout()
    {
        // 退出登录
        session(null);
        $this->success('退出成功', 'user/login', '', 1);
    }

}
