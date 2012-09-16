<?php

/*
 <<NOT USING THIS NOW>>
Copied from http://vocecommunications.com/blog/2010/11/adding-rewrite-rules-for-custom-post-types/
*/
class Custom_Post_Type_With_Rewrite_Rules {

	private $post_type;
	private $query_var;
	private $permalink_prefix;
	private $permalink_structure;

	/**
	 * Constructor method
	 *
	 * $permalink_args options:
	 * -front: The front of the permalinks for this post type.  All URLs for this post type will start with this
	 * -structure: The structure of the permalink.  Accepts the following tags: %year%, %month%, %day% and %{query_var}%, the structure must contain the query var tag
	 *
	 * @param string $post_type
	 * @param array $post_type_args Arguments normally passed into register_post_type
	 * @param array $permalink_args Arguments controlling the permalink structure.
	 */
	public function __construct($post_type, $post_type_args = array(), $permalink_args = array()) {
		//make sure the rewrite settings for the post type are set to false to prevent interference
		$post_type_args['rewrite'] = false;

		//register the post type and get the returned args
		$post_type_args = register_post_type($post_type, $post_type_args);

		if('' == get_option('permalink_structure') || !$post_type_args->publicly_queryable) {
			return; //only continue if using permalink structures and post type is publicly queryable
		}

		$this->post_type = $post_type_args->name;
		$this->query_var = $post_type_args->query_var;

		$default_permalink_args = array(
				'structure' => '%year%/%monthnum%/%day%/%'.$this->query_var.'%/',
				'front' => $this->post_type
		);

		$permalink_args = wp_parse_args($permalink_args, $default_permalink_args);

		$this->permalink_prefix = trim($permalink_args['front'], '/');
		$this->permalink_structure = trailingslashit(ltrim($permalink_args['structure'], '/'));

		//register the add_rewrite_rules method to run only when rules are being flushed.
		add_action('delete_option_rewrite_rules', array($this, 'add_rewrite_rules'));

		//go ahead and add the rewrite rules if the option is currently empty
		$current_rules = get_option('rewrite_rules');
		if(empty($current_rules)) {
			$this->add_rewrite_rules();
		}

		//add a filter to fix the url for this post type
		add_filter('post_type_link', array($this, 'filter_post_type_link'), 10, 4);

	}

	public function add_rewrite_rules() {
		global $wp_rewrite;

		//register the rewrite tag to use for the post type
		$wp_rewrite->add_rewrite_tag('%'.$this->query_var.'%', '([^/]+)', $this->query_var . '=');

		//we use the WP_Rewrite class to generate all the endpoints WordPress can handle by default.
		$rewrite_rules = $wp_rewrite->generate_rewrite_rules($this->permalink_prefix.'/'.$this->permalink_structure, EP_ALL, true, true, true, true, true);

		//build a rewrite rule from just the prefix to be the base url for the post type
		$rewrite_rules = array_merge($wp_rewrite->generate_rewrite_rules($this->permalink_prefix), $rewrite_rules);
		$rewrite_rules[$this->permalink_prefix.'/?$'] = 'index.php?paged=1';
		foreach($rewrite_rules as $regex => $redirect) {
			if(strpos($redirect, 'attachment=') === false) {
				//add the post_type to the rewrite rule
				$redirect .= '&post_type=' . $this->post_type;
			}

			//turn all of the $1, $2,... variables in the matching regex into $matches[] form
			if(0 < preg_match_all('@\$([0-9])@', $redirect, $matches)) {
				for($i = 0; $i < count($matches[0]); $i++) {
					$redirect = str_replace($matches[0][$i], '$matches['.$matches[1][$i].']', $redirect);
				}
			}
			//add the rewrite rule to wp_rewrite
			$wp_rewrite->add_rule($regex, $redirect, 'top');
		}
	}

	/**
	 * Filter to turn the links for this post type into ones that match our permalink structure
	 *
	 * @param string $permalink
	 * @param object $post
	 * @return string New permalink
	 */
	public function filter_post_type_link($permalink, $post) {
		if(($this->post_type == $post->post_type) && '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
			$rewritecode = array(
					'%year%',
					'%monthnum%',
					'%day%',
					'%hour%',
					'%minute%',
					'%second%',
					'%post_id%',
					'%author%',
					'%'.$this->query_var.'%'
			);

			$author = '';
			if ( strpos($this->permalink_structure, '%author%') !== false ) {
				$authordata = get_userdata($post->post_author);
				$author = $authordata->user_nicename;
			}

			$unixtime = strtotime($post->post_date);
			$date = explode(" ",date('Y m d H i s', $unixtime));
			$rewritereplace = array(
					$date[0],
					$date[1],
					$date[2],
					$date[3],
					$date[4],
					$date[5],
					$post->ID,
					$author,
					$post->post_name,
			);
			$permalink = str_replace($rewritecode, $rewritereplace, '/'.$this->permalink_prefix.'/'.$this->permalink_structure);
			$permalink = user_trailingslashit(home_url($permalink));
		}
		return $permalink;
	}
}






/**
 * Public registration method for custom post types with rewrite rules.
 *
 * $permalink_args options:
 * -front: The front of the permalinks for this post type.  All URLs for this post type will start with this
 * -structure: The structure of the permalink.  Accepts the following tags: %year%, %month%, %day% and %{query_var}%, the structure must contain the query var tag
 *
 * @param string $post_type
 * @param array $post_type_args Arguments normally passed into register_post_type
 * @param array $permalink_args Arguments controlling the permalink structure.
 */
function register_post_type_with_rewrite_rules($post_type, $post_type_args = array(), $permalink_args = array()) {
	new Custom_Post_Type_With_Rewrite_Rules($post_type, $post_type_args, $permalink_args);
}

?>