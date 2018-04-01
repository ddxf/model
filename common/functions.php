<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//实例化数据表模型对象 //本质上是实例化类，但传入的是表名
function D($table)
{
    //由表名找到类名 类名 = 表名+Model;
    $classname = $table."Model";
    return new $classname;
    
}

function M($table=null)
{
    //实例化model
    $model = new Model();
    //动态的写入表名
    if(!empty($table)){
         $model->trueName = $GLOBALS['XMR_G']['db']['tablePrefix'].$table;
    }
    return $model;  
}

/*
 * 输出错误提示的方法
 * @param string $msg 错误信息
 * @param bool $stoped
 */
//$msg 错误信息
//time 可选 几秒跳转
//url 跳转地址
//$arr = ['msg'=>$msg,‘time’=>$time,'url'=>$url]
function E($arr , $stoped = true)
{
    //提取数据
    extract($arr);
    //默认3秒跳转
    !isset($time) && $time =3;
     //默认跳转地址  默认跳转上一个页面
     !isset($url) && $url = @$_SERVER['HTTP_REFERER'];
    //载入模板显示，
    include 'tpl/system.html';
    $stoped && exit();
}