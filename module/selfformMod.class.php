<?php
class selfformMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
		if($_GET["_action"]!='showpc'&&$_GET["_action"]!='geterweima'&&$_GET["_action"]!='showdata'&&$_GET["_action"]!='getshowdata')
		$this->getuserinfo();
    }
	public function geterweima(){
		$id=in($_POST['id']);
    	if(empty($id)){
    		$this->error404();
    	}
		$time=time()+5;
		 $url = "http://".$this->config['MOBILE_DOMAIN']."/selfform/index/fid-".$id."?time=".$time."&token=".$_GET['token'];
          model('login')->getQrcode($url, 'selfform_'.$id.'_'.$time);
           $img = "/upload/aidimage/selfform_".$id.'_'.$time.".png";
		   $this->msg($img,1);
		}
	public function showpc(){
		$id=in($_GET['id']);
    	if(empty($id)){
    		$this->error404();
    	}
		$this->info=$info=model('selfform')->info($id);
	
    	if(empty($info)){
    		$this->error404();
    	}
		
		$this->display('selfform_showpc.html');
		}
	public function showdata(){
		$id=in($_GET['id']);
    	if(empty($id)){
    		$this->error404();
    	}
		$this->info=$info=model('selfform')->info($id);
	
    	if(empty($info)){
    		$this->error404();
    	}
		$this->formlist=$formlist=model('selfform')->inputs_list(array('fid'=>$id));	
		
		$this->display('selfform_showdata.html');
		}
	public function getshowdata(){
		$id=in($_POST['id']);
    	if(empty($id)){
    		$this->error404();
    	}
			$where=' fid ='.$id;
		$list=model('selfform')->form_value_list($where);
			
    	if($list){
			
		 $this->formlist=$formlist=model('selfform')->inputs_list(array('fid'=>$id));	
			
		$data=array();
		foreach($list as $key=>$value){
			
			$values=unserialize($value['values']);
		
			foreach($formlist as $k=>$v){
				if(($v['type']==6||$v['type']==8||$v['type']==9)){
					if($values[$v['field']]){
					$data[$v['field']][$values[$v['field']]]+=1;
					}
				}
				
				}
		}
		foreach($formlist as $k=>$v){
			if(($v['type']==6||$v['type']==8||$v['type']==9)){
				
				
				  $list=explode("\n",html_out($v['options']));
                foreach ($list as $keyo) {
                    $value=explode('|',$keyo);
                  
			$temp[$v['field']][$value[1]]=(object)array('name'=>$value[0],'data'=>array(0));		
                }
				
				foreach($data[$v['field']] as $key=>$val){
					
			$temp[$v['field']][$key]=(object)array('name'=>model('expand_model')->get_list_model($v['type'],$key,$v['options']),'data'=>array($val));
				}
			}
			}
			
	$this->msg((array)$temp,1);
			}	
		
		
		}
    public function index()
    {  	
	 $fid=in($_GET['fid']);
    	if(empty($fid)){
    		$this->error404();
    	}
	
    	$this->info=$info=model('selfform')->info($fid);
	
    	if(empty($info)){
    		$this->error404();
    	}
		if($info['dynamic']&&$info['interval']){
			$time=$_GET['time'];
			if($time<time()-$info['interval']){
				$this->msg('二维码已失效，请重新扫码！',0);
				}
			
			}
		
		
			//获取所有字段
    	$this->field_list=$field_list=model('selfform')->form_inputs_list(array('fid'=>$info['id']));
    	if(empty($field_list)){
            
                $this->msg('未发现表单信息！',0);
           
        }
	
	$signinfo=model('selfform')->input_value(array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid']));
	if($signinfo){
		
		$signinfo['values']=unserialize($signinfo['values']);
		$this->signinfo=$signinfo;
	}
	
	$this->display('selfform_index.html');
	}
	//提交表单
    public function post()
    {
    	$fid=in($_POST['fid']);
    	if(empty($fid)){
    		$this->error404();
    	}
	
    	$info=model('selfform')->info($fid);
	
    	if(empty($info)){
    		$this->error404();
    	}
    	//获取所有字段
    	$field_list=model('selfform')->form_inputs_list(array('fid'=>$info['id']));
    	if(empty($field_list)){
            
                $this->msg('未发现表单信息！',0);
           
        }

        $data=array();
        foreach ($field_list as $value) {
            if($value['must']==1){
                if(empty($_POST[$value['field']])){
                    
                        $this->msg($value['name'].'不能为空！',0);
                   
                }
            }
            $data[$value['field']]=model('expand_model')->field_in($_POST[$value['field']],$value['type'],$value['field']);
            if(!isset($_POST[$value['field']])){
                $data[$value['field']]=$value['default'];
            }
        }

     $valuedata=array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid'],
				  'aid'=>$_POST['aid'],
				  'values'=>serialize($data),
				  'time'=>time());
        //过滤完后提交表单
        model('selfform')->add_value($valuedata);
    
      
       
         $this->msg('提交成功');
       
        
    }
	
		//提交表单
    public function only_post()
    {
    	$fid=in($_POST['fid']);
    	if(empty($fid)){
    		 $this->msg('未发现表单信息！',0);
    	}
		
		if($_POST['code']&&$_POST['mobile']){
	if($_COOKIE['mobilecode']&&$_POST['code']!=$_COOKIE['mobilecode']){
			
			 $this->msg('手机验证码不对！',0);
			}
		}
    	$info=model('selfform')->info($fid);
	
    	if(empty($info)){
    	 $this->msg('未发现表单信息！',0);
    	}
    	//获取所有字段
    	$field_list=model('selfform')->form_inputs_list(array('fid'=>$info['id']));
    	if(empty($field_list)){
            
                $this->msg('未发现表单信息！',0);
           
        }

        $data=array();
        foreach ($field_list as $value) {
            if($value['must']==1){
                if(empty($_POST[$value['field']])){
                    
                        $this->msg($value['name'].'不能为空！',0);
                   
                }
            }
            $data[$value['field']]=model('expand_model')->field_in($_POST[$value['field']],$value['type'],$value['field']);
            if(!isset($_POST[$value['field']])){
                $data[$value['field']]=$value['default'];
            }
        }
	if(model('selfform')->input_value(array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid'],
				  'aid'=>$_POST['aid'],))){
			 $this->msg('请勿重复提交',0);		  
					  }
     $valuedata=array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid'],
				  'aid'=>$_POST['aid'],
				  'values'=>serialize($data),
				  'time'=>time());
        //过滤完后提交表单
        model('selfform')->add_value($valuedata);
    
      
       
         $this->msg('提交成功',1);
       
        
    }
	
	  public function selfform_post()
    {
		
		var_dump($_POST);die;
    	$fid=in($_POST['fid']);
    	if(empty($fid)){
    		 $this->msg('未发现表单信息！',0);
    	}
		if($_POST['code']&&$_POST['mobile']){
	if($_COOKIE['mobilecode']&&$_POST['code']!=$_COOKIE['mobilecode']){
			
			 $this->msg('手机验证码不对！',0);
			}
		}
    	$info=model('selfform')->info($fid);
	
    	if(empty($info)){
    		 $this->msg('未发现表单信息！',0);
    	}
    	//获取所有字段
    	$field_list=model('selfform')->form_inputs_list(array('fid'=>$info['id']));
    	if(empty($field_list)){
            
                $this->msg('未发现表单信息！',0);
           
        }

        $data=array();
        foreach ($field_list as $value) {
            if($value['must']==1){
                if(empty($_POST[$value['field']])){
                    
                        $this->msg($value['name'].'不能为空！',0);
                   
                }
            }
            $data[$value['field']]=model('expand_model')->field_in($_POST[$value['field']],$value['type'],$value['field']);
            if(!isset($_POST[$value['field']])){
                $data[$value['field']]=$value['default'];
            }
        }
		
		 $valuedata=array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid'],
				  'values'=>serialize($data),
				  'time'=>time());
        //过滤完后提交表单
	if(model('selfform')->input_value(array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid']))){
					  
					   model('selfform')->edit_value($valuedata);
					   $msg=$info['msg']?$info['msg']:'修改成功';
					   
					 if($info['tourl']){
			$this->alert($msg,$info['tourl']);
			}else{
			 $this->msg($msg,1);	
			 }	  
					  }
    
        model('selfform')->add_value($valuedata);
    
      
        $msg=$info['msg']?$info['msg']:'提交成功';
		
		if($info['tourl']){
			$this->alert($msg,$info['tourl']);
			}else{
         $this->msg($msg,1);
			}
        
    }
	//提交表单
    public function formin($formdata)
    {
		
	
    	$fid=in($formdata['fid']);
    	if(empty($fid)){
    		$this->error404();
    	}
	
    	$info=model('selfform')->info($fid);
	
    	if(empty($info)){
    		$this->error404();
    	}
    	//获取所有字段
    	$field_list=model('selfform')->form_inputs_list(array('fid'=>$info['id']));
    	if(empty($field_list)){
            
            return;
           
        }

        $data=array();
        foreach ($field_list as $value) {
            if($value['must']==1){
				
				
                if(empty($formdata[$value['field']])){
			
                    return;
                   
                }
            }
            $data[$value['field']]=model('expand_model')->field_in($formdata[$value['field']],$value['type'],$value['field']);
            if(!isset($formdata[$value['field']])){
                $data[$value['field']]=$value['default'];
            }
        }

     $valuedata=array('fid'=>$fid,
	 			  'uid'=>$this->userinfo['uid'],
				  'aid'=>$formdata['aid'],
				  'commentid'=>$formdata['commentid'],
				  'values'=>serialize($data),
				  'time'=>time());
				 	
        //过滤完后提交表单
        model('selfform')->add_value($valuedata);
    
       
         return true;
       
        
    }
}

?>