<?php
//插件公共类
class common_pluginMod extends commonMod
{
    private $_data = array();

    public function __construct()
    {
        parent::__construct();
        define('__PLUGINDIR__', __ROOTDIR__. '/plugins/'.$_GET['_module']);
        define('__PLUGINTPL__', __ROOTURL__. '/plugins/'.$_GET['_module'].'/template');
    }

    public function __get($name){
        return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
    }

    public function __set($name, $value){
        $this->_data[$name] = $value;
    }

    //包含内模板显示
    protected function show($tpl = ''){
        module('common')->view()->assign( $this->_data );
        $content=module('common')->display(__ROOTDIR__. '/plugins/'.$_GET['_module'].'/template/'.$tpl,true,true,true);
        $body=module('common')->display('index/common',true,true);
        $html=str_replace('<!--body-->', $content, $body);
        echo $html;
    }

    //模板输出定义
    protected function display($tpl = '', $return = false, $is_tpl = true ,$diy_tpl=false){
        module('common')->view()->assign( $this->_data );
        module('common')->display(__ROOTDIR__. '/plugins/'.$_GET['_module'].'/template/'.$tpl,false,true,true);
    }

    //获取插件信息
    protected function plugin_config(){
        $url = __ROOTDIR__ . '/plugins/' . $_GET['_module']. '/config.xml';
        $config = Xml::decode(file_get_contents($url));
        return $config;
    }



}
?>