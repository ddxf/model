<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once 'db_pdo.php';
//require_once 'db_mysqli.php';

class Model
{
    //保存产生数据库的驱动对象
    private static $db;
    public $trueName; //保存表名
    //
    private $error;//专业放错误信息(私有)
    //
    public $data;
    //成员属性 保存查询查询 sql 的各个部分
     private $opt =[
         "FIELD" => '*',//查询字段 
         'WHERE' => '',//查询条件
         'ORDER' =>'',//排序条件
         'LIMIT'=>'', //限制 条数
         'GROUP'=>'',//分组 
         'HAVING'=>'',//分组子查询
     ];   
     //设置验证时间的常量
     const MODEL_INSERT = 1;//新增的时候验证
     const MODEL_UPDATE = 2;//编辑的时候验证
     const MODEL_BOTH = 3;//两者都验证
     
      //设置验证条件的常量
     const EXIST_VALIDATE = 0 ;//存在就验证
     const MUST_VALIDATE = 1 ;//必须验证
     const VALUE_VALIDATE = 2 ;//有值验证
     
     
     
     
//数据路连接 产生操作数据库的驱动对象
     //$type 传入一下驱动的类型，PDO驱动，mysqli 驱动(传入字符串,mysqli ,PDO)
    public function __construct() {
       //判断驱动的类型 ;
        if(!self::$db){
            switch ($GLOBALS['XMR_G']['db']['type']) {
                case 'mysqli': self::$db = new DbMysqli($GLOBALS['XMR_G']['db']['host'],
                                                        $GLOBALS['XMR_G']['db']['username'], 
                                                          $GLOBALS['XMR_G']['db']['passwd'], 
                                                         $GLOBALS['XMR_G']['db']['dbname'],
                                                         $GLOBALS['XMR_G']['db']['charset']);
                    break;
                case 'pdo':self::$db =  new DbPDO($GLOBALS['XMR_G']['db']['host'],
                                                    $GLOBALS['XMR_G']['db']['username'], 
                                                    $GLOBALS['XMR_G']['db']['passwd'], 
                                                    $GLOBALS['XMR_G']['db']['dbname'],
                                                    $GLOBALS['XMR_G']['db']['charset']);
                    break;
                case 'oracle':
                    break;
                case 'sqlite':
                    break;
                default:
                    break;
             } 
        }
        
        //初识化表名
        if(isset($this->table)){
            $this->trueName = $this->tablePrefix.$this->table;
        }
    }
    /*
     * 添加一条记录的方法
     * @param string $table  要操作的表名
     * @param array  $data  入库的数据
     */
    
    //$data=['uname'=>'zhangsan','password'=>'123456'.....];
    public function add($data=null)
    {    
        //如果add 有自己的数据就使用add 的数据
        //如果没有的话使用全局数据
        !empty($data)  && $this->data = $data;
//        $sql = "INSERT INTO xmr_user (id,uname,password,nickname,created_at) VALUES (null,"
//                . "zhangsan','123456','xiaofengzi','2018-11-11') ";
         $sql = "INSERT INTO `{$this->trueName}` (%FIELDS%) VALUES (%VALS%) ";
        //重组数据值，满足以上sql 的 FIELDS部分， VAL部分；
         $fields = [];//存放所有的键
         $vals = [];//存放所有的值
         foreach($this->data as $field => $val)
         {
             //调用驱动里面的方法，对要入库的数据进行转义
             $val =  self::$db->valEsc($val);//
             array_push($fields, $field);
             array_push($vals, "'".$val."'");
         }
         $fieldsStr = implode(',', $fields);
         $valsStr = implode(',', $vals);
         //替换模板中的占位符
         $sql =str_replace(['%FIELDS%','%VALS%'], [$fieldsStr,$valsStr], $sql);
         return self::$db->execute($sql);
    }
    
