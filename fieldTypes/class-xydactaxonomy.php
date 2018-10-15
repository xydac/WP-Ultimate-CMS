<?php 

class xydactaxonomy extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'xydactaxonomy';
		$this->flabel = __('Taxonomy Combo Box',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}

	function get_input($no='false',$val=false,$tabular=false)
	{
		$term_list = get_terms( $this->value, 'hide_empty=0' );

		if($term_list && !is_wp_error($term_list)){
			$count = count($term_list);
			$option_list=array();
			if ($count > 0) {
				foreach ($term_list as $term) {
					$option_list[$term->slug]=$term->name;
				}
				if(is_string($no))
					$no = substr(uniqid(),0,8);
				return combobox::get_combobox_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc,'options'=>$option_list),$val,"xydac_custom",true);
			}
		}
	}

	public function output($vals,$atts)
	{
		extract(shortcode_atts(array(
				'pre' => '',
				'before_element'=>'',
				'after_element'=>'',
				'post' => '',
		), $atts));

		$s = "";
		foreach($vals as $val){

			$this_term = get_term_by('slug', $val, $this->value);
			if($this_term)
				$s.=$before_element."<a href='".get_term_link($val,$this->value)."'>".$this_term->name."</a>".$after_element;
		}
		if(''!=$s)
			return $pre.$s.$post;
		else
			return $s;
	}

	public function saving(&$temp,$post_id,$val,$oval='')
	{
		if(esc_attr(stripslashes($val))==esc_attr(stripslashes($oval)))
			return;
		if($this->hasmultiple)
		{
			$v= esc_attr(stripslashes($val));
			if(empty($oval) && !empty($v))
				array_push($temp,add_post_meta($post_id, $this->name, $v));
			else if(!empty($oval) && empty($v))
				array_push($temp,delete_post_meta($post_id, $this->name, $oval)); 
			else
				array_push($temp,update_post_meta($post_id, $this->name, esc_attr(stripslashes($val)),esc_attr(stripslashes($oval))));
			
		}
		else{
			array_push($temp,update_post_meta($post_id, $this->name, esc_attr(stripslashes($val)),esc_attr(stripslashes($oval))));
			wp_set_post_terms( $post_id, $val, $this->value, false ) ;
		}

	}



}

?>