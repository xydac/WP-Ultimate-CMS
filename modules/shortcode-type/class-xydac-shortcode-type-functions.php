<?php
class xydac_shortcode_type_functions{
	var $shortcodes;
	var $values = array();
	var $text;
	function __construct()
	{
		$this->shortcodes = stripslashes_deep(xydac()->modules->shortcode_type->get_active());
		$newshortcode = array();
		if(is_array($this->shortcodes) && !empty($this->shortcodes)){
			foreach($this->shortcodes as $shortcode){
				add_shortcode('xys_'.$shortcode['name'],array($this,'xydac_shortcode'));
				$name = 'xys_'.$shortcode['name'];
				$newshortcode[$name] = $shortcode;
			}
			$this->shortcodes = $newshortcode;
		}
		
	}
	function rep_func($matches){
		$val = $matches[1];
		if(isset($this->values[$val]))
			return $this->values[$val];
		else if(strtoupper($val)=='CONTENT')
			return $this->text;
		else
			return '';
	}
	function xydac_shortcode($atts, $text,$shortcode_name)
	{
		
		if(!isset($this->shortcodes[$shortcode_name]))
			return;
		else{
			$shortcode = $this->shortcodes[$shortcode_name];
			$attr = explode(',',$shortcode['attr']);
			
			if(is_array($attr) && !empty($attr)){
				$a = array();
				foreach($attr as $att){
					$kv = explode('=',$att);
					$key = $kv[0];
					if(isset($kv[1])){
						$value = $kv[1];
						$a[$key] = $value;
					}else
						$a[$key]='';
				}
				$attr = $a;
			}
			extract(shortcode_atts($attr, $atts));
			$this->values = shortcode_atts($attr, $atts);
			$this->text = do_shortcode($text);
			$output = preg_replace_callback('/##([0-9]*[a-z|A-Z]*[0-9]*)##/',array($this,'rep_func'),$shortcode['customhtml']);
			
			return '<div class="'.$shortcode_name.'">'.$output.'</div>';
		}
		
		
	}

}