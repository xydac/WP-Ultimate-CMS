<?php
/*
 Module Name:	ShortCode
Type:			Module
Description:	Xydac Shortcode module
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

		add_filter('xydac_cms_admin_script',array($this,'xydac_cms_admin_script_func'),60,1);
	}
	
	function xydac_cms_admin_script_func($script){
		$s=<<<SCRIPT
		jQuery(document).ready(function() {
		jQuery("select[name='shortcode_type\[simpleshortcode\]']").change(function(){
			if(jQuery(this).children(':selected').first().text()=='True'){
				jQuery("div[id='xydac_panel_shortcode_type\[nestedtagname\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[nestedattr\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[beforeloop\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[loop1\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[afterloop1\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[loop2\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[afterloop2\]']").hide();
			}
			else{
				jQuery("div[id='xydac_panel_shortcode_type\[customhtml\]']").hide();
				jQuery("div[id='xydac_panel_shortcode_type\[nestedtagname\]']").show();
				jQuery("div[id='xydac_panel_shortcode_type\[nestedattr\]']").show();
				jQuery("div[id='xydac_panel_shortcode_type\[beforeloop\]']").show();
				jQuery("div[id='xydac_panel_shortcode_type\[loop1\]']").show();
				jQuery("div[id='xydac_panel_shortcode_type\[afterloop1\]']").show();
				jQuery("div[id='xydac_panel_shortcode_type\[loop2\]']").show();
				jQuery("div[id='xydac_panel_shortcode_type\[afterloop2\]']").show();
			}
		});
});
SCRIPT;
		return $script."\n/*-----Xydac Shortcode admin Script-----*/\n".$s;
	}
}

?>