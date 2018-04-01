<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 * 自动验证类
 */
class Validate 
{
    
    public function _input($data,$status,$model)
    {
        //遍历验证规则，跟data数据对比，进行验证
        foreach ($model->_validate as  $item) {
            //读取规则中的信息，
            //字段，详细规则，错误提示，验证条件，附加规则，验证时间
            @list($field,$rule,$msg,$exists,$method,$timing)= $item;
            //对于没有的选项，设置默认信息
            //设置默认条件
            empty($exists)  && $exists = 1; //必须验证
            //设置附加规则
            empty($method)  && $method = "notNull";//不为空
            //默认的条件
            empty($timing)  && $timing = 3;//新增和编辑时都验证
          
            //不符合当前验证条件和验证时间的跳过
            //存在字段就验证  但是字段不存在 跳过
            if( $exists==0 && !isset($data[$field])) continue;
            //值不为空的时候验证   但是值为空  跳过
             if( $exists==2 && empty($data[$field])) continue;
             //如果不是新增或者编辑的时候都验证， 
             //validate 指定的验证时间，和实际的验证时间不一致 跳过
             if($timing !=3 && $status != $timing)       continue;
             
             //开始执行各项验证
             switch ($method){
                 //值是否为空
                 case "notNull":
                     $result = !empty($data[$field]);
                     break;
                 //值是否满足长度要求
                case "length":
                     $result = $this->length($data[$field],$rule);
                     break;
                //值是否唯一
                case "unique":
                    $result = $this->isUnique($field,$data[$field],$model);
                    break;
                
                //一个字段的值是否跟另外一个字段的值一致
                case "confirm":
                    $result = $data[$field] == $data[$rule];
                    break;
                //一个字段的值是否等于固定值
                 case "equal":
                     $result = $data[$field] == $rule;
                    break;
                
                //一个字段的值是否是手机格式
                case "phone":
                     $result = boolval(preg_match("/^1[35678]\d{9}$/",$data[$field]));
                    break;
                //一个字段的值是否是数字
                case "number":
                      $result =  is_numeric($data[$field]);
                    break;
                //使用函数验证：
                 case "function":
                      $result =  $rule($data[$field]);
                      break;
                  //使用方法验证： 
                   case "callback":
                      $result =  $model->$rule($data[$field]);
                      break;
                     
             }
            // 如果result 为false 表示验证没通过
             if($result ==false)  return $msg;
             
             
        }
        
        return true;
    }
    /*
     * 验证长度是否符合要求
     * @param $v 要验证的值
     * @param $rule 要验证的具体规则 “6,15”或数组 [6,15]
     * @return  bool 验证通过，返回true,不通过返回false;
     */
    public function length($v,$rule) 
    {
        if(is_string($rule)){
            $rule = explode(',',$rule);     
        }
        //提取出最小值，最大值
        list($min,$max) = $rule;
        //判断$v的长度是否符合要求
        if(strlen($v)<$min || strlen($v)>$max)
        {
            return false;
        } 
        return true;
       // if(is_array($rule))
    }
    /*
     * 验证值是否唯一
     * @param string $field  要验证的字段
     * @param string $val 要验证的值
     * @param object  $model 数据表模型
     * @return boolean 验证结果，不唯一返回false 唯一返回true;
     */
    public function isUnique($field,$val,$model)
    {
       $res =  $model->where([$field=>$val])->find();
       if(!empty($res))
       {
         return false;  
       }
        return true; 
    }
}
