<?php
//@todo: remove the global style and script stuff from post type and place them globally
class xydac_post_type_functions{

	function __construct()
	{

		add_action( 'right_now_content_table_end', array($this,'xydac_right_now'));
		add_filter ('the_content',array($this,'the_content_manager'));
		add_filter ('enter_title_here',array($this,'xydac_enter_title_here_manager'));
		//@todo: enabling this creates problem with xml rpc
		//add_filter( 'pre_get_posts', array($this,'xydac_posts_home') );
		add_shortcode('post_title',array($this,'xydac_cms_get_post'));

	}
	function xydac_cms_get_post($atts)
	{
		global $post;
		return $post->post_title;
	}
	function the_content_manager($content)
	{

		global $post;
		$content= trim($content);
		$post_type = get_post_type($post);

		$xydac_cpts = xydac()->modules->post_type->get_active();//xydac_get_active_cpt();
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

	function xydac_enter_title_here_manager($content)
	{
		global $post;
		$cpts = xydac()->modules->post_type->get_active();//xydac_get_active_cpt();
		if(is_array($cpts))
			foreach($cpts as $cpt)
			if($cpt['name']==get_post_type($post))
			if(isset($cpt['args']['labels']['enter_text_here']) && !empty($cpt['args']['labels']['enter_text_here']))
			return $cpt['args']['labels']['enter_text_here'];
		return $content;
	}

	function xydac_posts_home( $query ) {
		if ( !is_preview() && !is_admin() && !is_singular() && !is_404() ) {
			$args = array(
					'public' => true ,
					'_builtin' => false
			);
			$post_types = get_post_types( $args );
			$post_types = array_merge( $post_types , array( 'post' ) );
			$my_post_type = get_query_var( 'post_type' );
			if ( empty( $my_post_type ) )
				$query->set( 'post_type' , $post_types );
		}
	}

	function xydac_right_now() {
		$cpts = xydac()->modules->post_type->get_active();//xydac_get_active_cpt();
		if (is_array($cpts) && !empty($cpts))
			foreach ($cpts  as $cpt )
			{
				$num_posts = wp_count_posts( $cpt['name'] );
				$num = number_format_i18n( $num_posts->publish );
				if(!empty($cpt['args']['label']))
					$text = _n( $cpt['args']['label'], $cpt['args']['label'], intval($num_posts->publish) );
				else
					$text = _n( $cpt['name'], $cpt['name'], intval($num_posts->publish) );
				if ( current_user_can( 'edit_posts' ) ) {
					$num = "<a href='edit.php?post_type=".$cpt['name']."'>$num</a>";
					$text = "<a href='edit.php?post_type=".$cpt['name']."'>$text</a>";
				}
				echo '<td class="first b b-'.$cpt['name'].'">' . $num . '</td>';
				echo '<td class="t '.$cpt['name'].'">' . $text . '</td>';
				echo '</tr>';

			}
	}
}
?>