<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//自动完成类
class AutoComp
{
    //根据设置的完成规则， 对原有的数据进行重组，返回完成的数据
    /*
     * @param $data   原始数据
     * @param $status       新增或编辑
     * @param $model        当前模型对象
     * @return $data  重组后的新数据
     * 
     */
    
    public function  _input($data,$status,$model)
    {
          //遍历验证规则，跟data数据对比，进行验证
        foreach ($model->_auto  as  $item) {
            //读取规则中的信息，
            //字段，详细规则，验证时间，附加规则， 参数
            @list($field,$rule,$timing,$method,$args)= $item;
            //对于没有的选项，设置默认信息
            //设置默认条件
             //默认的条件
            empty($timing)  && $timing = 3;//新增和编辑时都完成
            //设置附加规则
            empty($method)  && $method = "function";//默认是函数
              //设置参数
            if($method=="function" || $method=="callback")
            empty($args)  && @$args = [$data[$field]];//默认是传进来的字段值
            
             //不符合当前验证时间的跳过
            
            if($timing !=3 && $status != $timing)       continue;
              //开始执行各项完成设置
             switch ($method){
             //填充固定值
                 case "string":
                     $data[$field] = $rule;
                 break;
             //填充字段值
                 case "field":
                     $data[$field] = $data[$rule];
                 break;
//             //填充函数完成
//                 case "function":
//                    $argStr = $this->parseArgs($args) ;
//                     $data[$field] = $rule($argStr);
//                 break;
//             //使用方法完成
//                 case "callback":
//                    $argStr = $this->parseArgs($args) ;
//                     $data[$field] = $model->$rule($argStr);
//                 break;
             
                //填充函数完成
                 case "function":
                    $argStr = $this->parseArgs($args) ;
                     $evalStr = "\$data[\$field] = \$rule($argStr);";
                     eval($evalStr);
                 break;
             //使用方法完成
                 case "callback":
                    $argStr = $this->parseArgs($args) ;
                    eval("\$data[\$field] = \$model->\$rule($argStr);");
                 break;
             }
        } 
        return $data;
    }
    
    
    public function parseArgs($args)
    {
        //$args =  ['a','b','c'];         
        $argStr = '';
        $join = '';
       for($i=0;$i<count($args);$i++){
           $argStr .= $join . "\$args[$i]";
            $join = ",";
       }
       return $argStr;
    }
    
}
