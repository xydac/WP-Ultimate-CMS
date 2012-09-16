<?php 

class radiobutton extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'radiobutton';
		$this->flabel = __('Radio Button',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}
	public static function get_radiobutton_input( $args = array(), $value = false, $pre_arr=false, $create_old = false )
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
		$r.='<p class="xydac-custom-meta">';
		if($pre_arr)
		{
			foreach ( $options as $key=>$option )
				if ($key==$value)
				$r.="<input id='".$key."' type='radio' name='".$pre_arr.'['.$name.']'."' value='".$key."' checked='checked'/><label class='radio'  for='".$key."'>".$option."</label>";
			else
				$r.="<input id='".$key."' type='radio' name='".$pre_arr.'['.$name.']'."' value='".$key."'/><label class='radio' for='".$key."'>".$option."</label>";

		}
		else
		{
			foreach ( $options as $key=>$option )
				if ($key==$value)
				$r.="<input id='".$key."' type='radio' name='".$name."' value='".$key."' checked='checked'/><label class='radio'  for='".$key."'>".$option."</label>";
			else
				$r.="<input id='".$key."' type='radio' name='".$name."' value='".$key."'/><label class='radio' for='".$key."'>".$option."</label>";

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
		return self::get_radiobutton_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc,'options'=>$this->get_options()),$val,"xydac_custom",true);
	}

}

?>