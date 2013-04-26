<?php

class xydac_post_type_fields extends xydac_ultimate_cms_core{
	var $name;

	function __construct($name)
	{
		$this->name = $name;
		global $xydac_cms_fields;
		$form_variables = array(
				'field_label' => array( 'arr_label' => __('Field Label',XYDAC_CMS_NAME) , 'name' => '[field_label]', 'type'=>'string', 'desc'=> __('Label used for Page type Field ',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_type' => array( 'arr_label' => __('Field Type',XYDAC_CMS_NAME) , 'name' => '[field_type]', 'type'=>'array', 'desc'=> __('Field Type.',XYDAC_CMS_NAME) , 'default'=>' ', 'values'=>$xydac_cms_fields['fieldtypes']['posttype']),
				'field_has_multiple' => array( 'arr_label' => __('Field Takes Multiple Input',XYDAC_CMS_NAME) , 'name' => '[field_has_multiple]', 'type'=>'array', 'desc'=> __('Does this Field take multiple values.',XYDAC_CMS_NAME) , 'default'=>' ', 'values'=>array('true'=>'True','false'=>'False')),
				'field_desc' => array( 'arr_label' => __('Description for Field',XYDAC_CMS_NAME) , 'name' => '[field_desc]', 'type'=>'string', 'desc'=> __('Enter the short description for the field',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_val' => array( 'arr_label' => __('Field Value',XYDAC_CMS_NAME) , 'name' => '[field_val]', 'type'=>'string', 'desc'=> __('Please enter the values for the field (This is optional)',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_order' => array( 'arr_label' => __('Field Order ',XYDAC_CMS_NAME) , 'name' => '[field_order]', 'type'=>'string', 'desc'=> __('Enter 1,2,3.. order in which you want the Custom Field to appear.',XYDAC_CMS_NAME) , 'default'=>' '),
		);
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_action('xydac_core_righthead',array($this,'right_head'));
		add_filter('xydac_core_field_name',array($this,'field_name'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_filter('xydac_core_editlink',array($this,'xydac_core_editlink_func'));
		//parent::__construct(xydac()->modules->post_type->get_module_name().__("_field",XYDAC_CMS_NAME),xydac()->modules->post_type->get_module_label().__(" Field",XYDAC_CMS_NAME),xydac()->modules->post_type->get_base_path()."&manage_".xydac()->modules->post_type->get_module_name()."=".$name,xydac()->modules->post_type->get_registered_option('field')."_".$name,$form_variables);
		$args = array('field_val' => $name);
		parent::__construct(xydac()->modules->post_type,'field',$form_variables,$args);
	}

	function field_name()
	{
		return "field_name";
	}
	function xydac_core_editlink_func($str)
	{
		return $str.'&manage_post_type='.$this->name;
	}
	function headfootcolumn()
	{
		$headfootcolumn = array('field_name'=>__("Name",XYDAC_CMS_NAME),'[field_label]'=>__("Label",XYDAC_CMS_NAME),'[field_type]'=>__("Type",XYDAC_CMS_NAME),'[field_order]'=>__("Field Order",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function right_head()
	{
		echo '<p><strong> '.__('Custom Post Type Name : ',XYDAC_CMS_NAME).'<span style="color:red">'.$this->name.'</span></strong></p>';
	}
	function xydac_core_leftdiv()
	{
		return "class=xydacfieldform";
	}
}

?>