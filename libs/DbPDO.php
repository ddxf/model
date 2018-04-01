<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class DbPDO extends PDO
{
    private $sql=[]; //保存当前正在执行的sql 
    public function __construct($host,$username,$passwd,$dbname,$charset="utf8") 
    {
       $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
      try{
           parent::__construct($dsn,$username,$passwd);
      }  catch (PDOException $e)
      {
          exit($e->getMessage());
      }
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
        if(false !== $affected_rows = $this->exec($sql)){
            return $this->lastInsertId()?: $affected_rows;
        }else{
            $this->E();
        }
    }
    /*
     * 执行查询的方法
     * @param string sql  要执行的查询语句
     * @param $mode 查询结果集的形式
     */
    public function query($sql, $mode = PDO::FETCH_ASSOC) {
         
          $this->sql[] = $sql;
      if( false !== $pdo_stmt = parent::query($sql)){
            return $pdo_stmt->fetchAll($mode);
      }else{
          $this->E();
      } 
    }
    /*
     * 用来开启事务
     */
    public function startTransaction()
    {
        
        $this->beginTransaction();
      
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
        echo sprintf($tpl,implode(',',$this->sql),$this->errorInfo()[2]);
    }
    
    
    public function valEsc($val)
    {
        return addslashes($val);
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
       // var_dump($table);
        $sql = "desc {$table}";
       // var_dump($sql);die;
        return $this->query($sql);
    }
}

