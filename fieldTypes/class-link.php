<?php 
//@todo: may be this is not working on 30 jul 2012
class link extends field_type{

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = true;
		$this->ftype = 'link';
		$this->flabel = __('Link',XYDAC_CMS_NAME);

	}
	function get_input($no='false',$val=false,$tabular=false)
	{
		$this->temp_title = new text($this->name."_title",$this->label,$this->desc,"",true);
		$this->temp_link = new text($this->name."_link",$this->label,$this->desc,"",true);
		global $post;
		$val_title = false;
		$val_link= false;
		if(is_string($val) && $val=='false')
			$val = substr(uniqid(),0,8);
		else
		{
			if($val)
			{
				$val_title = get_post_meta($post->ID, $this->temp_title->name.'-'.$val, true);
				$val_link = get_post_meta($post->ID, $this->temp_link->name.'-'.$val, true);
			}
		}
		$r="<div>";
		$r.= text::get_text_input(array('name'=>$this->temp_title->name.'-'.$val,'tabular'=>$tabular,'label'=>$this->label.__(' Title',XYDAC_CMS_NAME),'desc'=>$this->desc),$val_title,"xydac_custom[".$this->name.'-'.$val."]",true);
		$r.= text::get_text_input(array('name'=>$this->temp_link->name.'-'.$val,'tabular'=>$tabular,'label'=>$this->label.__(' Link',XYDAC_CMS_NAME),'desc'=>$this->desc),$val_link,"xydac_custom[".$this->name.'-'.$val."]",true);
		$r.='<p><span class="'.$this->name.'-a">&nbsp;</span></p>';//do not remove this -a, this is just for ajax script
		$r.="</div>";
		return $r;
	}

	public function saving(&$temp,$post_id,$val,$oval='')
	{
		$this->temp_title = new text($this->name."_title",$this->label,$this->desc,"",true);
		$this->temp_link = new text($this->name."_link",$this->label,$this->desc,"",true);
		if(is_array($val))
		{
			$key='';
			$new = true;
			foreach($val as $k=>$v)
			{
				$b=explode('-',$k);
				$key = $b[count($b)-1];
			}
			$vals = get_post_meta($post_id,$this->name,false);
			foreach($vals as $k=>$v)
				if($v==$key)
				$new = false;

			$this->temp_title->name = $this->temp_title->name.'-'.$key;
			$this->temp_link->name = $this->temp_link->name.'-'.$key;
			$eval = trim($val[$this->temp_title->name]);
			if(!empty($eval))
			{
				if($new)
					add_post_meta($post_id, $this->name, $key);
				$this->temp_title->saving($temp,$post_id,esc_attr(stripslashes($val[$this->temp_title->name])),$oval);
				$this->temp_link->saving($temp,$post_id,esc_attr(stripslashes($val[$this->temp_link->name])),$oval);
			}
		}
	}
	public function output($vals,$atts)
	{
		$this->temp_title = new text($this->name."_title",$this->label,$this->desc,"",true);
		$this->temp_link = new text($this->name."_link",$this->label,$this->desc,"",true);
		global $post;
		$data = array();
		foreach($vals as $val)
		{
			$title = get_post_meta($post->ID, $this->temp_title->name.'-'.$val, true);
			$link = get_post_meta($post->ID, $this->temp_link->name.'-'.$val, true);
			$data[$title]=$link;

		}
		ksort($data);
		$e='';
		foreach($data as $k=>$v)
			$e.='<a href="'.$v.'">'.$k.'</a></li>';
		return $e;

	}

}

?>