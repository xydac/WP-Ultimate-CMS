<?php

class xydac_forms_manager extends xydac_ultimate_cms_core{


	function __construct()
	{
		$avl_types = "";
		if(isset($_GET['xydac_forms_name']))
		{
			$fields = xydac()->modules->forms->get_field($_GET['xydac_forms_name']);
			if(is_array($fields))
			{
				$avl_types.="<ul>";
				foreach($fields as $field)
					$avl_types.="<li><b>[xydac_page_field]".$field['field_name']."[/xydac_page_field]</b></li>";
				$avl_types.="</ul>";
			}
        }
        
		$form_variables = array(
                
                'heading-1' => array('arr_label' => __('Design',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_label', 'type'=>'heading', 'initialclose'=>false),
                'description' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[description]', 'type'=>'string', 'desc'=> __('A short descriptive summary of what the page type is.',XYDAC_CMS_NAME) , 'default'=>' '),
				'content_html' => array( 'arr_label' => __('Content HTML',XYDAC_CMS_NAME) , 'name' => '[content_html]' , 'arr_clazz' => 'codemirror_custom_html', 'type'=>'textarea', 'desc'=> __('The Content of this is used on the frontend of current page type as template. You should use the shortcode and html to define how overall page should look like. Should you need the editor box content you can used shortcode [CONTENT]',XYDAC_CMS_NAME), 'default'=>''),
				'content_css' => array( 'arr_label' => __('Content CSS',XYDAC_CMS_NAME) , 'name' => '[content_css]' , 'arr_clazz' => 'codemirror_custom_css', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom CSS Styles for this page type ',XYDAC_CMS_NAME), 'default'=>''),
                'content_js' => array( 'arr_label' => __('Content Javascript',XYDAC_CMS_NAME) , 'name' => '[content_js]' , 'arr_clazz' => 'codemirror_custom_js', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom Java script for this page type ',XYDAC_CMS_NAME), 'default'=>''),
                
        );
        $form_variables = apply_filters( 'xydac_forms_variables', $form_variables );
        $form_variables['heading-95'] = array('name'=>'finalheading','type'=>'heading','initialclose'=>true, 'finalclose'=>true);


		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_action('xydac_core_bulkaction',array($this,'xydac_core_bulkaction'));
		
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot'));
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot_help'));
		//add_filter('xydac_core_rowactions',array($this,'xydac_core_rowactions'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		//parent::__construct(xydac()->modules->forms->get_module_name(),xydac()->modules->forms->get_module_label(),xydac()->modules->forms->get_base_path(),xydac()->modules->forms->get_registered_option('main'),$form_variables,true,false);
		$args = array('enableactivation'=>false,'xydac_core_show_additional' => true,'custom_css_id'=>'content_css','custom_jss_id'=>'content_js');
		parent::__construct(xydac()->modules->forms,'main',$form_variables,$args);
	}
	
	function xydac_core_rightfoot()
	{	if($this->xydac_core_editmode && isset($this->xydac_editdata['name'])){
            $fields = xydac()->modules->forms->get_field($this->xydac_editdata['name']);
            echo '<div class="editbox">';
            echo '<h3>'.__("Available Fields for use in Action Template").'</h3>';
            foreach($fields as $k=>$field){
				echo '<pre>##'.$field['field_name'].'##</pre>';//
			}
			echo '</div>';
		}
	}
	function xydac_core_rightfoot_help()
	{	
		?>
			<div class="editbox">
				<h3>Quick Help</h3>
				<p>Xydac Forms allows your to create Custom Forms. You can use the form in frontend using shortcode on any page</p>
				<p>Individual forms can be customized based on needs. Edit <strong>Custom HTML</strong> section to create any html form. To add the fields anywhere add <strong>[CONTENT]</strong>.</p>
                <p>Individual form's css and javsscript can be update in respective sections.</p>
				<h4><a href="admin.php?page=xydac_ultimate_cms"> Note that you need to Activate Mods with Form Action</a></h4>
                
			</div>
		<?php
		
	}
	function xydac_core_leftdiv()
	{
		return "class=xydacfieldform";
	}
	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[description]'=>__("Description",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function xydac_core_rowactions()
	{
		$action = array('Export'=>get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php?forms_name=");
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
				$l = get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php?forms_name=".$cpt;
				echo "<div id='message' class='updated below-h2'><p><a href=$l>".__('Click Here to download the Export File',XYDAC_CMS_NAME)."</a></p></div>";
			}
		}
	}
}

?>