    /*修改记录的方法
     * @param string 表名
     * @param array $data  新的数据
     * @param where  条件 可以为null 
     * @return  int 修改的行数
     */
    public function save($data=null)
    {
          
        //如果save 有自己的数据就使用save 的数据
        //如果没有的话使用全局数据
        !empty($data)  && $this->data = $data;
        //写sql 结构
        $sql =  "UPDATE `{$this->trueName}` SET %DATA% %WHERE%";
        //重组数据值
       //['uname'=>'lisi','password'=>'abcde']
        
       // ["uname = 'lisi'","password='abcde'"]
        
       // uname = 'lisi',password='abcde'
        $sets = [];
        foreach ($this->data as $field => $value) {
            $tmpStr = $field . "='". self::$db->valEsc($value)."'";
            array_push($sets, $tmpStr);
        }
        $setsStr = implode(',', $sets);
        
//        //重置where 条件
//        if(!empty($where)){
//            if(is_string($where)){
//                $condition = "WHERE ".$where;
//            }else if (is_array($where)){
//                $condition = [];
//                 foreach ($where  as $field => $value) {
//                        $tmpStr = $field . "='". self::$db->valEsc($value)."'";
//                        array_push($condition, $tmpStr);
//                 }
//                $condition = "WHERE ".implode(' AND ',$condition);
//            }  
//        }
        //替换掉模板中的占位符：
        $sql = str_replace(["%DATA%","%WHERE%"] ,[$setsStr,$this->opt['WHERE']] ,$sql);
        
         //重新初始化sql 选项
        $this->initOpt();
        
        return self::$db->execute($sql);
    }
    
    public function delete($id=null)
    {
        $sql ="DELETE FROM `{$this->trueName}` %where%";

        //如果传入了id值 ，就是表示自定义条件，覆盖之前的where中的条件。
        if(is_int($id) || is_string($id)){
            $this->where([$this->pk=>$id]);
        }
        //替换模板中的占位符
        $sql =  str_replace("%where%",$this->opt['WHERE'],$sql);
        //重新初始化sql 选项
        $this->initOpt();
       //
         return self::$db->execute($sql);
    }
  
      /*查询记录的方法
      *
     * @return  array assoc 数组
     */
    public function select()
    {
        $sql = "SELECT %field% FROM `{$this->trueName}` %where% %group% %having% %order %limit%";
        //重组数据值
        $sql = str_replace(["%field%","%where%", "%order", "%limit%" ,"%group%", "%having%"], 
                            $this->opt, $sql);
        //var_dump($sql);
        //执行有效sql
        $this->initOpt();
        return self::$db->query($sql); 
    }
    
    //调试sql 语句的方法
    public function dumpSql()
    {
        //调用驱动中的方法
        self::$db->dumpSql();
    }
    
    /*
     * 查询字段的设置
     * @param string $field
     * @return  Model对象
     * 
     */
    public function field($field=null)
    {
        if(!empty($field))
        {
          $this->opt['FIELD']  = $field;
        }
        return $this;
    }
    
    /*
     * 设置where 部分
     * @param mixed $where  条件组装
     * @return \Model 当前模型对象
     */
    public function where($where=null)
    {
        
        
        //重置where 条件
        if(!empty($where)){
            if(is_string($where)){
                $this->opt['WHERE'] = "WHERE ".$where;
            }else if (is_array($where)){
                //$map['uid']  = array('BETWEEN','1,8'); id BETWEEN 1 AND 
                // $map['uid']  = array('not in','1,5,8'); uid not in (1,5, 8)
                //$map['uid']  = array('in','1,5,8');uid  in (1,5, 8)
                
//                $where['uname']  = array('like', '%ge%');
//                $where['nickname']  = array('like','%哥%');
//                $where['_logic'] = 'or';
                
                $condition = [];
                $combine= "AND";
                 foreach ($where  as $field => $value) {
                     //判断一下是否有连接条件
                     if($field === '_logic'){
                         $combine= $value;
                         continue;
                     }
                    if(is_array($value)) 
                    {
                        //根据value 的值 提取出表达式 和具体规则 
                        list($exp, $rule) = $value;
                        
                        switch (strtolower($exp)){
                            case "in":
                            case "not in":
                                //in 和not in 规则
                                $tmpStr = $field ." ".$exp." "."($rule)";
                                 array_push($condition,$tmpStr);
                                break;
                            case "between":
                                //between 规格
                                list($start,$end)= explode(',',$rule);
                                $tmpStr = $field ." BETWEEN {$start} AND {$end}";
                                array_push($condition,$tmpStr);
                                break;
                            
                            //$where['uid'] = array(">",6);
                            default :
                                //默认规则中都是(字段 表达式 具体规则)
                                $tmpStr = $field ." {$exp} '".self::$db->valEsc($rule)."'";
                                array_push($condition,$tmpStr);
                                break;
                        }
                        
                    }else{
                        $tmpStr = $field . "='". self::$db->valEsc($value)."'";
                        array_push($condition, $tmpStr);
                    }
                        
                 }
                 $this->opt['WHERE']  = "WHERE ".implode(" {$combine} ",$condition);
            }  
        }
        //返回当前对象，保证连贯操作能执行下去
        return $this;
    }
    
