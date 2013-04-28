<?php
class xydac_taxonomy_functions{

	public function __construct()
	{
		add_action( 'init' , array($this , 'xydac_taxonomy_define') , 11 );
		add_action( 'admin_menu', array($this,'xydac_meta_boxes'));
		add_action( 'save_post', array($this,'xydac_save_tax'));
		add_action( 'widgets_init',array($this,'xydac_taxonomy_widgets_init'));
		add_action( 'manage_posts_custom_column', array($this,'xydac_taxonomy_custom_cols'), 10, 2);
		add_action( 'restrict_manage_posts',array($this,'xydac_restrict_manage_posts'));
		add_filter( 'posts_where' , array($this,'xydac_posts_where'),10,1 );
		add_filter( 'manage_posts_columns', array($this,'xydac_taxonomy_cols'),10,2 );

	}

	/**
	 * This method adds custom fields to various taxonomies already defined.
	 */
	function xydac_taxonomy_define(){
		$taxonomies =  xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
		if(is_array($taxonomies))
			foreach($taxonomies as $taxonomy)
			{
				$fields = xydac()->modules->taxonomy_type->get_field($taxonomy['name']);//get_taxonomy_fields

				if(is_array($fields)){
					foreach($fields as $ct_row)
						new ct_fields($taxonomy['name'],$ct_row['field_name'],$ct_row['field_label'],$ct_row['field_type'],$ct_row['field_desc'],$ct_row['field_val']);
				}else
					new ct_fields($taxonomy['name']);
			}

	}
	function xydac_taxonomy_widgets_init(){
		register_widget('Xydac_Tax_Term_List_Widget');
		register_widget('Xydac_Tax_Detail_Widget');
	}

	// deletes metaboxes from post edit screen and adds new metabox
	function xydac_meta_boxes(){

		$taxonomies =  xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
		if (is_array($taxonomies) && !empty($taxonomies))
			foreach ($taxonomies  as $taxonomy )
			{

				if($taxonomy['showascombobox']=='true' && is_array($taxonomy['object_type']) && isset(xydac()->modules->post_type))
				{
					if(xydac()->modules->taxonomy_type->xydac_checkbool($taxonomy['args']['hierarchical']))
					{
						foreach($taxonomy['object_type'] as $a)
							remove_meta_box($taxonomy['name'].'div',$a,'core');
					}
					else
					{
						foreach($taxonomy['object_type'] as $a)
							remove_meta_box('tagsdiv-'.$taxonomy['name'],$a,'core');

					}
					foreach($taxonomy['object_type'] as $a)
					{
						//@todo: dont know what fields actually do
						$fields = xydac()->modules->post_type->get_field($a);//getCptFields($a);
						if(!is_array($fields) || empty($fields))
							add_meta_box($taxonomy['name'].'_box', $taxonomy['args']['label'], array($this,'xydac_meta_handler'), $a, 'side', 'low', $taxonomy['name']);
					}
				}
			}
	}
	//handles creation of meta box
	function xydac_meta_handler($post,$tax) {
		$xy_terms = get_terms($tax['args'], 'hide_empty=0');
		$val = wp_get_object_terms($post->ID, $tax['args']);
		wp_nonce_field( "XYDAC_CMS", 'xydac_cms_field_nonce' );
		?>
<input
	type="hidden" name="xydac_taxonomy_hidden[]"
	value="<?php echo $tax['args']; ?>" />
<select name='<?php echo $tax['args']; ?>'
	id='<?php echo $tax['args']; ?>' style="width: 95%">
	<option class='<?php echo $tax['args']; ?>-option' value=''
	<?php if (!count($val)) echo "selected";?>>None</option>
	<?php
	foreach ($xy_terms as $xy_term) {
		if (!is_wp_error($val) && !strcmp($xy_term->slug, $val[0]->slug) && !empty($val) )
			echo "<option class='".$tax['args']."-options' value='" . $xy_term->slug . "' selected>" . $xy_term->name . "</option>\n";
		else
			echo "<option class='".$tax['args']."-options' value='" . $xy_term->slug . "'>" . $xy_term->name . "</option>\n";
	}
	?>
</select>
<?php
	}

