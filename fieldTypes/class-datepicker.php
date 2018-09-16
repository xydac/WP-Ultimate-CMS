<?php 

class datepicker extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 3.0;
		$this->basic = true;
		$this->ftype = 'datepicker';
		$this->flabel = __('Date Picker',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
	}
	public static function get_datepicker_input( $args = array(), $value = false, $pre_arr=false, $create_old = false )
	{
		extract( $args );
		$r='';
		if(isset($tabular) && $tabular){
			$r.='<tr class="form-field"><th scope="row" valign="top">';
		}
		$r.='<label for="'.$name.'">'.$label.'</label>';
		if(isset($tabular) && $tabular){
			$r.='</th><td>';
		}
		$r.='<p class="xydac-custom-meta">';
		if($pre_arr)
			$r.='<input type="date" name="'.$pre_arr.'['.$name.']'.'" id="'.$name.'" value="'.esc_html( $value, 1 ).'" />';
		else
			$r.='<input type="date" name="'.$name.'" id="'.$name.'" value="'.esc_html( $value, 1 ).'" />';
		if($create_old)
			$r.='<input type="hidden" name="'.$name.'-old" value="'.esc_html( $value, 1 ).'" />';
		
		if(isset($desc) && strlen($desc)>0)
			$r.='<a class="xydactooltip" href="#" ><span style="width: 180px;" class="info '.$name.'">'.$desc.'</span></a>';
		$r.='</p>';
		$r.='<div rel="'.$name.'" class="clear"></div>';
		if(isset($tabular) && $tabular){
			$r.='</td></tr>';
		}
		return $r;
	}
	function get_input($no='false',$val=false,$tabular=false)
	{
		if(is_string($no))
			$no = substr(uniqid(),0,8);
		return self::get_datepicker_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc),$val,"xydac_custom",true);
	}


}

?>