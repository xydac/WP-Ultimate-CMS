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
		if ( $title )
			echo $before_title . $title . $after_title;
		if(!empty($instance['taxonomy'])){
			$terms = get_terms( $instance['taxonomy']);
			if(!empty($terms)){
				echo "<ul>";
				foreach ( $terms as $term )
					echo '<li><a href="'.get_term_link($term->slug, $instance['taxonomy']).'">'.$term->name.'</a></li>';
				echo "</ul>";
			}
		}
		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','taxonomy' => '') );
		$title = $instance['title'];
		$taxonomy = $instance['taxonomy'];
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

<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => '','taxonomy' => ''));
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		return $instance;
	}

}
?>