<?php 

class nivoslider extends field_type{
	private $img;
	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = false;
		$this->ftype = 'nivoslider';
		$this->flabel = __('Nivo Slider',XYDAC_CMS_NAME);
		$this->hasmultiple = true;
		$this->compaitable = array('pagetype','posttype');
		$this->img = new image($this->name,$this->label,$this->desc,"",true);
	}


	function get_input($no='false',$val=false,$tabular=false)
	{
		if(is_string($no))
			$no = substr(uniqid(),0,8);
		return image::get_image_input(array('name'=>$this->name."-".$no,'tabular'=>$tabular,'label'=>$this->label,'desc'=>$this->desc),$val,"xydac_custom",true);
	}



	public function output($vals,$atts)
	{

		$s = "";
		foreach($vals as $val)
		{
			$val = wp_specialchars_decode($val,ENT_QUOTES);
			if (preg_match('/\A(?:\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$])\Z/i', $val))
				$val='<img src="'.$val.'" />';
			else
				$val=do_shortcode(wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES));
			$s.=$val;
		}
		//$s='<ul id="xydacnivoslider" class="nivoSlider">'.$s.'</ul>';
		if(isset($this->value['theme']))
			$theme = $this->value['theme'];
		else
			$theme = 'default';
		if(is_array($vals) && count($vals)>1){
		$s= '

		<div class="slider-wrapper theme-'.$theme.'">
		<div class="ribbon"></div>
		<div id="slider" class="nivoSlider">
		'.$s.'
		</div>
		<div id="htmlcaption" class="nivo-html-caption">
		 
		</div>

		 
		</div>';
		}
		return $s;

	}
	public function adminscript()
	{
		return $this->img->adminscript();
	}
	public function sitescript()
	{
		$s = $this->get_include_contents($this->abspath.$this->ftype."/jquery.nivo.slider.pack.js");
		$s.= <<<XYDAC
		\n
		jQuery(document).ready(function() {
        jQuery('#slider').nivoSlider();
    });
XYDAC;
		return $s;
	}

	public function sitestyle()
	{
		$s = $this->get_include_contents($this->abspath.$this->ftype."/default/default.css");
		$s.= $this->get_include_contents($this->abspath.$this->ftype."/pascal/pascal.css");
		$s.= $this->get_include_contents($this->abspath.$this->ftype."/orman/orman.css");
		$s.= $this->get_include_contents($this->abspath.$this->ftype."/nivo-slider.css");
		//$s.= $this->get_include_contents($this->abspath.$this->ftype."/style.css");
		$s.= ".theme-default #slider {
		/*  margin:100px auto 0 auto; */
		width:618px; /* Make sure your images are the same size */
		height:246px; /* Make sure your images are the same size */
	}";
		return $s;
	}

}

?>