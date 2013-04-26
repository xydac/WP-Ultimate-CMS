<?php 
//@TODO: change the code for saving
class checkbox extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'checkbox';
		$this->flabel = __('Check Box',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}
	public static function get_checkbox_input( $args = array(), $value = false, $pre_arr=false, $create_old = false )
	{
		extract( $args );
		$r = '';
		if(isset($tabular) && $tabular){
			$r.='<tr class="form-field"><th scope="row" valign="top">';
		}
		if($value)
			$value = explode(',',$value);
		else
			$value=array();
		$r.='<label for="'.$name.'">'.$label.'</label>';
		if(isset($tabular) && $tabular){
			$r.='</th><td>';
		}
		$r.='<p class="xydac-custom-meta">';
		if($pre_arr)
		{
			if(is_array($options))
				foreach ( $options as $key=>$option )
				if ( in_array($key,$value))
				$r.="<input id='".$key."' type='checkbox' name='".$pre_arr.'['.$name.']'."[".$key."]' value='".$key."' checked='checked'/><label class='checkbox'  for='".$key."'>".$option."</label>";
			else
				$r.="<input id='".$key."' type='checkbox' name='".$pre_arr.'['.$name.']'."[".$key."]' value='".$key."'/><label class='checkbox' for='".$key."'>".$option."</label>";

		}
		else
		{
			if(is_array($options))
				foreach ( $options as $key=>$option )
				if ( in_array($key,$value))
				$r.="<input id='".$key."' type='checkbox' name='".$name."[".$key."]' value='".$key."' checked='checked'/><label class='checkbox'  for='".$key."'>".$option."</label>";
			else
				$r.="<input id='".$key."' type='checkbox' name='".$name."[".$key."]' value='".$key."'/><label class='checkbox' for='".$key."'>".$option."</label>";

		}
		if($create_old)
			$r.='<input type="hidden" name="'.'['.$name.'-old]'.'" value="'.esc_html( $value, 1 ).'" />';
		$r.='</p><p><span class="'.$name.'">'.$desc.'</span></p>';
		if(isset($tabular) && $tabular){
			$r.='</td></tr>';
		}
		return $r;
	}
	function get_input($no='false',$val=false,$tabular=false)
	{
		if(is_string($no))
			$no = substr(uniqid(),0,8);
		return self::get_checkbox_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc,'options'=>$this->get_options()),$val,"xydac_custom",true);
	}
	function saving(&$temp,$post_id,$val,$oval='')
	{
		$str="";
		if(is_array($val))
			foreach($val as $k=>$v)
			$str.=$k.",";
		$val = substr($str,0,-1);
		array_push($temp,update_post_meta($post_id, $this->name, esc_attr($val),esc_attr($oval)));
	}
}

?>