<?php
/*
Module Name:	Codemirror
Type:			Mod
Description:	Adds Codemirror Support to Ultimate CMS plugin
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0
*/

/*
 * Add the styles to plugin
 * Add the script to plugin
 * Make the filter modification
 * @todo: complete the preview option for archives.
 */
class xydac_mods_codemirror{
	var $plugindir;
	function __construct(){
		$this->plugindir = dirname(__FILE__);
		add_filter('xydac_cms_admin_style',array($this,'xydac_cms_admin_style_func'),10,1);
		add_filter('xydac_cms_admin_script',array($this,'xydac_cms_admin_script_func'),10,1);
		add_filter('xydac_cms_admin_script_url',array($this,'xydac_cms_admin_script_url_func'),10,1);
		add_filter('xydac_cms_admin_style_url',array($this,'xydac_cms_admin_style_url_func'),10,1);
		add_action('xydac_core_rightfoot',array($this,'xydac_core_rightfoot_func'));
		add_action('wp_ajax_xydac_cms_codemirror', array($this,'wp_ajax_xydac_cms_codemirror_func' ));
	}
	function xydac_cms_admin_style_func($style)
	{
		ob_start();
		include $this->plugindir.'/codemirror.css';
		$codemirror_style = ob_get_clean();
		if(isset($_GET['codemirror'])&& ('1'==$_GET['codemirror'])){
			return $style."\n".$codemirror_style;
		}
		else
			return $style;
	}
	function xydac_cms_admin_script_func($script)
	{
		ob_start();
		include $this->plugindir.'/codemirror.js';
		include $this->plugindir.'/codemirrorinit.js';
		include $this->plugindir.'/css.js';
		include $this->plugindir.'/javascript.js';
		include $this->plugindir.'/xml.js';
		include $this->plugindir.'/htmlmixed.js';
		$codemirror_script = ob_get_clean();
		 $codemirror_script.=<<<SCRIPT
		function updatePreview() {
	        var previewFrame = document.getElementById('xydac-codemirror-preview');
	        var preview =  previewFrame.contentDocument ||  previewFrame.contentWindow.document;
	        preview.open();
	        var i=0;
	        var beforeloop='',customhtml='',afterloop='',customcss='',customscript='';
	        for(i;i<5;i++)
	        {
	        if(xydac_codemirror_name[i]=='beforeloop')
	        	jQuery.ajax({type:'POST',url:ajaxurl, async:false,data:{action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, success: function(data) {beforeloop = data;return false}}).responseText;
	        else if(xydac_codemirror_name[i]=='customhtml')
	        	jQuery.ajax({type:'POST',url:ajaxurl, async:false,data:{action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, success: function(data) {customhtml = data;return false}}).responseText;
	        else if(xydac_codemirror_name[i]=='afterloop')
	        	jQuery.ajax({type:'POST',url:ajaxurl, async:false,data:{action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, success: function(data) {afterloop = data;return false}}).responseText;
	        else if(xydac_codemirror_name[i]=='customcss')
	        	jQuery.ajax({type:'POST',url:ajaxurl, async:false,data:{action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, success: function(data) {customcss = data;return false}}).responseText;
	        else if(xydac_codemirror_name[i]=='customscript')
	        	jQuery.ajax({type:'POST',url:ajaxurl, async:false,data:{action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, success: function(data) {customscript = data;return false}}).responseText;
	        
	        	/* if(xydac_codemirror_name[i]=='beforeloop')
	        		jQuery.post(ajaxurl, {action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, function(data) {beforeloop = data;return false});
	        	else if(xydac_codemirror_name[i]=='customhtml')
	        		jQuery.post(ajaxurl, {action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, function(data) {customhtml = data;return false});
	        	else if(xydac_codemirror_name[i]=='afterloop')
	        		jQuery.post(ajaxurl, {action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, function(data) {afterloop = data;return false});
	        	else if(xydac_codemirror_name[i]=='customcss')
	        		jQuery.post(ajaxurl, {action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, function(data) {customcss = data;return false});
	        	else if(xydac_codemirror_name[i]=='customscript')
	        		jQuery.post(ajaxurl, {action: 'xydac_cms_codemirror',val: xydac_codemirror[i].getValue()}, function(data) {customscript = data;return false}); */
	        }
	        var fullhtml = beforeloop+customhtml+afterloop;
	        var nm = document.getElementById("xydac_archive_type[name]").value;
	        preview.write("<html><head><style>"+customcss+"</style></head><body id='"+nm+"'>"+fullhtml+"</body></html>");
	        //preview.write("ll");
	        preview.close();
      	}
      setTimeout(updatePreview, 300);
SCRIPT;

		if(isset($_GET['codemirror'])&& ('1'==$_GET['codemirror'])){
			return $script."\n".$codemirror_script;
		}
		else
			return $script;
	}
	function xydac_cms_admin_script_url_func($script_url){
		if(isset($_GET['page']) && 0==strpos($_GET['page'],'xydac_ultimate')){
			$script_url.='&codemirror=1';
		}
		return $script_url;
	}
	function xydac_cms_admin_style_url_func($style_url){
		if(isset($_GET['page']) && 0==strpos($_GET['page'],'xydac_ultimate')){
			$style_url.='&codemirror=1';
		}
		return $style_url;
	}
	function xydac_core_rightfoot_func(){
		echo "<iframe id='xydac-codemirror-preview'></iframe>";
	}
	function wp_ajax_xydac_cms_codemirror_func(){
		$val = esc_attr($_POST['val']);
		echo (do_shortcode(stripslashes_deep(htmlspecialchars_decode($val,ENT_QUOTES))));
		die();
	}
}

?>