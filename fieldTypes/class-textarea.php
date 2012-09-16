<?php 

class textarea extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'textarea';
		$this->flabel = __('Text Area',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}
	public static function get_textarea_input( $args = array(), $value = false, $pre_arr=false, $create_old = false )
	{
		extract( $args );
		$r = '';
		if(isset($tabular) && $tabular){
			$r.='<tr class="form-field"><th scope="row" valign="top">';
		}
		$r.='<label for="'.$name.'">'.$label.'</label>';
		if(isset($tabular) && $tabular){
			$r.='</th><td>';
		}
		if($pre_arr)
			$r.='<p><textarea name="'.$pre_arr.'['.$name.']'.'" id="'.$name.'" cols="60" rows="4" >'.esc_html( $value, 1 ).'</textarea></p>';
		else
			$r.='<p><textarea name="'.$name.'" id="'.$name.'" cols="60" rows="4" >'.esc_html( $value, 1 ).'</textarea></p>';
		if($create_old)
			$r.='<input type="hidden" name="'.$name.'-old" value="'.esc_html( $value, 1 ).'" />';
		$r.='<p><span class="'.$name.'">'.$desc.'</span></p>';
		if(isset($tabular) && $tabular){
			$r.='</td></tr>';
		}
		return $r;
	}
	function get_input($no='false',$val=false,$tabular=false)
	{
		if(is_string($no))
			$no = substr(uniqid(),0,8);
		return self::get_textarea_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc),$val,"xydac_custom",true);
	}
	public function input($post_id)
	{
		global $post;
		$input = "";
		$val = get_post_meta($post_id, $this->name, false);
		if(is_array($val) && count($val)>0)
		{
			foreach($val as $k=>$v)
				$input.= $this->get_input($k,wp_htmledit_pre($v));
			if($this->hasmultiple)
				$input.= $this->get_input();
		}
		else
			$input.= $this->get_input();
		if($this->hasmultiple && $post->post_type=='page')
			return $input."<a href='#' class='xydac_add_more_page' id='".$this->name."'>".__('ADD MORE',XYDAC_CMS_NAME)."</a>";
		else if($this->hasmultiple)
			return $input."<a href='#' class='xydac_add_more' id='".$this->name."'>".__('ADD MORE',XYDAC_CMS_NAME)."</a>";
		else
			return $input;
	}

	public function saving(&$temp,$post_id,$val,$oval=null)
	{
		array_push($temp,update_post_meta($post_id, $this->name, $val,$oval));
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
		foreach($vals as $val)
			$s.=$before_element.$val.$after_element;
		return $pre.$s.$post;
	}

}

?>