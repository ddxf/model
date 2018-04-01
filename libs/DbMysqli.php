<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//db_mysqli mysqli 底层驱动封装
class DbMysqli extends mysqli
{
    private $sql=[]; //保存当前正在执行的sql 
    public function __construct($host,$username,$passwd,$dbname,$charset="utf8") 
    {
        //数据库连接;
       // new mysqli($host, $user, $password, $database, $port, $socket)
       parent::__construct($host,$username,$passwd,$dbname);
       //设置字符集
       $this->set_charset($charset);
    }
    
    /*
     * 执行增删改的方法
     * @param string $sql
     * @return int insert 返回最近插入的行id 
     *             update delete 返回受影响行数 
     */
    public function execute($sql)
    {
        $this->sql[] = $sql;
        if(false !== parent::query($sql)){
            return $this->insert_id?:$this->affected_rows;
        }else{
            $this->E();
        }
    }
    /*
     * 执行查询的方法
     * @param string sql  要执行的查询语句
     * @param $mode 查询结果集的形式
     */
    public function query($sql, $mode = MYSQLI_ASSOC) {
         
          $this->sql[] = $sql;
      if( false !== $mysqli_result = parent::query($sql)){
            return $mysqli_result->fetch_all($mode);
      }else{
          $this->E();
      } 
    }
    /*
     * 用来开启事务
     */
    public function startTransaction()
    {
        if(method_exists($this, "begin_transaction")){
             $this->begin_transaction();
        }else{
               $this->autocommit(false);    
        }
       
      
    }

    public function E()
    {
        //输出sql 语句，$this->sql
        //输出报错信息。
        //定义一个简单的提示模板
        $tpl = "<ul>";
        $tpl .= "<li>sql语句:%s";
        $tpl .= "</li>";
         $tpl .= "<li>报错信息:%s";
        $tpl .= "</li>";       
        $tpl .= "</ul>";
        echo sprintf($tpl,  implode(',',$this->sql),$this->error);
    }
    
    public function valEsc($val)
    {
        return $this->escape_string($val);
    }
    
    public function dumpSql()
    {
        var_dump($this->sql);
    }
    
     /*
     * 读取表信息的方法
     * @param string 表名要读取的表
     * @return array 表信息的集合
     */
    
    public function getTable($table)
    { 
        $sql = "desc {$table}";
        return $this->query($sql);
    }
}
