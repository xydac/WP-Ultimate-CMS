<?php

class xydac_archive_type_manager extends xydac_ultimate_cms_core{

	function __construct()
	{
		$form_variables = array(
				'query' => array( 'arr_label' => __('Query',XYDAC_CMS_NAME) , 'name' => '[query]', 'type'=>'textarea', 'desc'=> __('The Main Query for WP_QUERY ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'description' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[description]', 'type'=>'string', 'desc'=> __('A short descriptive summary of what the Archive is.',XYDAC_CMS_NAME) , 'default'=>' '),
				'width' => array( 'arr_label' => __('Wrapper DIV  width',XYDAC_CMS_NAME) , 'name' => '[args][width]', 'type'=>'string', 'desc'=> __('The Width for Wrapper DIV',XYDAC_CMS_NAME) , 'default'=>' '),
				'cols' => array( 'arr_label' => __('Number of Columns',XYDAC_CMS_NAME) , 'name' => '[args][cols]', 'type'=>'array', 'desc'=> __('Select The Number of Columns in the archive. (This works only with Table Mode in Flase)',XYDAC_CMS_NAME) , 'default'=>' ', 'values'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6')),
				'rows' => array( 'arr_label' => __('Number of Rows',XYDAC_CMS_NAME) , 'name' => '[args][rows]', 'type'=>'array', 'desc'=> __('Select The Number of Rows in the archive (This works only with Table Mode in Flase)',XYDAC_CMS_NAME) , 'default'=>' ', 'values'=>array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6')),
				'beforeloop' => array( 'arr_label' => __('Before Loop HTML',XYDAC_CMS_NAME) , 'name' => '[args][beforeloop]', 'type'=>'textarea', 'desc'=> __('THE HTML Content to be displayed before loop',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customhtml' => array( 'arr_label' => __('Custom HTML',XYDAC_CMS_NAME) , 'name' => '[args][customhtml]', 'type'=>'textarea', 'desc'=> __('THE HTML Content to be displayed in loop',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'afterloop' => array( 'arr_label' => __('After Loop HTML',XYDAC_CMS_NAME) , 'name' => '[args][afterloop]', 'type'=>'textarea', 'desc'=> __('THE HTML Content to be displayed After loop',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'tablemode' => array( 'arr_label' => __('Table Mode ',XYDAC_CMS_NAME) , 'name' => '[args][tablemode]', 'type'=>'boolean', 'desc'=> __('Display as Table ?',XYDAC_CMS_NAME) , 'default'=>'false'),
				'customcss' => array( 'arr_label' => __('Custom Css ',XYDAC_CMS_NAME) , 'name' => '[customcss]', 'type'=>'textarea', 'desc'=> __('Custom CSS Styles for archive',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customscript' => array( 'arr_label' => __('Custom Java Script',XYDAC_CMS_NAME) , 'name' => '[customscript]', 'type'=>'textarea', 'desc'=> __('The custom Javascript to be used for archive',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
		);
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		//parent::__construct("xydac_archive","Archive",XYDAC_CMS_ARCHIVE_PATH,XYDAC_CMS_ARCHIVE_OPTION,$form_variables);
		//parent::__construct(xydac()->modules->archive_type->get_module_name(),xydac()->modules->archive_type->get_module_label(),xydac()->modules->archive_type->get_base_path(),xydac()->modules->archive_type->get_registered_option('main'),$form_variables);
		$args = array('custom_css_id'=>'customcss','custom_jss_id'=>'customscript');
		parent::__construct(xydac()->modules->archive_type,'main',$form_variables,$args);
	}

	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[query]'=>__("Query",XYDAC_CMS_NAME),'[args][tablemode]'=>__("Table Mode",XYDAC_CMS_NAME));
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