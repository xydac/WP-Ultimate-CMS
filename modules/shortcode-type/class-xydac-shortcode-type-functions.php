<?php
class xydac_shortcode_type_functions{
	var $shortcodes;
	var $values = array();
	var $nestedvalues = array();
	var $text="";
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
	function get_attr_data($attr){
		$attr = explode(',',$attr);
			
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
		return $attr;
	}
	function xydac_shortcode($atts, $text,$shortcode_name)
	{
		
		if(!isset($this->shortcodes[$shortcode_name]))
			return;
		else{
			$shortcode = $this->shortcodes[$shortcode_name];
			$simpleshortcode = $shortcode['simpleshortcode'];
			$attr = $this->get_attr_data($shortcode['attr']);
			
			
			$this->values = shortcode_atts($attr, $atts);
			
			if($simpleshortcode=='false'){
				
				extract($shortcode);
				$nestedattr = $this->get_attr_data($nestedattr);
				
				
				$nestedtagname = 'xys_'.$nestedtagname;
				$s = '';
				if(preg_match_all("/(.?)\[(".$nestedtagname.")\b(.*?)(?:(\/))?\](?:(.+?)\[\/".$nestedtagname."\])?(.?)/s", $text, $matches)){
					
					for($i = 0; $i < count($matches[0]); $i++) {
						$matches[3][$i] = shortcode_parse_atts( $matches[3][$i] );
					}
					$s.=$beforeloop;
					for($i = 0; $i < count($matches[0]); $i++) {
						$this->values = shortcode_atts($nestedattr, $matches[3][$i]);
						$this->text =  $matches[5][$i];
						$s .= preg_replace_callback('/##([0-9]*[a-z|A-Z]*[0-9]*)##/',array($this,'rep_func'),$loop1);
						 
					}
					$s.=$afterloop1;
					for($i = 0; $i < count($matches[0]); $i++) {
						$this->values = shortcode_atts($nestedattr, $matches[3][$i]);
						$this->text =  $matches[5][$i];
						$s .= preg_replace_callback('/##([0-9]*[a-z|A-Z]*[0-9]*)##/',array($this,'rep_func'),$loop2);
							
					}
					$s.=$afterloop2;
						
				}
				return '<div class="'.$shortcode_name.'">'.$s.'</div>';
				
			}else{
				$this->text = do_shortcode($text);
				$output = preg_replace_callback('/##([0-9]*[a-z|A-Z]*[0-9]*)##/',array($this,'rep_func'),$shortcode['customhtml']);
					
				return '<div class="'.$shortcode_name.'">'.$output.'</div>';
			}
			
		}
		
		
	}

}