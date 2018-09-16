<?php

class xydac_page_type_manager extends xydac_ultimate_cms_core{


	function __construct()
	{
		$avl_types = "";
		if(isset($_GET['xydac_page_type_name']))
		{
			$fields = xydac()->modules->page_type->get_field($_GET['xydac_page_type_name']);
			if(is_array($fields))
			{
				$avl_types.="<ul>";
				foreach($fields as $field)
					$avl_types.="<li><b>[xydac_page_field]".$field['field_name']."[/xydac_page_field]</b></li>";
				$avl_types.="</ul>";
			}
		}
		$form_variables = array(
				'label' =>  array( 'arr_label' => __('Label for Page Type ',XYDAC_CMS_NAME) , 'name' => '[label]', 'type'=>'string', 'desc'=> __('A plural descriptive name for the page type marked for translation.',XYDAC_CMS_NAME) , 'default'=>' '),
				'description' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[description]', 'type'=>'string', 'desc'=> __('A short descriptive summary of what the page type is.',XYDAC_CMS_NAME) , 'default'=>' '),
				'content_html' => array( 'arr_label' => __('Content HTML',XYDAC_CMS_NAME) , 'name' => '[content_html]' , 'arr_clazz' => 'codemirror_custom_html', 'type'=>'textarea', 'desc'=> __('The Content of this is used on the frontend of current page type as template. You should use the shortcode and html to define how overall page should look like. Should you need the editor box content you can used shortcode [CONTENT]',XYDAC_CMS_NAME), 'default'=>''),
				'content_css' => array( 'arr_label' => __('Content CSS',XYDAC_CMS_NAME) , 'name' => '[content_css]' , 'arr_clazz' => 'codemirror_custom_css', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom CSS Styles for this page type ',XYDAC_CMS_NAME), 'default'=>''),
				'content_js' => array( 'arr_label' => __('Content Javascript',XYDAC_CMS_NAME) , 'name' => '[content_js]' , 'arr_clazz' => 'codemirror_custom_js', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom Java script for this page type ',XYDAC_CMS_NAME), 'default'=>''),
		);
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_action('xydac_core_bulkaction',array($this,'xydac_core_bulkaction'));
		
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot'));
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot_help'));
		//add_filter('xydac_core_rowactions',array($this,'xydac_core_rowactions'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		//parent::__construct(xydac()->modules->page_type->get_module_name(),xydac()->modules->page_type->get_module_label(),xydac()->modules->page_type->get_base_path(),xydac()->modules->page_type->get_registered_option('main'),$form_variables,true,false);
		$args = array('enableactivation'=>false,'xydac_core_show_additional' => true,'custom_css_id'=>'content_css','custom_jss_id'=>'content_js');
		parent::__construct(xydac()->modules->page_type,'main',$form_variables,$args);
	}
	
	function xydac_core_rightfoot()
	{	if($this->xydac_core_editmode && isset($this->xydac_editdata['name'])){
			$fields = xydac()->modules->page_type->get_field($this->xydac_editdata['name']);
			echo '<div class="editbox">';
			echo '<h3>'.__("Available Short codes").'</h3>';
			foreach($fields as $k=>$field){
				echo '<pre>[xydac_page_field]'.$field['field_name'].'[/xydac_page_field]</pre>';//
			}
			echo '</div>';
		}
	}
	function xydac_core_rightfoot_help()
	{	
		?>
			<div class="editbox">
				<h3>Quick Help</h3>
				<p>Custom Page Type are something new. It’s similar to Custom post type but are made for Pages. You can do almost everything with a page that you could do with custom post type.</p>
				<p>Using this you can define a permanent template styling and fields for similar information that is posted in multiple pages.</p>
				
				Check out More Details at  
				<a href="https://xydac.com/ultimate-cms/page-type/">
				https://xydac.com/ultimate-cms/page-type/</a>
			</div>
		<?php
		
	}
	function xydac_core_leftdiv()
	{
		return "class=xydacfieldform";
	}
	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[label]'=>__("Label",XYDAC_CMS_NAME),'[description]'=>__("Description",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function xydac_core_rowactions()
	{
		$action = array('Export'=>get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php?page_type_name=");
		return $action;
	}
	function xydac_core_doactions()
	{
		$action = array('activate'=>__("Activate",XYDAC_CMS_NAME),
				'deactivate'=>__("Deactivate",XYDAC_CMS_NAME),
				'delete'=>__("Delete",XYDAC_CMS_NAME)
		);
		return $action;
	}
	function xydac_core_bulkaction($page)
	{
		switch($_POST['action'])
		{
			case "export" :{
				$cpt ="";
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
					$cpt.=$v.",";
				$cpt = substr($cpt,0,-1);
				$l = get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php?page_type_name=".$cpt;
				echo "<div id='message' class='updated below-h2'><p><a href=$l>".__('Click Here to download the Export File',XYDAC_CMS_NAME)."</a></p></div>";
			}
		}
	}
}

?>