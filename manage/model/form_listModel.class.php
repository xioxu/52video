<?php
//表单数据处理
class form_listModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //字段列表
    public function field_list($id)
    {
        return $this->model->table('form_field')->where('fid='.$id.' AND admin_display=1')->order('sequence asc,id asc')->select();
    }
    //所有字段列表
    public function list_lod($id)
    {
        return $this->model->table('form_field')->where('fid='.$id)->select();
    }
    public function signinfo($where){
        if(empty($where)){
            return;
        }
		
        $signinfo=$this->model->table('signup')->where($where)->find();
		
        return $signinfo;
    }
	  //内容信息
    public function infobyuser($uid,$table){
        //读取模型表
        $aid=$this->model->table('form_data_'.$table)->where('uid='.$uid)->find();
        return $aid;
    }
    //表单内容列表
    public function form_list($id,$limit,$order)
    {
        //获取模型表
        $model=model('form')->info($id);
        $lang=model('lang')->current_lang();
        return $this->model->table('form_data_'.$model['table'])->where('lang='.$lang['id'])->order($order.',id desc')->limit($limit)->select();
    }
	
	 //表单内容列表
    public function form_signuplist($where,$limit='')
    {
   
		
        return $this->model->field('A.*,B.status,B.dateline')->table('form_data_signup','A')->add_table('signup','B','A.uid=B.uid')->where($where)->order('B.id desc')->limit($limit)->select();
    }
	
	  public function form_signupautolist($where,$limit='')
    {
   
		
        return $this->model->table('signupauto')->where($where)->limit($limit)->select();
    }
	
	 //审核草稿
    public function status($uid,$aid,$status){
        $where['status']=intval($status);
        return $this->model->table('signup')->data($where)->where(array('aid'=>$aid,'uid'=>$uid))->update();
    }
	
	  public function delsign($uid,$aid){
      
        return $this->model->table('signup')->where(array('aid'=>$aid,'uid'=>$uid))->delete();
    }
	  public function delsignauto($id,$aid){
      
        return $this->model->table('signupauto')->where(array('aid'=>$aid,'id'=>$id))->delete();
    }
	
	  public function signautoinfo($where){
      
        return $this->model->table('signupauto')->where($where)->find();
    }
	
	
	  public function addsignauto($data){
      if(!$this->signautoinfo($data))
        return $this->model->table('signupauto')->data($data)->insert();
    }
	
	//表单内容统计
    public function signupcount($where)
    {
        
        return $this->model->table('form_data_signup','A')->add_table('signup','B','A.uid=B.uid')->where($where)->count();
    }
	//表单内容统计
    public function signupautocount($where)
    {
      
        return $this->model->table('signupauto')->where($where)->count();
    }

    //表单内容统计
    public function count($id)
    {
        //获取模型表
        $model=model('form')->info($id);
        return $this->model->table('form_data_'.$model['table'])->count();
    }

    //添加内容
    public function add($data){
        if(empty($data)){
            return;
        }
        //读取模型表
        $model=model('form')->info($data['fid']);
        //录入表数据
        $lang=model('lang')->current_lang();
        $data['lang']=$lang['id'];
        $id=$this->model->table('form_data_'.$model['table'])->data($data)->insert();
        return $id;
    }

    //编辑内容
    public function edit($data){
        if(empty($data)){
            return;
        }
        //读取模型表
        $model=model('form')->info($data['fid']);
        //录入表数据
        $condition['id']=$data['id'];
        $id=$this->model->table('form_data_'.$model['table'])->data($data)->where($condition)->update(); 
        return $id;
    }


    //内容信息
    public function info($id,$table){
        //读取模型表
        $aid=$this->model->table('form_data_'.$table)->where('id='.$id)->find();
        return $aid;
    }

    //内容删除
    public function del($id,$fid){
        if(empty($id)||empty($fid)){
            return;
        }
        //读取模型表
        $model=model('form')->info($fid);
        //删除操作
        $condition['id']=$id;
        return $this->model->table('form_data_'.$model['table'])->where($condition)->delete();
    }


}