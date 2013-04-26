<?php 

class gallery extends field_type{

	public function __construct($name,$label='',$desc='',$val='',$hasmultiple=false,$help='')
	{
		parent::__construct($name,$label,$desc,$val,$hasmultiple,$help);
		$this->ver = 1.0;
		$this->basic = false;
		$this->ftype = 'gallery';
		$this->flabel = __('Gallery',XYDAC_CMS_NAME);
		$this->help = 'width,height';
	}
	// item_name => field_type => count => img_alt => "";
	// item_name => field_type => count => img_url => "";
	public function input($post_id)
	{
		$img = get_post_meta($post_id, $this->name, TRUE);
		$count=0;
		if(is_array($img))
			$img = $img[$this->ftype];
		if(is_array($img))
			foreach($img as $c=>$v)
			{
				$count = $c;
				$e.="<label for='".$this->name."-AlternateText'>".$this->label." Label-".$count."</label>";
				$e.="<p><input type='text' id='".$this->name."-AlternateText' name='xydac_custom[".$this->ftype."-".$this->name."][".$count."][img_alt]' value='".$v['img_alt']."' /></p>";
				$e.="<label for='".$this->name."-URL'>".$this->label." URL</label>";
				$e.="<p><input type='text' id='".$this->name."-URL' name='xydac_custom[".$this->ftype."-".$this->name."][".$count."][img_url]' value='".$v['img_url']."' /></p>";
				//$e.="<img src='".$v['img_url']."' alt='".$v['img_alt']."'/>";
				//$e.="<p><span>".$this->desc."</span></p>";
			}
			$count++;
			$img = array();
			$img['img_alt']='';
			$img['img_url']='';

			$e.="<label for='".$this->name."-AlternateText'>".$this->label." Label-".$count."</label>";
			$e.="<p><input type='text' id='".$this->name."-AlternateText' name='xydac_custom[".$this->ftype."-".$this->name."][".$count."][img_alt]' value='".$img['img_alt']."' /></p>";
			$e.="<label for='".$this->name."-URL'>".$this->label." URL</label>";
			$e.="<p><input type='text' id='".$this->name."-URL' name='xydac_custom[".$this->ftype."-".$this->name."][".$count."][img_url]' value='".$img['img_url']."' /></p>";

			$e.="<img src='".$img['img_url']."' alt='".$img['img_alt']."'/>";
			$e.="<p><span>".$this->desc."</span></p>";
			return $e;
	}

	public function saving(&$temp,$post_id,$val,$oval=null)
	{
		$nval = array();
		foreach($val as $k=>$v)
			if(''!=$v['img_url'])
			$nval[$this->ftype][$k]= array('img_url' => $v['img_url'],'img_alt' => $v['img_alt']);
		array_push($temp,update_post_meta($post_id, $this->name, $nval));
	}
	public function output($val,$atts)
	{
		$c=1;
		$d = '';
		$atemp = explode(',',$this->value);

		$width = $atemp[0];
		$height = $atemp[1];
		$d.='<div id="slider" style="color:red;width:'.$width.';height:'.$height.'">
		<ul id="sliderContent">';
		if(is_array($val))
			foreach ($val as $k=>$data)
			{
				$d.='<li class="sliderImage">
				<a href=""><img src="'.$data['img_url'].'" alt="'.$c.'" /></a>
				<span class="bottom">'.$data['img_alt'].'</span>
				</li>';
				//$d.='<img class = "as" id ="'.$this->name.'-'.$k.'" src="'.$data['img_url'].'" alt="'.$data['img_alt'].'">';
				$c++;
			}
			$d.="<div class=\"clear sliderImage\"></div></ul></div>";
			return $d;
	}
	public function sitescript()
	{
		//USING s3Slider jQuery Plugin //http://www.serie3.info/s3slider/
		$r='(function($){$.fn.s3Slider = function(vars) { var element = this; var timeOut = (vars.timeOut != undefined) ? vars.timeOut : 4000; var current = null; var timeOutFn = null; var faderStat = true; var mOver = false; var items = $("#" + element[0].id + "Content ." + element[0].id + "Image"); var itemsSpan = $("#" + element[0].id + "Content ." + element[0].id + "Image span"); items.each(function(i) { $(items[i]).mouseover(function() { mOver = true; }); $(items[i]).mouseout(function() { mOver = false;fadeElement(true); }); });var fadeElement = function(isMouseOut) { var thisTimeOut = (isMouseOut) ? (timeOut/2) : timeOut; thisTimeOut = (faderStat) ? 10 : thisTimeOut; if(items.length > 0) { timeOutFn = setTimeout(makeSlider, thisTimeOut); } else { console.log("Poof.."); } }; var makeSlider = function() {current = (current != null) ? current : items[(items.length-1)]; var currNo = jQuery.inArray(current, items) + 1; currNo = (currNo == items.length) ? 0 : (currNo - 1); var newMargin = $(element).width() * currNo; if(faderStat == true) { if(!mOver) { $(items[currNo]).fadeIn((timeOut/6), function() { if($(itemsSpan[currNo]).css(\'bottom\') == 0) { $(itemsSpan[currNo]).slideUp((timeOut/6), function() { faderStat = false; current = items[currNo]; if(!mOver) { fadeElement(false); } }); } else { $(itemsSpan[currNo]).slideDown((timeOut/6), function() { faderStat = false; current = items[currNo]; if(!mOver) { fadeElement(false); } }); } }); } } else { if(!mOver) { if($(itemsSpan[currNo]).css(\'bottom\') == 0) { $(itemsSpan[currNo]).slideDown((timeOut/6), function() { $(items[currNo]).fadeOut((timeOut/6), function() { faderStat = true; current = items[(currNo+1)]; if(!mOver) { fadeElement(false); } }); }); } else { $(itemsSpan[currNo]).slideUp((timeOut/6), function() { $(items[currNo]).fadeOut((timeOut/6), function() { faderStat = true; current = items[(currNo+1)]; if(!mOver) { fadeElement(false); } }); }); } } } }
		makeSlider();};})(jQuery); ';
		$r.="jQuery(document).ready(function() { if(jQuery('#slider').length > 0) { jQuery('#slider').s3Slider({ timeOut: 3000 }); } });";
		return $r;
	}
	public function sitestyle()
	{
		$r="#slider { width: 200px; /* important to be same as image width */ height: 200px; /* important to be same as image height */ position: relative; /* important */ overflow: hidden; /* important */ } #sliderContent { width: 410px; /* important to be same as image width or wider */ position: absolute; top: 0; margin-left: 0; } .sliderImage { float: left; position: relative; display: none; } .sliderImage span { position: absolute; font: 10px/15px Arial, Helvetica, sans-serif; padding: 10px 13px; width: 384px; background-color: #000; filter: alpha(opacity=70); -moz-opacity: 0.7; -khtml-opacity: 0.7; opacity: 0.7; color: #fff; display: none; } .clear { clear: both; } .sliderImage span strong { font-size: 14px; } .top { top: 0; left: 0; } .bottom { bottom: 0; left: 0; } div#slider ul { list-style-type: none;} ";
		return $r;
	}
}

?>