    /*
     * 设置排序规则
     * @param string $order 排序
     * @return  model //返回当前对象
     */
    public  function order($order=null)
    {
        if(!empty($order))
        {
            $this->opt['ORDER']= " ORDER BY ".$order;
        }
        return $this;
    }
    
    
    /*
     * 设置限制条数
     * @param string $start 起始参数  '0,4'
     * @param string $length 查询长度
     * @return  model //返回当前对象   
     */
    public  function limit($start=null,$length = null)
    {
        if($start !== null)
        {
            //如果没有第二个参数
            if(empty($length)){
               list($start,$length) = explode(',', $start);
            }
            //如果有第二个参数
            $this->opt['LIMIT']= " LIMIT {$start},{$length}";   
        }
        return $this;
        
//       if($start !== null)
//       {
//            //如果有第二参数
//            if($length !== null)
//            {
//                $this->opt['LIMIT'] =   " LIMIT ".$start.",".$length; 
//            }else{
//                //如果没有第二个参数 
//                $this->opt['LIMIT']= " LIMIT ".$start;
//            }   
//       }
      
    }
    /*
     * 分页查询-数据的获取
     * @param int $pid  页码
     * @param int $length 每页长度
     * @return  当前模型对象
     */
    
    public function  page($pid, $length)
    {
        $start = ($pid - 1)*$length;
        return   $this->limit($start, $length);     
    }
    //查询一条记录的方法
    public function find()
    {
         //特殊的select limit 为1 
        $rows = $this->limit(0, 1)->select();//返回二维数组
        if(!empty($rows))
        {
             return $rows[0];
        } else {
            return [];
        }
    }
    
    /*
     * 读取字段的值，返回字符串
     * @param $filed
     * @return string
     */
    public function getField($field)
    {
        $row = $this->field($field)->find();//此处返回的是一维数组
        if(!empty($row))
        {
             return $row[$field];
        } else {
            return "";
        }
       
    }
    public function group($group=null)
    {
        if(!empty($group))
        {
            $this->opt['GROUP'] = " GROUP BY " .$group;
        }
        return $this;
    }
    
    public function having($having=null)
    {
          
        if(!empty($having))
        {
            $this->opt['HAVING'] = " HAVING " .$having;
        }
        return $this;
    }
    /*
     * 查询某个字段（为空的值不算）的结果集总数
     */
    
    public function count($field="*")
    {
        //select count($field) from 表名
        return $this->getField("count({$field})");
        
    }
    
    /*
     * 查询某个字段的 最大值
     */
    
    public function max($field="*")
    {
       
        return $this->getField("MAX({$field})");
        
    }
    
     /*
     * 查询某个字段的 最小值
     */
    
    public function min($field="*")
    {
        
        return $this->getField("MIN({$field})");
        
    }
    
     /*
     * 求字段的和
     */
    
    public function sum($field="*")
    {
      
        return $this->getField("SUM({$field})");
        
    }
    
     /*
     * 求字段的平均数
     */
    
    public function avg($field="*")
    {
        return $this->getField("AVG({$field})");
        
    }
    
    //SELECT * FROM __USER__ WHERE uid>9 
    //select * from xmr_user where uid >9  
    public function query($sql)
    {
        $prefix =   isset($this->tablePrefix)?$this->tablePrefix:$GLOBALS['XMR_G']['db']['tablePrefix'];
        //var_dump($prefix);
        //正则替换 表名
        $sql = preg_replace_callback("/__(\w+)__/", function($matches) use($prefix){
          
            //var_dump($matches[1]);
            return $prefix.strtolower($matches[1]);
        }, $sql);
        
       // var_dump($sql);
        
        return  self::$db->query($sql);
    }
    /*
     * 增删改sql的执行，传入sql 的表名需要强化
     * @param string $sql  UPDATE __USER__ SET ... WHERE...
     * @return  int  操作影响的行数，或者新增 的行id
     */
     public function execute($sql)
    {
        $prefix = isset($this->tablePrefix)?$this->tablePrefix:$GLOBALS['XMR_G']['db']['tablePrefix'];
        //var_dump($prefix);
        //正则替换 表名
        $sql = preg_replace_callback("/__(\w+)__/", function($matches) use($prefix){
          
            //var_dump($matches[1]);
            return $prefix.strtolower($matches[1]);
        }, $sql);
        return  self::$db->execute($sql);
    }
    
    
    /*
     * 动态查询
     */
    public function __call($name, $args) 
    {
       if(preg_match("/^getBy(\w+)/", $name,$matches))
       {
          $field =  strtolower($matches['1']);
       }
      return $this->where([$field=>$args['0']])->find() ;
      
    }
   /*
    * @param array $data
    * @return model
    */
    
