<?php 

class sidebar extends field_type{

	public function __construct($name,$label='',$desc='',$val='',$hasmultiple=false,$help='')
	{
		parent::__construct($name,array('label'=>$label,'desc'=>$desc,'val'=>$val,'hasmultiple'=>$hasmultiple,'help'=>$help));
		$this->ver = 2.0;
		$this->basic = true;
		$this->ftype = 'sidebar';
		$this->flabel = __('Side Bar',XYDAC_CMS_NAME);
	}

	function get_input($no="false",$val=false)
	{
		global $wp_registered_sidebars;
		$opt = array();
		if(is_string($no))
			$no = substr(uniqid(),0,8);
		foreach($wp_registered_sidebars as $o)
		{
			$name = $o['name'];
			$id = $o['id'];
			$opt[$id]=$name;
		}
		return combobox::get_combobox_input(array('name'=>$this->name."-".$no,'label'=>$this->label,'desc'=>$this->desc,'options'=>$opt),$val,"xydac_custom",true);

	}
	public function output($vals,$atts)
	{
		$atts = wp_specialchars_decode(stripslashes_deep($atts),ENT_QUOTES);
	 extract(shortcode_atts(array(
	 		'pre' => '',
	 		'before_element'=>'',
	 		'after_element'=>'',
	 		'post' => '',
	 ), $atts));

		$s = "";
		ob_start();
		foreach($vals as $val)
			if ( is_active_sidebar( $val ) )
			{
				echo wp_specialchars_decode($before_element);
				dynamic_sidebar( $val );
				echo wp_specialchars_decode($after_element);
			}
			$content= ob_get_contents();
			ob_end_clean();
			return wp_specialchars_decode($pre).$content.wp_specialchars_decode($post);

	}

}

?>