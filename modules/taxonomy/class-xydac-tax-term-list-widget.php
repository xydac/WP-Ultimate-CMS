<?php 
class Xydac_Tax_Term_List_Widget extends WP_Widget {
	private $active_taxonomies;
	function __construct() {
		$widget_ops = array('classname' => 'xydac_taxonomy_term_list', 'description' => __( "A Taxonomy Term List Widget") );
		parent::__construct('xydactaxtermlist', __('Xydac Taxonomy Term List'), $widget_ops);
		$this->active_taxonomies = xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
	}

	function widget( $args, $instance ) {
		global $post;
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		$showascombobox =  $instance['showascombobox'];
		if ( $title )
			echo $before_title . $title . $after_title;
		if(!empty($instance['taxonomy'])){
			$terms = get_terms( $instance['taxonomy']);
			
			if(!$showascombobox && !empty($terms)){
				
				echo "<ul>";
				foreach ( $terms as $term )
					echo '<li><a href="'.get_term_link($term->slug, $instance['taxonomy']).'">'.$term->name.'</a></li>';
				echo "</ul>";
			}else if($showascombobox && !empty($terms)){ 
				
					$select =  '<form action="'.get_bloginfo('url').'" method="get">';
					$select.= "<select name='".$instance['taxonomy']."' id='cat' class='postform'>n";
					$select.= "<option value='-1'>Select ".$instance['taxonomy']."</option>";
					$tax = $instance['taxonomy'];
					foreach ( $terms as $term ){
						if($term->count > 0){
							if($_GET[$tax]==$term->slug)
								$select.= "<option value='".$term->slug."' selected>".$term->name."</option>";
							else 
								$select.= "<option value='".$term->slug."'>".$term->name."</option>";
						}
					}
					
					$select.= "</select>";
				echo $select;
				echo '<input type="submit" name="submit" value="view" />';
				echo '</form>';
				
			}
		}
		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','taxonomy' => '','showascombobox' => false) );
		$title = $instance['title'];
		$taxonomy = $instance['taxonomy'];
		$showascombobox = $instance['showascombobox'];
		?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
		<input class="widefat"
		id="<?php echo $this->get_field_id('title'); ?>"
		name="<?php echo $this->get_field_name('title'); ?>" type="text"
		value="<?php echo esc_attr($title); ?>" /> </label>
</p>
<p>
	<label for="<?php echo $this->get_field_id('taxonomy'); ?>"><?php _e('Taxonomy:'); ?>
		<select class="widefat"
		id="<?php echo $this->get_field_id('taxonomy'); ?>"
		name="<?php echo $this->get_field_name('taxonomy'); ?>">
			<?php foreach($this->active_taxonomies as $tax){ ?>
			<option value="<?php echo $tax['name']?>"
			<?php if(esc_attr($taxonomy)==$tax['name']) echo "SELECTED"; ?>>
				<?php echo $tax['args']['label']?>
			</option>
			<?php } ?>
	</select> </label>
</p>
<p>
	<label for="<?php echo $this->get_field_id('showascombobox'); ?>"><?php _e('Show as Combobox:'); ?>
		 <input class='checkbox'
		id="<?php echo $this->get_field_id('showascombobox'); ?>"
		name="<?php echo $this->get_field_name('showascombobox'); ?>" type="checkbox"
		<?php checked($instance['showascombobox'], true) ?> /></label>
</p>

<?php
	}

	function update( $new_instance, $old_instance ) {
		$new_instance = (array) $new_instance;
		$instance = wp_parse_args((array) $new_instance, array( 'title' => '','taxonomy' => '','showascombobox' => 0));
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		if(isset($new_instance['showascombobox']))
			$instance['showascombobox'] = 1;
		return $instance;
	}

}
?>