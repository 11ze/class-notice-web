<?php

namespace app\index\validate;

use think\Validate;
use app\index\model\Notice as NoticeModel;
use app\index\model\File as FileModel;

class Notice extends Validate
{
    protected $rule = [
        'title|标题'  => 'require',
        'uni_title'   => 'check_title',
        'files'       => 'check_file_size',
    ];

    protected $message = [
    
    ];

    protected $scene = [
        'deliver' => ['title', 'uni_title'],
        'edit'    => ['title', ],
    ];

    protected function check_title($title)
    {
        // 检查标题是否已存在
        $result = NoticeModel::get(['title'=>$title]);
        if($result == ''){
            return true;
        }else{
            return '重复了, 换个标题试试';
        }
    }

    protected function check_file_size($files)
    {
        // 检查文件大小是否太大

        foreach($files as $file){
            $file = filesize($file);
            if($file >= 2097152){
                clearstatcache();
                return '文件超过2M';
            }
        }
        clearstatcache();
        return true;
    }
}
