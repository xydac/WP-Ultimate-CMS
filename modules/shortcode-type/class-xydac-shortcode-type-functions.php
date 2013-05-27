<?php
class xydac_shortcode_type_functions{
	var $shortcodes;
	var $values = array();
	var $nestedvalues = array();
	var $nestedtagname = '';
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
	function xydac_shortcode($atts, $fullcontent,$shortcode_name)
	{
		
		if(!isset($this->shortcodes[$shortcode_name]))
			return;
		else{
		
			$shortcode = $this->shortcodes[$shortcode_name];
			$simpleshortcode = isset($shortcode['simpleshortcode'])?$shortcode['simpleshortcode']:true;
			extract($shortcode);
			$attr = $this->get_attr_data($shortcode['attr']);
			$this->nestedtagname = 'xys_'.$nestedtagname;
			$this->values = shortcode_atts($attr, $atts);
			
			$template = $customhtml;
			$template_pattern = '/(?:##BEGINLOOP##)(.+?)(?:##ENDLOOP##)/s';
			preg_match_all($template_pattern,trim($template),$templatematches);
			$templatesplit = preg_split($template_pattern, trim($template), -1, PREG_SPLIT_DELIM_CAPTURE);
			
			$output = '';
			if($this->nestedtagname!='' && is_array($templatematches) &&!empty($templatematches[1])){
				preg_match_all("/(.?)\[(".$this->nestedtagname.")\b(.*?)(?:(\/))?\](?:(.+?)\[\/".$this->nestedtagname."\])?(.?)/s",$fullcontent,$matches);
				$output.=$templatesplit[0];
				
				foreach($templatematches[1] as $j=>$loop){
					foreach($matches[0] as $i=>$match){
						$this->values = shortcode_atts($attr, array_merge($atts,(array)shortcode_parse_atts( $matches[3][$i] )));
						$this->text =  $matches[5][$i];
						$output.= preg_replace_callback('/##([a-z|A-Z]*[0-9]*)##/',array($this,'rep_func'),$loop);
					}
					if(isset($templatesplit[2*($j+1)]))
						$output.=$templatesplit[2*($j+1)];
				}
			}else{
				$this->text =  $fullcontent;
				$output.= preg_replace_callback('/##([a-z|A-Z]*[0-9]*)##/',array($this,'rep_func'),trim($template));
			}
			return '<div class="'.$shortcode_name.'">'.do_shortcode($output).'</div>';
		}		
	}
}
?>