	//handles saving of metabox data
	function xydac_save_tax( $post_id ) {
		if (isset($_POST['xydac_cms_field_nonce']) && wp_verify_nonce( $_POST['xydac_cms_field_nonce'],"XYDAC_CMS" ))
		{
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return $post_id;
			if (isset($_POST['post_type']) && 'page' == $_POST['post_type'] )
			{
				if ( !current_user_can( 'edit_page', $post_id ) )
					return $post_id;
			} else {
				if ( !current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}
			$post = get_post($post_id);
			if(isset($_POST['xydac_taxonomy_hidden']))
				if(is_array($_POST['xydac_taxonomy_hidden']))
				foreach($_POST['xydac_taxonomy_hidden'] as $a=>$t)
				{
					$temp = $_POST[$t];
					if($post->post_type != 'revision')
						wp_set_object_terms( $post_id, $temp , $t );
				}
				return $temp;
		}
		else
			return $post_id;
	}


	//--------------------


	function array_insert(&$array, $insert, $position) {
		settype($array, "array");
		settype($insert, "array");
		settype($position, "int");

		if($position==0) {
			$array = array_merge($insert, $array);
		} else {
			if($position >= (count($array)-1)) {
				$array = array_merge($array, $insert);
			} else {
				$head = array_slice($array, 0, $position);
				$tail = array_slice($array, $position);
				$array = array_merge($head, $insert, $tail);
			}
		}
	}

	function xydac_taxonomy_cols($columns,$post_type) {
		$taxonomies = xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
		if (is_array($taxonomies) && !empty($taxonomies))
			foreach ($taxonomies  as $taxonomy )
			{
				if(is_array($taxonomy['object_type']))
					if(in_array($post_type,$taxonomy['object_type']))
					{
						$label = (!empty($taxonomy['args']['labels']['name'])  ? $taxonomy['args']['labels']['name'] : ( !empty($taxonomy['args']['label']) ? $taxonomy['args']['label'] : $taxonomy['name']));
						$this->array_insert($columns,array($taxonomy['name'] => __($label)),2);
					}
			}
			return $columns;
	}
	function xydac_taxonomy_custom_cols($column_name, $post_id)
	{
		$taxonomies = xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
		if (is_array($taxonomies) && !empty($taxonomies))
			foreach ($taxonomies  as $taxonomy )
			{
				if( $column_name == $taxonomy['name'] ) {
					if($terms = get_the_term_list( $post_id, $taxonomy['name'], '', ', ', '' )){
						echo $terms;
					} else
						echo '<i>'.__('None').'</i>';
						
				}
			}
				
	}

	//-----------------------
	//--code from DGB

	function xydac_restrict_manage_posts() {
		global $typenow;
		$taxonomies = xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
		if (is_array($taxonomies) && !empty($taxonomies))
			foreach ($taxonomies  as $taxonomy )
			{
				if(isset($taxonomy['object_type']) && is_array($taxonomy['object_type']))
					if (in_array($typenow,$taxonomy['object_type']))
					echo $this->xydac_print_html($taxonomy['name']);
			}

	}

	function xydac_print_html($taxonomy_name) {
		$taxonomy = get_taxonomy($taxonomy_name);
		$terms = get_terms($taxonomy_name);
		$label = "Show All {$taxonomy->label}";
		$html = array();
		$html[] = "<select id=\"$taxonomy_name\" name=\"$taxonomy_name\">";
		$html[] = "<option value=\"0\">$label</option>";
		$this_term = get_query_var($taxonomy_name);
		foreach($terms as $term) {
			$default = ($this_term==$term->slug ? ' selected="selected"' : '');
			$value = esc_attr($term->name);
			$html[] = "<option value=\"{$term->slug}\"$default>$value</option>";
		}
		$html[] = "</select>";
		return implode("\n",$html);
	}
	function xydac_posts_where($where)
	{
		if( is_admin() )
		{
			global $wpdb;
			$taxonomies = xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
			if(is_array($taxonomies))
				foreach($taxonomies as $taxonomy)
				{
					$tax = $taxonomy['name'];
					if(isset($_GET[$tax]) && !empty($_GET[$tax]) && intval($_GET[$tax])!=0)
					{
						$terms = get_terms($tax,'slug='.$_GET[$tax]);
						if(count($terms)>0)
							foreach($terms as $term)
							$where.=" OR ID IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id={$term->term_taxonomy_id})";
					}
				}
					
		}
		return $where;
	}

}