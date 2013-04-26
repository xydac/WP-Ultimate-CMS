<?php 

class text extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 2.0;
		$this->basic = true;
		$this->ftype = 'text';
		$this->flabel = __('Text Box',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}
	public static function get_text_input( $args = array(), $value = false, $pre_arr=false, $create_old = false )
	{
		extract( $args );
		$r='';
		if(isset($tabular) && $tabular){
			$r.='<tr class="form-field"><th scope="row" valign="top">';
		}
		$r.='<label for="'.$name.'">'.$label.'</label><p>';
		if(isset($tabular) && $tabular){
			$r.='</th><td>';
		}
		if($pre_arr)
			$r.='<input type="text" name="'.$pre_arr.'['.$name.']'.'" id="'.$name.'" value="'.esc_html( $value, 1 ).'" />';
		else
			$r.='<input type="text" name="'.$name.'" id="'.$name.'" value="'.esc_html( $value, 1 ).'" />';
		if($create_old)
			$r.='<input type="hidden" name="'.$name.'-old" value="'.esc_html( $value, 1 ).'" />';
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
		return self::get_text_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc),$val,"xydac_custom",true);
	}


}

?>