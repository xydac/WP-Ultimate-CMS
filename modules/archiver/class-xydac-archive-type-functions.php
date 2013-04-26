<?php
class xydac_archive_type_functions{

	function __construct()
	{
		add_filter('xydac_cms_site_style',array($this,'xydac_cms_site_style_func'),10,1);
		add_filter('xydac_cms_site_script',array($this,'xydac_cms_site_script_func'),10,1);
		add_shortcode('xydac_archive',array($this,'xydac_archive_shortcode'));
	}
	function xydac_cms_site_style_func($stuff)
	{
		$css = "";
		$cpts = xydac()->modules->archive_type->get_main();//get_option(XYDAC_CMS_ARCHIVE_OPTION);
		if(is_array($cpts))
			foreach($cpts as $cpt)
			{
				if(isset($cpt['customcss']))
					$css.= "\n/*--".$cpt['name']."--*/".$cpt['customcss'];
			}
			return $stuff.$css;
	}

	function xydac_cms_site_script_func($stuff)
	{
		$script ="";
		$cpts = xydac()->modules->archive_type->get_main();//get_option(XYDAC_CMS_ARCHIVE_OPTION);
		if(is_array($cpts))
			foreach($cpts as $cpt)
			{
				if(isset($cpt['customscript']))
					$script.= "\n/*--".$cpt['name']."--*/".$cpt['customscript'];
			}
			return $stuff.$script;
	}
	function xydac_archive_shortcode($atts, $text)
	{
		extract(shortcode_atts(array(
				'query' => '',
				'width' => '',
				'heading' => ''
		), $atts));
		$archives = stripslashes_deep(xydac()->modules->archive_type->get_main());//get_option(XYDAC_CMS_ARCHIVE_OPTION)
		if(is_array($archives))
			foreach($archives as $archive)
			if($archive['name']==$text)
			{
				if(isset($query)&& !empty($query))
					$query = trim($atts['query']);
				else
					$query = $archive['query'];
				if(isset($width)&& !empty($width))
					$archive['args']['width'] = trim($atts['width']);

				$archive['args']['name']= $text;
				$archive['args']['heading']= $heading;
					
				return xydac()->modules->archive_type->xydac_archiver($query,$archive['args']);
			}
	}

}