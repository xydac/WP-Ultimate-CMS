<?php
if ( !class_exists( 'ct_fields' ) ) :
class ct_fields
{
	var $ct_type;
	var $ct_field_name;
	var $ct_field_label;
	var $ct_field_type ;
	var $ct_field_desc ;
	var $ct_field_val ;
	var $field;
	function ct_fields($taxonomy,$name=null,$label=null,$type=null,$desc=null,$val=null)
	{

		$this->ct_type = $taxonomy;
		$this->ct_field_name = $name;
		$this->ct_field_label = $label;
		$this->ct_field_type = $type;
		$this->ct_field_desc = $desc;
		$this->ct_field_val = $val;
		if($this->ct_field_name!=null && $this->ct_field_type!=null){
			$this->field = new $this->ct_field_type($this->ct_field_name,array('label'=>$this->ct_field_label,'desc'=>$this->ct_field_desc,'val'=>$this->ct_field_val,'fieldoptions'=>array('accesstype'=>'taxonomy')));
			add_action ( $this->ct_type.'_add_form_fields', array($this, 'field_input_metabox'));
			add_action ( $this->ct_type.'_edit_form_fields', array($this, 'field_input_metabox'));
			add_action ( 'edited_'.$this->ct_type, array($this, 'save_field_data' ));
			add_action ( 'created_'.$this->ct_type, array($this, 'save_field_data' ));
			add_filter( 'manage_edit-'.$this->ct_type.'_columns', array($this,'taxonomy_columns'));
			add_filter( 'manage_'.$this->ct_type.'_custom_column', array($this,'taxonomy_columns_manage'),10,3);
		}
		add_shortcode('xy_'.$this->ct_type, array($this,'xydac_shortcode'));
		//echo $this->ct_type.$post->ID.$this->ct_field_name."<br/>";
	}

	function taxonomy_columns($columns) {
		$columns[$this->ct_field_name] = __( $this->ct_field_label, XYDAC_CMS_NAME );
		return $columns;
	}
	function taxonomy_columns_manage( $out ,$column_name, $term) {
		global $wp_version;
		//if ($column_name !=$this->ct_field_name)
		//   return;
		$val =  get_metadata('taxonomy', $term, $this->ct_field_name, TRUE);
		//if ( !$val )
		//    $val = '<em>' . __( 'undefined', XYDAC_CMS_NAME ) . '</em>';
		//echo $val;
		if ($column_name==$this->ct_field_name)
		{
			if($this->ct_field_type!='image')
			{
				if ( !$val )
					$out .= '<em>' . __( 'undefined', XYDAC_CMS_NAME ) . '</em>';
				else
					$out .= $val;
			}
			else
			{
				if ( !$val )
					$out .= '<em>' . __( 'undefined', XYDAC_CMS_NAME ) . '</em>';
				else if(preg_match('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?\.(gif|jpg|jpeg|png|svg)/',$val,$match))
					$out .= "<img src='".$match[0]."' alt=$this->ct_field_name width='75px' height='75px' />";
			}
		}

		if(((float)$wp_version)<3.1)
			return $out;
		else
			echo $out;
	}


