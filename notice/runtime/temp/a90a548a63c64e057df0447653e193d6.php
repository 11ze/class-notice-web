<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:61:"/home/ze/apache/notice/application/index/view/user/login.html";i:1535130152;s:64:"/home/ze/apache/notice/application/index/view/notice/header.html";i:1535130152;s:64:"/home/ze/apache/notice/application/index/view/notice/footer.html";i:1535130152;}*/ ?>
<!DOCTYPE html>
<html>
  <head>
      <meta name="description" content="自己做的第一个web网站, 使用tp5和bootstrap框架, 用于发布 查看和管理班级公告" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="/notice/public/static/common/images/logo.ico">
    <title><?php echo (isset($title) && ($title !== '')?$title:'宝贝请注意'); ?></title>
    <link rel="stylesheet" href="/notice/public/static/common/bootstrap-3/css/bootstrap.css">
    
  </head>
  
  
  <body background="/notice/public/static/common/images/back.jpg">
    <div class="container">
        <div class="row clearfix">
            <div class="col-md-12 column">
                <div class="page-header">
                    <h1>
                        宝贝请注意<small>&nbsp;>>&nbsp;<?php echo $page; ?></small>
                    </h1>
                </div>
                <nav class="navbar navbar-default" role="navigation">
                    <div class="navbar-header">
                         
                         <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button> 
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            
                            <li class="<?php echo (isset($act_list) && ($act_list !== '')?$act_list:''); ?>">
                                 <a href="<?php echo url('notice/index'); ?>">公告列表</a>
                            </li>
                            <li class="<?php echo (isset($act_deliver) && ($act_deliver !== '')?$act_deliver:''); ?>">
                                 <a href="<?php echo url('notice/deliver'); ?>">发布公告</a>
                            </li>
                            <li class="<?php echo (isset($act_manage) && ($act_manage !== '')?$act_manage:''); ?>">
                                 <a href="<?php echo url('notice/manage'); ?>">管理公告</a>
                            </li>
                        
                        </ul>
                        
                        <ul class="nav navbar-nav navbar-right">
                        
                        <?php if(!session('?fullname')): ?>
                            <li class="<?php echo (isset($act_login) && ($act_login !== '')?$act_login:''); ?>">
                                <a href="<?php echo url('user/login'); ?>">登录</a>
                            </li>
                            <li class="<?php echo (isset($act_register) && ($act_register !== '')?$act_register:''); ?>">
                                <a href="<?php echo url('user/register'); ?>">注册</a>
                            </li>
                        <?php else: ?>
                            <li>
                                 
                                 <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><?php echo session('fullname'); ?><strong class="caret"></strong></a>
                                <ul class="dropdown-menu">
                                    <li>
                                         <a href="<?php echo url('user/change_passwd'); ?>">修改密码</a>
                                    </li>
                                    <li>
                                         <a href="<?php echo url('user/logout'); ?>">退出</a>
                                    </li>
                                    
                                </ul>
                            </li>
                        <?php endif; ?>
                        </ul>
                    </div>

                </nav>
                <div class="row clearfix">
                    <div class="col-md-9 column">

<form class="form-horizontal" role="form" method="post" action="<?php echo url('user/do_login'); ?>">
    <div class="form-group">
         <label for="username" class="col-sm-2 control-label">用户名</label>
        <div class="col-sm-10">
            
            <?php if(session('?login_name')): ?>
                <input class="form-control" name="username" type="text"  placeholder="请输入用户名" value="<?php echo session('login_name'); ?>"/>
            <?php else: ?>
                <input class="form-control" name="username" type="text"  placeholder="请输入用户名" value="<?php echo cookie('username'); ?>"/>
            <?php endif; ?>
            
        </div>
    </div>
    <div class="form-group">
         <label for="password" class="col-sm-2 control-label">密&nbsp;&nbsp;码&nbsp;</label>
        <div class="col-sm-10">
            <input class="form-control" name="password" type="password"  placeholder="请输入密码"/>
        </div>
    </div>
    <div class="form-group">
         <label for="captcha" class="col-sm-2 control-label">验证码</label>
        <div class="col-sm-4">
            <img src="<?php echo captcha_src(); ?>" onclick="javascript:this.src=this.src+'?time='+Math.random()" >
            <p class="help-block">
                点击图片刷新
            </p>
        </div>
        <div class="col-sm-6">
            <input class="form-control" type="text" name="captcha" placeholder="请输入验证码">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <div class="checkbox">
                 <label><input type="checkbox" name="record"/>记住我</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
             <button type="submit" class="btn btn-default">登录</button>
        </div>
    </div>
</form>

                    </div>
                    
                    <div class="col-md-3 column">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    班级信息
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p>班级：计算机161</p>
                                <p>人数：58</p>
                                <p>班主任：</p>
                                <p>联系方式：</p>
                            </div>
                            <div class="panel-footer">
                                今天适宜看公告
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
  <script src="/notice/public/static/common/jquery-3.3.1.min.js"></script>
  <script src="/notice/public/static/common/bootstrap-3/js/bootstrap.js"></script>
  
  <script>
    window.onload=function(){
        var add_file = document.getElementById("add_file");
        add_file.onclick=function(){
            var file = "<input name='input_file[]' type='file' />";
            file_form.innerHTML += file;
        }
    }   
  </script>
  </body>
</html>

