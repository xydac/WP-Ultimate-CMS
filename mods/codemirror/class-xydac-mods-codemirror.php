<?php
/*
Module Name:	Codemirror
Type:			Mod
Description:	Adds Codemirror Support to Ultimate CMS plugin. Codemirror allows you to view codes in text boxes in a more presentable way. It helps with color coding and indentation. Worth trying this out.
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		2.0
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
		add_filter('xydac_cms_admin_script',array($this,'xydac_cms_admin_script_func'),10,1);
	}
	function xydac_cms_admin_script_func($script)
	{	
		include $this->plugindir.'/codemirror.wp.js';
	}
}

?>