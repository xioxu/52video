<?php
class loginMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    // 登录页面
    public function index()
    {
        $this->display();
    }

    //登陆检测
    public function check(){
        if(empty($_POST['user'])||empty($_POST['password'])){
            $this->msg('帐号信息输入错误!',0);
        }
        //获取帐号信息
        $_POST['user'] = addslashes($_POST['user']); 
		$_POST['password'] = addslashes($_POST['password']); 
		
		
		$info=model('login')->user_info($_POST['user']);
        //进行帐号验证
        if(empty($info)){
            $this->msg('登录失败! 无此管理员帐号!',0);
        }
        if($info['password']<>md5($_POST['password'])){
            $this->msg('登录失败! 密码错误!',0);
        }
        if($info['status']==0){
            $this->msg('登录失败! 帐号已禁用!',0);
        }
		
        //更新帐号信息
        $data['logintime']=time();
        $data['ip']=get_client_ip();
        $data['loginnum']=intval($info['loginnum'])+1;
        model('login')->edit($data,intval($info['id']));
        //更新登录记录
        model('log')->login_log($info);
        //设置登录信息
       //设置登录信息
        $cookie=$info['id'].'|'.sha1($info['user']).'|'.sha1($info['password']);
        if($_POST['remember']){
        	$expire = time() + 604800;
        }else{
        	$expire = time() + 7200;
        }
        setcookie($this->config['SPOT'].'_adminuser',$cookie,$expire,'/');
       $user= model('user')->current_user(false);
		if($user['grade']==3){
           $this->msg('您没有权限登陆！',0);
        }
        $this->msg('登录成功!',1);
        
    }

    //退出
     public function logout(){
        if(empty($_POST)){
            $this->redirect(__APP__);
        }
        model('user')->current_user(true,true);
        unset($_SESSION[$this->config['SPOT'].'_user']);
		 setcookie($this->config['SPOT'].'_adminuser','',-1,'/');
        $this->msg('退出成功! ',0);
     }



}

?>