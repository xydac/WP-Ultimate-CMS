<?php

class xydac_shortcode_type_manager extends xydac_ultimate_cms_core{

	function __construct()
	{
		$form_variables = array(
				'nestedtagname' => array( 'arr_label' => __('Nested Shortcode Name',XYDAC_CMS_NAME) , 'name' => '[nestedtagname]', 'type'=>'string', 'desc'=> __('Shortcode name used for nested shortcode.',XYDAC_CMS_NAME) , 'default'=>' '),
				'description' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[description]', 'type'=>'string', 'desc'=> __('A short descriptive summary of what the Shortcode is.',XYDAC_CMS_NAME) , 'default'=>' '),
				'attr' => array( 'arr_label' => __('Attributes',XYDAC_CMS_NAME) , 'name' => '[attr]', 'type'=>'textarea', 'desc'=> __('Please Enter valid attributes seperated by comma. You can use these attributes in template below.Attributes can have default value if required, this can be defined as key1=value1,key2,key3=value3',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customhtml' => array( 'arr_label' => __('Template',XYDAC_CMS_NAME) , 'name' => '[customhtml]' , 'arr_clazz' => 'codemirror_custom_html', 'type'=>'textarea', 'desc'=> __('The Custom template to be used for shortcodes, If you have an attribute email, You can use this attributes in template by writing it as ##email##. Moreover if you want to use the inside content of your shortcode, you need to use ##content##.  ',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customcss' => array( 'arr_label' => __('Custom Css ',XYDAC_CMS_NAME) , 'name' => '[customcss]' , 'arr_clazz' => 'codemirror_custom_css', 'type'=>'textarea', 'desc'=> __('Custom CSS Styles for shortcode',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
				'customscript' => array( 'arr_label' => __('Custom Java Script',XYDAC_CMS_NAME) , 'name' => '[customscript]' , 'arr_clazz' => 'codemirror_custom_js', 'type'=>'textarea', 'desc'=> __('The custom Javascript to be used for shortcode',XYDAC_CMS_NAME) , 'default'=>' ', 'height'=>'100px'),
		);
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot_shortcode'));
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot'));
		
		
		$args = array('custom_css_id'=>'customcss','custom_js_id'=>'customscript');//this doesn't work here put it in modules
		parent::__construct(xydac()->modules->shortcode_type,'main',$form_variables,$args);
	}
	function xydac_core_rightfoot()
	{	
		?>
			<div class="editbox">
				<h3>Quick Help</h3>
				You can create custom shortcodes such that when you use it in content it uses the HTML  defined here in <em>Template</em> section and using the style defined in Custom Css section.
				You may also inject custom Javascript.
				<h5>The shortcode created after activating is as below</h5>
				<code>[xys_{name} {attr}="{val}" ] {content} [/xys_{name}]</code>
				<ol>
					<li>{name}: The name for the shortcode. If the shortcode name is card then shortcode generated will be [xys_card]</li>
					<li>{attr}: any values defined in Attributes box</li>
					<li>{val}: Value of given Attribute.</li>
					<li>{content}: Based on use content can be used inside the template</li>
				</ol>
				
				Check out Example 
				<a href="https://xydac.com/ultimate-cms/shortcode-manager/shortcode-example/">
				https://xydac.com/ultimate-cms/shortcode-manager/shortcode-example/</a>
			</div>
		<?php
		
	}

	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[attr]'=>__("Attributes",XYDAC_CMS_NAME),'[description]'=>__("Description",XYDAC_CMS_NAME));
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
	function xydac_core_rightfoot_shortcode($module_data){
		
		if($module_data->xydac_core_editmode){
			$attr = $module_data->xydac_editdata['attr'];
			$name = $module_data->xydac_editdata['name'];
			$attributes = explode(',',$attr);
			$str='';
			foreach($attributes as $a){
				$pos=strpos($a,'=');
				if($pos>-1){
					$str.= substr($a,0,$pos).'="'.substr($a,$pos+1).'" ';
				}else{
					$str.= $a.'="" ';
				}
			}
			echo '<div class="editbox">';
			echo '<h3>'.__("Short Code For Use").'</h3>';
			
				echo "<code>[xys_$name $str] [/xys_$name]</code>";
			
			echo '</div>';
		}
	}
}

?>