<?php
class xydac_module_manager_manager extends xydac_ultimate_cms_core{
	var $base_path;
	function __construct()
	{
		$form_variables = array();
		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms&sub=modulemanager';
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		//parent::__construct("xydac_module_manager","Modules",$this->base_path,xydac()->allModules,$form_variables,true,true,array("active"=>XYDAC_CMS_ACTIVEM_OPTIONS,"show_link"=>"false"));
		$args = array('xydac_core_show_additional' => false,'xydac_core_show_left'=>false,
				'xydac_core_show_doaction'=>false,'show_link'=>false,
				'xydac_core_show_delete'=>false,'xydac_core_show_sync'=>false);
		add_filter('xydac_core_rowdata', array($this,'xydac_core_rowdata_func'));
		add_action('xydac_core_head',array($this,'xydac_core_head_func'));
		parent::__construct(xydac()->modules->module_manager,'main',$form_variables,$args);
	}

	
	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[type]'=>__("TYPE",XYDAC_CMS_NAME),'[description]'=>__("Description",XYDAC_CMS_NAME),'[author]'=>__("Author",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function xydac_core_head_func()
	{
		
		_e('Module Manager helps you to manage the modules associated with Ultimate CMS plugin. You can activate or deactivate the modules as per your use, Leaving any module activated doesn\'t use any extra resource But still it\'s safer to deactivate the same if not being used.');
	}
	function xydac_core_rowdata_func($datas){
		foreach($datas as $k=>$data)
			if($data['type']=='Core-Module' || $data['type']=='core-module' || $data['type']=='CoreModule' || $data['type']=='coremodule' || $data['type']=='coreModule')
				unset($datas[$k]);
			else 
				$datas[$k]['type'] = ucwords($datas[$k]['type']);
		return $datas;
	}
}
?>