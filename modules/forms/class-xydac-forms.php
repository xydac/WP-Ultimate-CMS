<?php
/*
Module Name:	Xydac Forms
Type:			Module
Description:	Xydac Forms Manager allows you to create and manage custom Forms for website Frontend. Based on the Enabled Mods, different actions can be performed on Form Submission.
Module URI: 	http://xydac.com/ultimate-cms/forms
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-forms-fields.php';
include 'class-xydac-forms-manager.php';
include 'class-xydac-forms-use.php';
class xydac_forms extends xydac_cms_module{

	function __construct()
	{
		parent::__construct('forms',array('module_label'=>'Xydac Forms',
				'has_custom_fields'=>true,
				'uses_active'=>true,
				'registered_option'=>array('main'=>'xydac_forms',
					'active'=>'xydac_forms_active',
					'field'=>'xydac_forms'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms_forms',
				'menu_position'=>'top',
				'custom_css_id'=>'customcss','custom_jss_id'=>'customscript'
		));
		new xydac_forms_use();
	}
	function get_form($form_id=0)
	{
		return get_main_by_name($form_id);	
	}

}

?>