<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Exception;
use think\Session;
use think\Db;
use app\index\model\Notice as NoticeModel;
use app\index\model\File as FileModel;

class Notice extends Controller
{
    public function index()
    {
        // 公告展示页面
        $this->assign('page', '公告列表');
        $this->assign('act_list', 'active');
        // 读取所有公告, 并按时间排序
        $notice_list = Db::name('notice')->order('create_at desc')->paginate(5);
        $this->assign('list', $notice_list);
        return $this->fetch();
    }

    public function deliver()
    {
        $this->check_login_identity();
        // 发布公告页面
        $this->assign('page', '发布公告');
        $this->assign('act_deliver', 'active');
        return $this->fetch();
    }

    public function do_deliver(Request $request)
    {
        $this->check_login_identity();
        // 发布公告
        try{
            $notice  = new NoticeModel;
            $title = trim($request->param('title'));
            $notice->fullname = session('fullname');
            $notice->title    = $title;
            $notice->content  = $request->param('content');
            $notice->job      = session('job');
            $files   = $request->file("input_file");
            // 判断是否有上传文件
            if(count($files) > 0){
                $notice->extra = 1;
            }else{
                $notice->extra = 0;
            }
            // 验证标题
            $data = [
                'title'       => $title,
                'uni_title'   => $title,
            ];
            // 验证公告信息并写入数据库
            $result = $notice->allowField(true)->validate('Notice.deliver')->save($data);
            // 将文件信息存储到数据库
            $this->add_file($files, $notice->title);
            if(false === $result){
                throw new Exception($notice->getError());
            }else{
                $this->success('发布成功', 'notice/index', '', 1);
            }
        }catch(Exception $e){
            $this->error($e->getMessage(), 'notice/deliver', '', 3);
        }
    }

    public function manage()
    {
        $this->check_login_identity();
        // 管理公告页面
        $fullname = session('fullname');
        $job      = session('job');
        $this->assign('page', '管理公告');
        $this->assign('act_manage', 'active');
        // 读取登录用户的所有公告
        $self_notice = Db::name('notice')->where('fullname', $fullname)
            ->where('job', $job)
            ->order('create_at desc')
            ->paginate(5);
        $this->assign('list', $self_notice);
        return $this->fetch();
    }
    
    public function operate(Request $request)
    {
        // 判断对公告进行哪种操作
        $message = $request->param('message');
        $title = explode('^&', $message)[0];
        // 替换标题中的空格, 跳转传参会将空格换成加号
        $title = str_replace(' ', '%20', $title);
        $operate = explode('^&', $message)[1];

        if($operate == '查看'){
            $this->redirect('notice/detail', ['title'=>$title]);
        }
        if($operate == '编辑'){
            $this->redirect('notice/edit', ['title'=>$title]);
        }
        if($operate == '删除'){
            $this->redirect('notice/delete', ['title'=>$title]);
        }
    }

    public function detail($title)
    {
        // 公告详情页面 
        $title = str_replace('%20', ' ', $title);
        //$result = Db::name('notice')->where('title', $title)->find();
        $result = NoticeModel::get(['title'=>$title]);
        // 判断是否有文件
        if($result['extra']){
            $files = FileModel::where('title',$title)->order('file_type desc')->select();
            $this->assign('extra_file', $files);
        }
        $this->assign('msg', $result);
        $this->assign('page', '公告详情');
        return $this->fetch();
    }

    public function edit($title)
    {
        $this->check_login_identity();
        // 编辑公告页面
        $title = str_replace('%20', ' ', $title);
        $result = NoticeModel::get(['title'=>$title]);
        $this->assign('content', $result->content);
        $this->assign('title', $title);
        $this->assign('page', '编辑公告');
        return $this->fetch();
    }

    public function do_edit(Request $request)
    {
        $this->check_login_identity();
        // 更新文件内容到数据库
        try{
            $title = $request->param('title');
            // 替换标题中的空格, 跳转传参会将空格换成加号
            $str_title = str_replace(' ', '%20', $title);
            $files = $request->file('input_file');
            $notice = NoticeModel::get(['title'=>$title]);
            $notice->content = $request->param('content');
            // 判断是否要重新上传文件
            if(count($files) > 0){
                // 删除原来的文件信息及服务端的文件并写入新的文件
                if($notice->extra){
                    $is_delete = $this->delete_file($title);
                }
                $this->add_file($files, $title);
                $notice->extra = 1;
            }

            $result = $notice->save();
            if(false === $result){
                throw new Exception($notice->getError());
            }else{
                $this->success('修改成功', 'notice/manage', '', 1);
            }
        
        }catch(Exception $e){
            $this->error($e->getMessage(), url('notice/edit',['title'=>$str_title]), '', 1);
        }
    }

    public function delete($title)
    {
        $this->check_login_identity();
        // 删除公告及公告附件
        $title = str_replace('%20', ' ', $title);
        // 读取该标题所有信息
        $result = NoticeModel::get(['title'=>$title]);
        // 判断是否有文件
        if($result['extra']){
            $is_delete = $this->delete_file($result['title']);
        }
        $result = NoticeModel::where('title',$title)->delete();
        $this->success('公告删除成功', 'notice/manage', '', 1);
    }

    protected function delete_file($title)
    {
        // 删除公告附件
        $files = FileModel::where('title',$title)->select();
        foreach($files as $file){
            $filename = $file['file_name'];
            $file = ROOT_PATH .'public' . DS . 'uploads' . DS . $filename;
            if(file_exists($file)){
                unlink($file);
            }
        }
        FileModel::where('title',$title)->delete();
        clearstatcache();
    }

    protected function add_file($files, $title)
    {
        // 文件信息列表
        $file_list = array();
        foreach($files as $file){
            $info = $file->move(ROOT_PATH.'public' . DS . 'uploads', '');
            // 判断文件是否图片, 如果是赋值1, 如果不是赋值0
            $type = $this->validate(
                    ['image' => $info], 
                    ['image' => 'require|image']
                    );
            if(true !== $type){
                $file_type = 0; 
            }else{
                $file_type = 1;
            }
            $file_name = $info->getSaveName();
            $file_info = [
                'title'     => $title, 
                'file_name' => $file_name,
                'file_type' => $file_type,
            ];
            $file_list[] = $file_info;
        }
        $file = new FileModel;
        // 将文件信息存储到数据库
        $file->saveAll($file_list);
    }

    protected function check_login_identity()
    {
        // 检查用户是否已登录以及有无权限进行操作
        if(!session('?fullname')){
            $this->error('请先登录', 'user/login', '', 1);        
        }
        if(session('job') == ''){
            $this->error('对不起, 你没有权限', 'notice/index', '', 1);
        }
    }

}
