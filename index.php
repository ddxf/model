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
//var_dump($GLOBALS['XMR_G']);

//$model = new Model();
//var_dump($model->select('xmr_user','uid > 1','uid desc','0,1'));
//var_dump($model);
//$obj = new Model('pdo', 'localhost', 'root', 'root', 'digua');
//$data = ['uname'=>"i'mzhangsan",'password'=>'123456','nickname'=>'fengzi',
//    'created_at'=>'2018-11-11'];
//$obj ->add('xmr_user',$data);


//$data = ['uname'=>'wangwu','password'=>'cdefg'];
//
//$obj ->save('xmr_user',$data,['uid'=>4,'nickname'=>'fengzi']);

//$obj ->delete('xmr_user',['uid'=>4,'nickname'=>'fengzi']);
//$result = $obj->select('xmr_user','uid > 1','uid desc','0,1');
//$obj->dumpSql();
//var_dump($result);

//$model = new UserModel();
//var_dump($model->select('uid > 1','uid desc','0,1'));
require_once 'common/functions.php';
//$userModel = D('User');  //new UserModel;
//var_dump($userModel->select('uid > 1','uid desc','0,1'));
$userModel = D('User');
//var_dump($userModel->select('uid > 1','uid desc','0,1'));
//var_dump($userModel->field('uname,nickname')
//        ->where('uid >1 ')->order('uid desc')->limit(0,4)->select());
//var_dump($userModel->field('uname,nickname')
//       ->where('uid >1 ')->order('uid desc')->page(2,2)->select());
echo "<pre>";
//var_dump($userModel->order('uid desc')->find());

//echo $userModel->where('uid=8')->getField('nickname');
//var_dump($userModel->field('groupc,AVG(score)')->group('groupc')->having('AVG(score)>90')->select());
//$where['uid'] = array('not in',"5,7");
//$where['uid'] = array("between","5,7");
//$where['score'] = array(">",90);

//$where['_logic'] = 'or';
//var_dump($userModel->where($where)->select());
//$userModel->dumpSql();



//$where['uname']  = array('like', '%ge%');
//$where['nickname']  = array('like','%哥%');
//$where['_logic'] = 'or';
//
//var_dump($userModel->where($where)->select());
//$userModel->dumpSql();
//echo $userModel->count();
//echo $userModel->max('score');
//echo $userModel->avg('score');

//$sql = "SELECT * FROM __USER__ WHERE uid>1";
//var_dump($userModel->query($sql));
//$sql = "UPDATE __USER__ SET nickname='二龙湖浩哥' WHERE uid = 7";
//
//var_dump($userModel->execute($sql));

//var_dump($userModel->getByUname('haoge'));
//$dataArr1 = ['uname'=>'fangge',
//        'nickname'=>'xiaoming',
//        'password'=>'sfds',
//         'groupc'=>'3组',
//         'score'=>80,
//         'created_at'=>'2011-11-07',
//    ];
//
//$dataArr2 = ['uname'=>'zengge',
//        'nickname'=>'bijingfenglaoshi',
//        'password'=>'1234',
//         'groupc'=>'4组',
//         'score'=>90,
//         'created_at'=>'2011-11-07',
//    ];
//$userModel->data($dataArr1)->add($dataArr2);


//$dataArr1 = ['uname'=>'zengge',
//        'nickname'=>'xiaoming',
//        'password'=>'sfds',
//         'groupc'=>'3组',
//         'score'=>80,
//         'created_at'=>'2011-11-07',
//    ];

//$userModel->where('uid = 12')->data($dataArr1)->save();
//$userModel->dumpSql();
$userModel->where('uid = 12')->delete();
$userModel->dumpSql();