	function xydac_shortcode($atts,$text) {
		extract(shortcode_atts(array(
				'field' => '',
				'start' => '',
				'end' => '',
				'name_before' => '',
				'name_after' => '',
				'description_before' => '',
				'description_after' => '',
				'field_term'=>'',
				'showall'=>'',
				'fieldcode'=>''
		), $atts));

		$fields = xydac()->modules->taxonomy_type->get_field($this->ct_type);//get_taxonomy_fields($this->ct_type);
		$result='';

		ob_start();
		if(''!=$field_term)
		{
			$terms = get_terms($this->ct_type,array('slug'=>$field_term));
				
		}else if($showall=='true')
			{$terms =  get_terms($this->ct_type,'hide_empty=0');}
		else{
			global $post;
			$terms = wp_get_object_terms($post->ID, $this->ct_type);
		}

		//$field is the field defined in shortcode.
		if(!empty($field))
		{
			//if field is given
			$e= '';
			if(!empty($terms))
				foreach($terms as $v){
				if($field=='name')
					$e.= wp_specialchars_decode($name_before).do_shortcode(wp_specialchars_decode(stripslashes_deep($v->name),ENT_QUOTES)).wp_specialchars_decode($name_after);
				elseif($field=='description')
				$e.= wp_specialchars_decode($description_before).do_shortcode(wp_specialchars_decode(stripslashes_deep($v->description),ENT_QUOTES)).wp_specialchars_decode($description_after);
				elseif($field=='permalink')
				$e.= "<a href='".get_term_link(get_term($v->term_id,$this->ct_type))."'>".$v->name."</a>";
				else{
					if(is_array($fields))
						foreach($fields as $fielddata)
						{
							$f = new $fielddata['field_type']($fielddata['field_name'],array('label'=>$fielddata['field_label'],'desc'=>$fielddata['field_desc'],'val'=>$fielddata['field_val'],'fieldoptions'=>array('accesstype'=>'taxonomy'),'showall'=>'true'));
							if(isset($field) && $field==$fielddata['field_name'])
							{
								$e.= $f->taxonomy_output(get_metadata("taxonomy", $v->term_id,$fielddata['field_name'] , TRUE),$atts);
								break;
							}
								
						}
				}
			}
			if(!empty($e)){
				$result.=wp_specialchars_decode($start);
				$result.=$e;
				$result.=wp_specialchars_decode($end);
			}
			else
				$result.="";

		}else{
			
			if(isset($this->ct_field_name) && $this->ct_field_name!=null && $this->ct_field_type!=null){
				//HAS FIELDS handle fields also
				$e= wp_specialchars_decode($start);
				if(!empty($terms))
					foreach($terms as $v){
					$x_name = wp_specialchars_decode($name_before).do_shortcode(wp_specialchars_decode(stripslashes_deep($v->name),ENT_QUOTES)).wp_specialchars_decode($name_after);
					$x_description = wp_specialchars_decode($description_before).do_shortcode(wp_specialchars_decode(stripslashes_deep($v->description),ENT_QUOTES)).wp_specialchars_decode($description_after);
					$x_permalink = get_term_link(get_term($v->term_id,$this->ct_type));
					if(empty($fieldcode)){
						$e.=  "<a href='".$x_permalink."' rel=".$x_description.">".$x_name."</a>";
					}else{
						$x_permalink =  "<a href='".$x_permalink."' rel=".$x_description.">".$x_name."</a>";
						$x_fielddata = array();
						foreach($fields as $fielddata)
						{
							$f = new $fielddata['field_type']($fielddata['field_name'],array('label'=>$fielddata['field_label'],'desc'=>$fielddata['field_desc'],'val'=>$fielddata['field_val'],'fieldoptions'=>array('accesstype'=>'taxonomy')));
							$x_fielddata['/#'.$f->name.'/']= $f->taxonomy_output(get_metadata("taxonomy", $v->term_id,$fielddata['field_name'] , TRUE),array_merge($atts,array('rawdata'=>'1')));
						}
						$patterns = array_keys($x_fielddata);
						$replacements = array_values($x_fielddata);
						$r= preg_replace($patterns, $replacements, wp_specialchars_decode($fieldcode));
						$e.= preg_replace('/#x_permalink/', $x_permalink, $r);
						
					}

				}

				$e.= wp_specialchars_decode($end);
				$result.=$e;
			}
			else{
				//HAS NO FIELDS only handle name,description
				if(!empty($terms)){
					$e= wp_specialchars_decode($start);
					if(isset($start) && !empty($start))
						$e.="<p>".$this->ct_type." ";
					foreach($terms as $term){
						$x_name = wp_specialchars_decode($name_before).do_shortcode(wp_specialchars_decode(stripslashes_deep($term->name),ENT_QUOTES)).wp_specialchars_decode($name_after);
						$x_description = wp_specialchars_decode($description_before).do_shortcode(wp_specialchars_decode(stripslashes_deep($term->description),ENT_QUOTES)).wp_specialchars_decode($description_after);
						$x_permalink = get_term_link(get_term($term->term_id,$this->ct_type));
						$e.=  "<a href='".$x_permalink."' rel=".$x_description.">".$x_name."</a>";
					}
					if(isset($start) && !empty($start))
						$e.="</p>";
					$e.= wp_specialchars_decode($end);
					$result.=$e;
				}
				
			}
		}
		echo $result;
		$res = ob_get_clean();
		return $res;

	}

	public function field_input_metabox($tag)
	{
		echo $this->field->taxonomy_input($tag,$this->ct_type);

	}
	public function save_field_data($term_id) {
		$val = $_POST['xydac_custom'][$this->ct_field_name.'-0'];
		if (isset($val) ) {
			$ct_value = esc_attr($val);
			update_metadata('taxonomy', $term_id, $this->ct_field_name, $ct_value);
		}
	}
}
endif;
?>