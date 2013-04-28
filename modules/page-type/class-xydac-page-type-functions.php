<?php

class xydac_page_type_functions{

	/**
	 * The default constructor for creating all the actions and filters relating to page type.
	 *
	 */
	function __construct()
	{
		add_filter ('the_content',array($this,'the_content_manager'));
		//add_filter('xydac_cms_site_style',array($this,'xydac_cms_site_style_func'),11,1);
		//add_filter('xydac_cms_site_script',array($this,'xydac_cms_site_script_func'),11,1);
	}
	function xydac_cms_site_style_func($style)
	{
		global $xydac_cms_fields;
		$cpts = get_active_page_types();
		$st ="";
		foreach ($cpts as $cpt) {
			if(isset($cpt["content_css"]))
				$st.="\n/*============START PAGE TYPE ".$cpt['name']."=============================*/\n".$cpt["content_css"]."\n/*============END ".$cpt['name']."=============================*/\n";
		}
		return $style.$xydac_cms_fields['sitestyle'].$st;
	}
	function xydac_cms_site_script_func($script)
	{
		global $xydac_cms_fields;
		$cpts = get_active_page_types();
		$st ="";
		foreach ($cpts as $cpt) {
			if(isset($cpt["content_js"]))
				$st.="\n/*============START PAGE TYPE ".$cpt['name']."=============================*/\n".$cpt["content_js"]."\n/*============END ".$cpt['name']."=============================*/\n";
		}
		return $script.$xydac_cms_fields['sitescript'].$st;
	}
	function the_content_manager($content)
	{
		global $post;
		$content= trim($content);
		$post_type = get_post_type($post);

		$xydac_cpts = xydac_get_active_cpt();
		$con ="";
		if(is_array($xydac_cpts))
		{
			foreach($xydac_cpts as $xydac_cpt)
				if($xydac_cpt['name']==$post_type)
				{

					$con = do_shortcode(wp_specialchars_decode(stripslashes_deep($xydac_cpt['content_html']),ENT_QUOTES));

					$val='';

					if(preg_match("/\[CONTENT]/", trim($con), $scon)==0)
					{
						if(!post_type_supports( $post_type, 'editor' ))
							$val=$con;
						else
							$val = $content;
					}
					else
						$val = preg_replace("/\[CONTENT]/", $content, $con);
					return $val;
				}
		}
		return $content;
	}
}
?>