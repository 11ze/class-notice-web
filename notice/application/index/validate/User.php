<?php

namespace app\index\validate;

use think\Validate;
use think\captcha\Captcha;
use app\index\model\User as UserModel;

class User extends Validate
{
    protected $rule = [
        'username|用户名' => 'require|max:25',
        'fullname|姓名'   => 'require|max:25',
        'email'           => 'email',
        'passwds'         => 'checkPasswd',
        'passwd|密码'     => 'require|min:6|max:16',
        'user'            => 'uni_user',
        'captcha'         => 'require|check_captcha:null',
        'legal_login'     => 'check_login'
    ];

    protected $message = [
        'email'            => '邮箱格式错啦',
        'captcha.require'  => '忘了输入验证码',
    ];

    protected $scene = [
        'register' => ['username', 'fullname', 'passwd', 'passwds', 'user'],
        'login'    => ['captcha', 'username', 'passwd', 'legal_login'],
        'update'   =>['passwd', 'passwds', 'legal_login'],
    ];

    protected function checkPasswd($passwds)
    {
        // 检查密码是否一致
        if($passwds[0] !== $passwds[1]){
            return '两次输入的密码不一致';
        }else{
            return true;
        }
    }

    protected function uni_user($username)
    {
        // 检查用户名是否已存在
        $result = UserModel::get(['username'=>$username]);
        if($result == ''){
            return true;
        }else{
            return '用户名被用了!';
        }
    }

    protected function check_captcha($value)
    {
        $captcha = new Captcha();
        if($captcha->check($value)){
            return true;
        }else{
            return '验证码错了!';
        }
    }

    protected function check_login($user_info)
    {
        // 检查用户名和密码是否正确
        $username = $user_info[0];
        $passwd = $user_info[1];
        $passwd = sha1($passwd);
        $result = UserModel::where('username', $username)
            ->where('password', $passwd)
            ->select();
        if(count($result) <= 0){
            return '用户名或密码错误!';
        }
        return true;
    }
}
