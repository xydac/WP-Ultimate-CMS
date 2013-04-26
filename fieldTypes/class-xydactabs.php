<?php 

class xydactabs extends field_type{
	private $temp_title;
	private $temp_data;

	public function __construct($name,$args = array())
	{
		parent::__construct($name,$args);
		$this->ver = 1.0;
		$this->basic = false;
		$this->ftype = 'xydactabs';
		$this->flabel = __('Xydac Tabs',XYDAC_CMS_NAME);
		$this->compaitable = array('pagetype','posttype','taxonomy');
		$this->temp_title = new text($this->name."_title",$this->label,$this->desc,"",true);
		$this->temp_data = new richtextarea($this->name."_data",$this->label,$this->desc,"",true);
		$this->hasmultiple = true;
		add_action('xydac_get_cpt_fieldtype',array($this,'get_cpt_field'));
	}
	function get_cpt_field($fields)
	{
		if(is_array($fields))
			foreach($fields as $field)
			if($field['field_name'] == $this->name)
			return $field['field_type'];
		return false;
	}
	function get_input($no='false',$val='false',$tabular=false)
	{
		global $post;
		$val_title = false;
		$val_data= false;
		if(is_string($val) && $val=='false')
			$val = substr(uniqid(),0,8);
		else
		{
			if($val)
			{
				$val_title = get_post_meta($post->ID, $this->temp_title->name.'-'.$val, true);
				$val_data = get_post_meta($post->ID, $this->temp_data->name.'-'.$val, true);
			}
		}
		$r="<div>";
		$r.= text::get_text_input(array('name'=>$this->temp_title->name.'-'.$val,'tabular'=>$tabular,'label'=>$this->label.__(' Title',XYDAC_CMS_NAME),'desc'=>$this->desc),$val_title,"xydac_custom[".$this->name.'-'.$val."]",true);
		$r.= richtextarea::get_richtextarea_input(array('name'=>$this->temp_data->name.'-'.$val,'tabular'=>$tabular,'label'=>$this->label.__(' Data',XYDAC_CMS_NAME),'desc'=>$this->desc),wp_specialchars_decode(stripslashes_deep($val_data),ENT_QUOTES),"xydac_custom[".$this->name.'-'.$val."]",true);
		$r.='<p><span class="'.$this->name.'-a">ii&nbsp;</span></p>';//don not remove this -a, this is just for ajax script
		$r.="</div>";
		return $r;
	}
	public function saving(&$temp,$post_id,$val,$oval='')
	{

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
			$this->temp_data->name = $this->temp_data->name.'-'.$key;
			$eval = trim($val[$this->temp_title->name]);
			if(!empty($eval))
			{
				if($new)
					add_post_meta($post_id, $this->name, $key);
				$this->temp_title->saving($temp,$post_id,esc_attr(stripslashes($val[$this->temp_title->name])),$oval);
				$this->temp_data->saving($temp,$post_id,esc_attr(stripslashes($val[$this->temp_data->name])),$oval);
			}
		}
	}
	public function output($vals,$atts)
	{
		global $post;
		$data = array();
		foreach($vals as $val)
		{
			$title = get_post_meta($post->ID, $this->temp_title->name.'-'.$val, true);
			$datas = get_post_meta($post->ID, $this->temp_data->name.'-'.$val, true);
			$data[$title]=$datas;

		}
		ksort($data);
		$e='';
		$e.= "<div class='xydac-custom-meta'>";
		$e.= '<ul class="xydac-custom-meta" id="xydac-custom-meta">';
		$i = 0;
		foreach($data as $k=>$v)
		{
			if($i==0)
				$e.='<li class="active '.sanitize_title_with_dashes($k).'"><a class="active" href="javascript:void(null);">'.$k.'</a></li>';
			else
				$e.='<li class="'.sanitize_title_with_dashes($k).'"><a href="javascript:void(null);">'.$k.'</a></li>';
			$i++;
		}
		$e.='</ul>';
		foreach($data as $k=>$v)
		{
			$e.= "<div class='".sanitize_title_with_dashes($k)."'>";
			$e.= do_shortcode(wp_specialchars_decode(stripslashes_deep($v),ENT_QUOTES));
			$e .="</div>";
		}
		$e .="</div>";


		return $e;

	}

}

?>