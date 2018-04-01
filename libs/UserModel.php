<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class UserModel extends Model
{
    //设置模型的表信息
    public $table = "user";//设置表名
    public $tablePrefix = "xmr_";//表前缀
    public $pk = "uid";    //表主键
    //设置验证规则
    public $_validate =[
        //用户名，在注册时必须不为空
        ['uname',"",'账号未填写',self::MUST_VALIDATE,"",self::MODEL_INSERT],
       //用户名，在注册时必须不能重复    
         ['uname',"",'账号已经被注册',self::MUST_VALIDATE,"unique",self::MODEL_INSERT],
        //密码在注册时候必须验证长度，6-15
         ['password',"6,15",'密码应为6-15位',self::MUST_VALIDATE,"length",self::MODEL_INSERT],
          //密码在编辑时候有值则验证
         ['password',"6,15",'密码应为6-15位',self::VALUE_VALIDATE,"length",self::MODEL_UPDATE],
        //注册或者编辑时，密码和确认密码需保持一致
         ['repassword',"password",'两次输入密码不一致',self::MUST_VALIDATE,"confirm",self::MODEL_BOTH],
        //昵称不能为空
        ['nickname','',"昵称未填写"]
    ];
    
    //自动完成规则设置
    public $_auto =[
       //设置created_at 字段
        ['created_at','date',self::MODEL_INSERT,'function',['Y-m-d H:i:s']],
          //设置password 字段
        ['password','md5',self::MODEL_INSERT,'function'],
        //填充固定值
        ['score','90',self::MODEL_INSERT,'string'],
          //填充其他字段的值
         ['groupc','nickname',self::MODEL_INSERT,'field'],
    ];
    
    
}