    public function data($data=null)
    {
        if(!empty($data))
        {
            //将数据写入成员属性中
            $this->data = $data;
        }
        //返回当前模型对象
        return $this;
        
    }
    
    
    public function create($data=null,$status=null)
    {
        //①获取数据源（默认是$_POST）
        
        empty($data) && $data = $_POST;
        if(empty($data))
        {
            $this->error = "数据不能为空";
            return false;
        }
        //②验证数据的合法性（不是数组或者对象报错）
        if(!is_array($data)  && !is_object($data))
        {
            $this->error = "非法数据对象";
            return false;
        }
        
        //检查字段映射
          
//判断数据状态（新增或者编辑，指定或者自动判断）  
        if(empty($status)){
            //通过数据中是否含有主键 自动判断
            $status = isset($data[$this->pk])?self::MODEL_UPDATE:self::MODEL_INSERT;
        }
        //数据自动验证 失败则返回false 
        if(is_array($this->_validate))
        {
            $obj = new Validate;
            if(true !== $result = $obj->_input($data,$status,$this))
            {
               // 发生了错误
                $this->error = $result;
                //返回false ,是 create 的执行结果
                return false;
            }
            
        }

// 表单令牌验证 失败则返回false 

// 表单数据赋值（过滤非法字段和字符串处理）  

        $data = $this->keyMap($data);    
        
// 数据自动完成  
// 
 
        if(is_array($this->_auto))
        {
            //var_dump(111);die;
            $obj = new AutoComp();
            $data = $obj->_input($data,$status,$this);
 
        }
//生成数据对象（保存在内存） 
        
        //var_dump($data);die;
        $this->data = $data;
        return true;
    }
    
    public function keyMap($data)
    {
        //$data = ['uname'=>'zhangsan',
        //          'nickname'=>'fengzi',
        //          'password'=>'123',
        //          'repassword'=>'123'
        //]
        //数据中的字段
        //['uid'=>'uid'
        //'uname'=>'uname'
        //'password'=>'password'
        //'nickname'=>'nickname'
        //.....
        //]
        //将data 中的数据，和数据库中的字段作对比，提取出有效的字段
       // var_dump($this->trueName);die;
        $rows = self::$db->getTable($this->trueName);
        //定义一个数组，存放表中的字段
        $cols = [];
        foreach ($rows as  $row) {
            $cols[$row['Field']] = $row['Field'];
        }
        // 将data 数组索引在cols索引中的元素部分提取出来
      return array_intersect_key($data, $cols);  
    }
    //读取错误信息
    public function getError()
    {
        return $this->error;
    }
    
    
    public function __get($name) {
       // 如果data 中有字段的值，则返回，没有报错 
        if(isset($this->data[$name])){
            return $this->data[$name];
        }else{
            E('没有'.$name.'值');
        }
    }
    /*
     * 给未定义的属性赋值，调用魔术方法 将值存入到data 中
     */
    public function __set($name, $value) {
       $this->data[$name] = $value;
    }
    //快速设置一个字段的值
    public function setField($field,$value=null)
    {
        //把field 和value 组成一个新的数组，调用data方法放入全局属性中，
        //然后再执行save操作
        $data = [];
        if(!empty($value))
        {
            $data[$field] = $value;
        }else{
            $data= $field;
        }
        
        $this->data($data)->save();
    }

    
    public function initOpt()
    {
        $this->opt =[
                    "FIELD" => '*',//查询字段 
                    'WHERE' => '',//查询条件
                    'ORDER' =>'',//排序条件
                    'LIMIT'=>'', //限制 条数
                    //  'GROUP'=>'',//分组 
                    //  'HAVING'=>'',//分组子查询
            
        ];
    }
}



