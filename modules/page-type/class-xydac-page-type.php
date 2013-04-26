<?php
/*
Module Name:	Page Type
Type:			Module
Description:	Xydac Page Type module
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-page-type-manager.php';
include 'class-xydac-page-type-fields.php';
include 'class-xydac-page-type-use.php';
include 'class-xydac-page-type-functions.php';
class xydac_page_type extends xydac_cms_module{

	function __construct()
	{
		parent::__construct('page_type',array('module_label'=>'Xydac Page Type',
				'has_custom_fields'=>true,
				'uses_active'=>true,
				'registered_option'=>array('main'=>'xydac_page_type',
						'active'=>'xydac_page_type_active',
						'field'=>'xydac_page_type'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms_page_type',
				'menu_position'=>'top'
		));
		new xydac_page_type_use();
		//new xydac_page_type_functions();
	}

	function get_page_type($page_id=0)
	{
		global $post;
		if(0==$page_id)
			return get_post_meta($post->ID, 'page_type', true);
		else
			return get_post_meta($page_id, 'page_type', true);
	}
}
?>