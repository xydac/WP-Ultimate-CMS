<?php
class xydac_archive_type_functions{

	function __construct()
	{
		add_shortcode('xydac_archive',array($this,'xydac_archive_shortcode'));
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