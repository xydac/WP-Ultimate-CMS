<?php
class xydac_module_manager_manager extends xydac_ultimate_cms_core{
	var $base_path;
	function __construct()
	{
		$form_variables = array();
		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms&sub=modulemanager';
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		parent::__construct("xydac_module_manager","Modules",$this->base_path,xydac()->allModules,$form_variables,true,true,array("active"=>XYDAC_CMS_ACTIVEM_OPTIONS,"show_link"=>"false"));
	}

	
	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[type]'=>__("TYPE",XYDAC_CMS_NAME),'[author]'=>__("Author",XYDAC_CMS_NAME),'[description]'=>__("Description",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
}
?>