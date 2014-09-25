<?php

/**
 * @Author: 杰少Pakey
 * @Email : admin@ptcms.com
 * @File  : plugin.php
 */
class plugin
{
    public $tag='';
    public static $_tags=array();

    /**
     * 调用插件
     * @param $tag
     * @param null $param
     */
    public static function call($tag,&$param=null){
        if(isset(self::$_tags[$tag])) {
            foreach (self::$_tags[$tag] as $name) {
                $classname=$name.'Plugin';
                $handler=new $classname();
                $handler->run($param);
            }
        }
    }

    /**
     * 注册插件方法
     * @param array $data
     */
    public static function register(array $data)
    {
        foreach($data as $tag=>$var){
            self::add($tag,$var);
        }
    }

    /**
     * 添加插件方法
     * @param $tag
     * @param $var
     */
    public static function add($tag, $var)
    {
        if (!is_array($var)) $var=array($var);
        if (isset(self::$_tags[$tag])){
            self::$_tags[$tag]=array_unique(array_merge(self::$_tags[$tag],$var));
        }else{
            self::$_tags[$tag]=$var;
        }
    }

    /**
     * 删除插件方法
     * @param $tag
     * @param $var
     */
    public static function del($tag, $var)
    {
        if (isset(self::$_tags[$tag])){
            $key=array_search($var,self::$_tags[$tag]);
            if ($key!==false){
                unset(self::$_tags[$tag][$key]);
            }
            if (empty(self::$_tags[$tag])) unset(self::$_tags[$tag]);
        }
    }

    /**
     * 获取插件列表
     * @param string $tag
     * @return array
     */
    public static function get($tag='')
    {
        if (empty($tag)) return self::$_tags;
        if (isset(self::$_tags[$tag])){
            return self::$_tags[$tag];
        }else{
            return array();
        }
    }

    public static function getlist()
    {
        $list=array();
        foreach(self::$_tags as $v){
            $list=array_merge($list,$v);
        }
        return array_unique($list);
    }

    /**
     * 安装插件
     */
    public function install()
    {
        $name=substr(get_class($this),0,-6);
        if ($this->checkstatus($name)){
            return 0;
        }elseif($this->tag==''){
            return -1;
        }
        self::add($this->tag,$name);
        $config=include APP_PATH.'/common/config.php';
        $config['plugin']=self::$_tags;
        F(APP_PATH.'/common/config.php',$config);
        return 1;
    }

    // 卸载插件
    public function uninstall()
    {
        $name=substr(get_class($this),0,-6);
        if (!$this->checkstatus($name)){
            return 0;
        }elseif($this->tag==''){
            return -1;
        }
        self::del($this->tag,substr(get_class($this),0,-6));
        $config=include APP_PATH.'/common/config.php';
        $config['plugin']=self::$_tags;
        F(APP_PATH.'/common/config.php',$config);
        return 1;
    }

    // 检查插件是否被安装
    public function checkstatus($plugin)
    {
        if (substr($plugin,-4)=='.php') $plugin=substr($plugin,0,-4);
        foreach(self::$_tags as $tag){
            if (in_array($plugin,$tag)) return true;
        }
        return false;
    }

    // 返回插件的配置项
    public function getconfig($key)
    {
        $name=substr(get_class($this),0,-6);
        return C('plugin_config.'.$name.'.'.$key);
    }
}