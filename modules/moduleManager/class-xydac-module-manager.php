<?php
/*
Module Name:	Module Manager
Type:			Core-Module
Description:	Xydac Module Manager
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-module-manager-manager.php';

class xydac_module_manager extends xydac_cms_module{
	var $base_path;

	function __construct()
	{
		parent::__construct('module_manager',array('module_label'=>'Module Manager',
				'has_custom_fields'=>false,
				'uses_active'=>true,
				'registered_option'=>array('main'=>'XYDAC_CMS_MODULES',
						'active'=>XYDAC_CMS_MODULES.'_active'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms'."&sub=module_manager"
		));
		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms';
		add_filter('xydac_cms_home_tab',array($this,'xydac_cms_home_tab_func'));
		add_action('xydac_cms_module_view_main',array($this,'xydac_cms_module_view_main_func'));
		add_filter('xydac_cms_admin_style',array($this,'xydac_cms_admin_style_func'),10,1);
	}

	public function xydac_cms_home_tab_func($tab){
		$mytab = array('name'=>'module_manager',
				'href'=>$this->base_path."&sub=module_manager",
				'label'=>'Module Manager',
				'default'=>false);
		if(is_array($tab))
			$tab['module_manager'] = $mytab;
		else
			$tab = $mytab; 
		return $tab;
		
	}
	function xydac_cms_module_view_main_func($tabname){
		if($tabname == 'module_manager')
			new xydac_module_manager_manager();

		
	}
	public function xydac_cms_admin_style_func($style){
		$st = ".xydac-col-module_manager-description{width:40%;}
				.xydac-col-module_manager-type{width:20%;}
				.xydac-col-module_manager-author{width:20%;}
				";
		return $style."\n".$st;
	}
}
?>
