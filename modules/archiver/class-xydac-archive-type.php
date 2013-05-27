<?php
/*
 Module Name:	Archive
Type:			Module
Description:	Xydac Archive module
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-archive-type-functions.php';
include 'class-xydac-archive-type-manager.php';
class xydac_archive_type extends xydac_cms_module{

	function __construct()
	{
		parent::__construct('archive_type',array('module_label'=>'Xydac Archives',
				'has_custom_fields'=>false,
				'uses_active'=>false,
				'registered_option'=>array('main'=>'xydac_archive'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms_archive_type',
				'menu_position'=>'top',
				'custom_css_id'=>'customcss','custom_jss_id'=>'customscript'
		));
		new xydac_archive_type_functions();


	}

	function xydac_archiver($query,$args)
	{

		global $post;
		ob_start();
		$temp_html = '<a href="[x_permalink]"><div class="xydac_box">';
		$temp_html .= '<div class="h4wrap"><h4>[x_title]</h4></div>';
		$temp_html .= '<p>[x_excerpt]</p>';
		$temp_html .= '</div></a>';
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args_default = array( 'width'=>'100%','cols'=>1,'rows'=>1,'beforeloop'=>'','afterloop'=>'','customhtml'=>$temp_html, 'tablemode'=>false,'paged'=>$paged);
		$args = array_merge($args_default,$args);
		extract(stripslashes_deep($args));
		//query_posts(do_shortcode($query));
		
		//--new code
		if(substr($query,0,5)=='array'){
			$arr = eval('return serialize('. $query . ');');
			$arr = unserialize($arr);
			if(is_array($arr))
				$query = urldecode(http_build_query($arr));
		}
		//--new code
		$xydac_query = new WP_Query(do_shortcode($query));
		if(!is_wp_error($xydac_query)){
			if('false'==$tablemode)
				$tablemode=false;
			else
				$tablemode = true;
			$counter = 0;
			$row = 0;
			$col = 0;
			$out = "";
			if(empty($cols))
				$cols = 1;
			if(empty($width))
				$width= "100%";
			if(empty($customhtml))
				$customhtml = $temp_html;
			$divwidth = 100/$cols;
			$out.= '<div class="xydac_main_wrapper" id="'.$name.'" style="width:'.$width.'">';

			$pattern = array('/\[x_heading\]/');
			$replacement = array($heading);
			$beforeloopresult = preg_replace($pattern, $replacement, $beforeloop);
			$out.= do_shortcode($beforeloopresult);
			while ($xydac_query->have_posts()) : $xydac_query->the_post();

			$counter++;

			if(!$tablemode)
				if ($row ==0)
				{
					$out .= '<div class="xydac_mainbox_cols" style="width:'.$divwidth.'%">';$col++;
				}
				$row++;
				//--
				$x_permalink = get_permalink();
				$x_title = get_the_title();
				$x_excerpt = get_the_excerpt();
				$x_content = get_the_content();
				$x_date = get_the_date();
				$x_time = get_the_time();

				$patterns = array('/\[x_permalink\]/','/\[x_title\]/','/\[x_excerpt\]/','/\[x_date\]/','/\[x_time\]/','/\[x_heading\]/');
				$replacements = array($x_permalink,$x_title,$x_excerpt,$x_date,$x_time,$heading);
				$result = preg_replace($patterns, $replacements, $customhtml);


				//--
				$out.= do_shortcode($result);
				//$out.= do_shortcode('[xydac_page_field]contact-person[/xydac_page_field]');
				if(!$tablemode)
					if ($row%$rows ==0)
					{

						$out.= '</div>';$row=0;
						if ($col%$cols==0)
						{
							$out .= '<div class="xydac_clear"></div>';
						}

					}

					endwhile;
					if($row!=0 && !$tablemode)
						$out.='</div><div class="xydac_clear"></div>';
					$out.= do_shortcode($afterloop);
					$out.='</div>';
					if(!$tablemode)
						$out .= '<div class="xydac_clear"></div>';
					echo $out;
					$list = ob_get_clean();
					wp_reset_postdata();
					return $list;


					//wp_reset_query();
					wp_reset_postdata();
		}
	}
}

?>