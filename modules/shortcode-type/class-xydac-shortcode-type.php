<?php
/*
 Module Name:	ShortCode
Type:			Module
Description:	Xydac Shortcode allows user to create shortcode for various HTML tags. It allows you to create new Shortcode that behaves the way you want them to be.
Module URI: 	http://xydac.com/ultimate-cms/shortcode-manager/
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-shortcode-type-functions.php';
include 'class-xydac-shortcode-type-manager.php';
class xydac_shortcode_type extends xydac_cms_module{

	function __construct()
	{
		parent::__construct('shortcode_type',array('module_label'=>'Xydac Shortcodes',
				'has_custom_fields'=>false,
				'uses_active'=>true,
				'registered_option'=>array('main'=>'xydac_shortcode',
						'active'=>'xydac_shortcode_active'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms_shortcode_type',
				'menu_position'=>'top',
				'custom_css_id'=>'customcss','custom_js_id'=>'customscript'
		));
		new xydac_shortcode_type_functions();
	}
	
}

?>