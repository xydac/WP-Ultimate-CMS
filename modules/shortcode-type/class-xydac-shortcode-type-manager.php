<?php

class xydac_shortcode_type_manager extends xydac_ultimate_cms_core{

	function __construct()
	{
		$form_variables = array(
					'nestedtagname' => array( 'arr_label' => __('Nested Shortcode Name',XYDAC_CMS_NAME) , 'name' => '[nestedtagname]', 'type'=>'string', 'desc'=> __('Shortcode name used for nested shortcode.',XYDAC_CMS_NAME) , 'default'=>' '),
				'description' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[description]', 'type'=>'string', 'desc'=> __('A short descriptive summary of what the Shortcode is.',XYDAC_CMS_NAME) , 'default'=>' '),
				'simpleshortcode' => array( 'arr_label' => __('Simple Shortcode Type',XYDAC_CMS_NAME) , 'name' => '[simpleshortcode]', 'type'=>'boolean', 'desc'=> __('Please select if this shortcode is Simple and not Nested.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'attr' => array( 'arr_label' => __('Attributes',XYDAC_CMS_NAME) , 'name' => '[attr]', 'type'=>'textarea', 'desc'=> __('Please Enter valid attributes seperated by comma. You can use these attributes in template below.Attributes can have default value if required, this can be defined as key1=value1,key2,key3=value3',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
					'nestedattr' => array( 'arr_label' => __('Nested Attributes',XYDAC_CMS_NAME) , 'name' => '[nestedattr]', 'type'=>'textarea', 'desc'=> __('Please Enter valid attributes seperated by comma for nested shortcodes. You can use these attributes in template below.Attributes can have default value if required, this can be defined as key1=value1,key2,key3=value3',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customhtml' => array( 'arr_label' => __('Template',XYDAC_CMS_NAME) , 'name' => '[customhtml]', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
					'beforeloop' => array( 'arr_label' => __('Template before Loop',XYDAC_CMS_NAME) , 'name' => '[beforeloop]', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes before looping, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
					'loop1' => array( 'arr_label' => __('Inside Loop1',XYDAC_CMS_NAME) , 'name' => '[loop1]', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes before looping, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
					'afterloop1' => array( 'arr_label' => __('After Loop1',XYDAC_CMS_NAME) , 'name' => '[afterloop1]', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes before looping, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
					'loop2' => array( 'arr_label' => __('Loop2',XYDAC_CMS_NAME) , 'name' => '[loop2]', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes before looping, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
					'afterloop2' => array( 'arr_label' => __('After Loop1',XYDAC_CMS_NAME) , 'name' => '[afterloop2]', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes before looping, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				
				'customcss' => array( 'arr_label' => __('Custom Css ',XYDAC_CMS_NAME) , 'name' => '[customcss]', 'type'=>'textarea', 'desc'=> __('Custom CSS Styles for shortcode',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customscript' => array( 'arr_label' => __('Custom Java Script',XYDAC_CMS_NAME) , 'name' => '[customscript]', 'type'=>'textarea', 'desc'=> __('The custom Javascript to be used for shortcode',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
		);
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		
		$args = array('custom_css_id'=>'customcss','custom_js_id'=>'customscript');//this doesn't work here put it in modules
		parent::__construct(xydac()->modules->shortcode_type,'main',$form_variables,$args);
	}

	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[attr]'=>__("Attributes",XYDAC_CMS_NAME),'[args][tablemode]'=>__("Table Mode",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function xydac_core_leftdiv()
	{
		return "class=xydacfieldform";
	}
	function xydac_core_doactions()
	{
		$action = array('delete'=>__("Delete",XYDAC_CMS_NAME));
		return $action;
	}
	
}

?>