<?php

class xydac_taxonomy_type_fields extends xydac_ultimate_cms_core{
	var $name;

	function __construct($name)
	{

		$this->name = $name;
		global $xydac_cms_fields;
		$form_variables = array(
				'field_label' => array( 'arr_label' => __('Field Label',XYDAC_CMS_NAME) , 'name' => '[field_label]', 'type'=>'string', 'desc'=> __('Label used for Taxonomy Field ',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_type' => array( 'arr_label' => __('Field Type',XYDAC_CMS_NAME) , 'name' => '[field_type]', 'type'=>'array', 'desc'=> __('Field Type.',XYDAC_CMS_NAME) , 'default'=>' ', 'values'=>$xydac_cms_fields['fieldtypes']['taxonomy']),
				'field_desc' => array( 'arr_label' => __('Description for Field',XYDAC_CMS_NAME) , 'name' => '[field_desc]', 'type'=>'string', 'desc'=> __('Enter the short description for the field',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_val' => array( 'arr_label' => __('Field Value',XYDAC_CMS_NAME) , 'name' => '[field_val]', 'type'=>'string', 'desc'=> __('Please enter the values for the field (This is optional)',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_order' => array( 'arr_label' => __('Field Order ',XYDAC_CMS_NAME) , 'name' => '[field_order]', 'type'=>'string', 'desc'=> __('Enter 1,2,3.. order in which you want the Custom Field to appear.',XYDAC_CMS_NAME) , 'default'=>' '),
		);
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_action('xydac_core_righthead',array($this,'right_head'));
		add_filter('xydac_core_field_name',array($this,'field_name'));
		add_filter('xydac_core_editlink',array($this,'xydac_core_editlink_func'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		//parent::__construct(xydac()->modules->taxonomy_type->get_module_name().__("_field",XYDAC_CMS_NAME),xydac()->modules->taxonomy_type->get_module_label().__(" Field",XYDAC_CMS_NAME),xydac()->modules->taxonomy_type->get_base_path()."&manage_".xydac()->modules->taxonomy_type->get_module_name()."=".$name,xydac()->modules->taxonomy_type->get_registered_option('field')."_".$name,$form_variables);
		//parent::__construct("xydac_page_type_custom_field",__("Taxonomy Type Field",XYDAC_CMS_NAME),XYDAC_CMS_TAXONOMY_TYPE_FIELDS_PATH."&manage_taxonomy_type=".$name,XYDAC_CMS_TAXONOMY_TYPE_OPTION."_".$name,$form_variables);
		$args = array('field_val' => $name);;
		parent::__construct(xydac()->modules->taxonomy_type,'field',$form_variables,$args);
	}

	function field_name()
	{
		return "field_name";
	}
	function xydac_core_editlink_func($str)
	{
		return $str.'&manage_taxonomy_type='.$this->name;
	}
	function headfootcolumn()
	{
		$headfootcolumn = array('field_name'=>__('Name',XYDAC_CMS_NAME),'[field_label]'=>__('Label',XYDAC_CMS_NAME),'[field_order]'=>__('Order',XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function right_head()
	{
		echo '<p><strong> '.__('Custom Taxonomy Type Name : ',XYDAC_CMS_NAME).'<span style="color:red">'.$this->name.'</span></strong></p>';
	}
	function xydac_core_leftdiv()
	{
		return "class=xydacfieldform";
	}
}

?>