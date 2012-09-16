<?php 
class Xydac_Tax_Detail_Widget extends WP_Widget {
	private $active_taxonomies;
	function __construct() {
		$widget_ops = array('classname' => 'xydac_taxonomy_detail', 'description' => __( "A Taxonomy Details Viewer Widget") );
		parent::__construct('xydactaxdetail', __('Xydac Taxonomy Detail'), $widget_ops);
		$this->active_taxonomies = xydac()->modules->taxonomy_type->get_active();//xydac_get_active_taxonomy();
	}

	function widget( $args, $instance ) {
		global $post;
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$templatehtml = $instance['templatehtml'];

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		if(!empty($instance['taxonomy'])){
			if(empty($templatehtml)){
				$tax = xydac()->modules->taxonomy_type->get_main(array('values'=>array('name'=>$instance['taxonomy']),'is_value_array'=>'true'));//xydac_get_taxonomy($instance['taxonomy']);
				$content = $tax['content_html'];
				echo make_clickable(do_shortcode(wp_specialchars_decode(stripslashes_deep($content),ENT_QUOTES)));
			}else{
				echo make_clickable(do_shortcode(wp_specialchars_decode(stripslashes_deep($templatehtml),ENT_QUOTES)));
			}
		}

		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','taxonomy' => '','templatehtml'=>'') );
		$title = $instance['title'];
		$taxonomy = $instance['taxonomy'];
		$templatehtml = $instance['templatehtml'];
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
	<label for="<?php echo $this->get_field_id('templatehtml'); ?>"><?php _e('Template:'); ?>
		<textarea class="widefat"
			id="<?php echo $this->get_field_id('templatehtml'); ?>" rows="8"
			cols="28" name="<?php echo $this->get_field_name('templatehtml'); ?>"> <?php echo esc_attr($templatehtml); ?>
		</textarea> </label>
</p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => '','taxonomy' => '','templatehtml'=>''));
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		$instance['templatehtml'] = $new_instance['templatehtml'];
		return $instance;
	}

}
?>