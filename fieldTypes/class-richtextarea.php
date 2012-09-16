<?php 

class richtextarea extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'richtextarea';
		$this->flabel = __('Rich Text Area',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
		//$this->minwpver = 3.2;
	}
	public static function get_richtextarea_input( $args = array(), $value = null, $pre_arr=false, $create_old = false )
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
		$value = htmlspecialchars_decode($value, ENT_QUOTES);
		ob_start();
		if($pre_arr)
			wp_editor($value, $pre_arr.'['.$name.']',array('editor_class'=>'xeditor'));
		else
			wp_editor($value, $name,array('editor_class'=>'xeditor'));
			
		$content= ob_get_contents();
		ob_end_clean();
		$r.=$content;
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
		return self::get_richtextarea_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc),$val,"xydac_custom",true);
	}

}

?>