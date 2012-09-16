<?php
/*
 Module Name:	modulemanager
Type:			Core-Module
Description:	Xydac Module Manager
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-module-manager-manager.php';

class xydac_module_manager{
	var $base_path;

	function __construct()
	{
		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms';
		add_filter('xydac_cms_home_tab',array($this,'xydac_cms_home_tab_func'));
		add_action('xydac_cms_module_view_main',array($this,'xydac_cms_module_view_main_func'));
		add_filter('xydac_cms_admin_style',array($this,'xydac_cms_admin_style_func'),10,1);
		
	}
	public function xydac_cms_home_tab_func($tab){
		$mytab = array('name'=>'modulemanager',
				'href'=>$this->base_path."&sub=modulemanager",
				'label'=>'Module Manager',
				'default'=>false);
		if(is_array($tab))
			$tab['modulemanager'] = $mytab;
		else
			$tab = $mytab; 
		return $tab;
		
	}
	function xydac_cms_module_view_main_func($tabname){
		if($tabname == 'modulemanager')
			new xydac_module_manager_manager();
	}
	public function xydac_cms_admin_style_func($style){
		$st = ".xydac_module_manager>#col-left{display:none;}
		.xydac_module_manager>#col-right{width:100%;}
		.xydac_module_manager .tablenav{display:none;}
		";
		return $style."\n".$st;
	}
}
?>