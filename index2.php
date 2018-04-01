<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//注册一个自动加载函数
spl_autoload_register(function($classname){
    //require_once 'db_pdo.php';
    //require_once 'db_mysqli.php';
   // 由类名，查找到类文件 (前提，类名和类文件需要存在关联关系,
   // 即类名和文件名一致)
   // var_dump($classname);
    require_once "libs/{$classname}.php";
});

$configArr = include 'config/config.php';
//var_dump($configArr);
//将配置文件写入全局变量中
foreach($configArr as $item=>$val)
{
    $GLOBALS['XMR_G'][$item] = $val; 
}

require_once 'common/functions.php';

$userModel = D('User');


if(false ==$userModel->create())
{
    E(['msg'=>$userModel->getError()]);
}
$userModel->add();




