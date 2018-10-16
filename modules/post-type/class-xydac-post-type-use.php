<?php
/* Handles input and output of post types */
/* uses get_reg_cptName() */

class xydac_post_type_use{

	function __construct()
	{
		add_shortcode('xydac_field',array($this,'xydac_cpt_shortcode'));
		add_action('admin_init',array($this,'custom_meta_admin'));
		add_action('save_post',array($this,'custom_meta_save'));
		add_action('wp_ajax_xydac_cms_post_type', array($this,'xydac_cms_ajax' ));

	}


	function xydac_cms_ajax()
	{
		$post_type = esc_attr($_POST['type']);
		$fieldname = esc_attr($_POST['field_name']);
		$subaction = esc_attr($_POST['subaction']);
		$fields = xydac()->modules->post_type->get_field($post_type);
		if(is_array($fields))
			foreach($fields as $field)
			if($fieldname == $field['field_name'])
			{
				$field_temp = new $field['field_type']($field['field_name'],array('label'=>$field['field_label'],'desc'=>$field['field_desc'],'val'=>$field['field_val']));
				if($subaction == 'xydac_add_more')
					echo $field_temp->get_input();
				else
					echo $field_temp->get_ajax_output($subaction);
			}

			die;
	}
	function xydac_cpt_shortcode($atts, $text) {
		global $post;
		$text = trim($text);
		$t=get_post_meta($post->ID, $text);
		$fieldtype = xydac()->modules->post_type->get_field_type($post->post_type,$text);//xydac_cms_get_cpt_fieldtype($post->post_type,$text);
		if($fieldtype)
		{
			$temp_field = new $fieldtype($text);
			return $temp_field->output($t,$atts);
		}
		else
			return $t;
	}
	/* BEGIN META BOX CODE */
	function custom_meta_admin()
	{
		//add_thickbox();
		$cpt_names = xydac()->modules->post_type->get_active_names();//get_reg_cptName();
		xydac()->log('custom_meta_admin',$cpt_names);
		if(is_array($cpt_names))
			foreach($cpt_names as $k=>$cpt_name)
			{
				$opt = xydac()->modules->post_type->get_field($cpt_name);//getCptFields($cpt_name);
				xydac()->log('custom_meta_admin',$opt);
				if($opt)
					add_meta_box('xydac-custom-meta-div', __('XYDAC Custom Fields',XYDAC_CMS_NAME), array($this,'custom_meta'), $cpt_name, 'normal', 'high');
			}
	}

	function custom_meta($post) {
		//$val = get_post_meta($post->ID, 'product-ver', TRUE);
		$fields = xydac()->modules->post_type->get_field($post->post_type);//getCptFields($post->post_type);
		$notbasic = array();
		$inputfields=array();
		$t="";
		$e="";
		$tax_combo = xydac()->modules->post_type->get_xydac_cms_tax_combo($post->ID);
		
		foreach($fields as $k=>$field)
		{
			$field_temp = new $field['field_type']($field['field_name'],array('label'=>$field['field_label'],'desc'=>$field['field_desc'],'val'=>$field['field_val'],'hasmultiple'=>$field['field_has_multiple']));
			if($field_temp->isBasic())
			{
				$t.= "<div id='".$field['field_name']."' class='xydac_cms_field  form-field' rel='".$post->post_type."'>".$field_temp->input($post->ID)."</div>";
				array_push($inputfields,$field['field_name']);
			}
			else
				array_push($notbasic,$field);
		}
		$e.= "<div class='xydac-custom-meta'>";
		$e.= '<ul class="xydac-custom-meta" id="xydac-custom-meta">
		<li class="active xydac-custom"><a class="active" href="javascript:void(null);">Basic</a></li>';
		foreach($notbasic as $k=>$field)
		{
			$e.='<li class="'.$field['field_name'].'"><a href="javascript:void(null);">'.$field['field_type'].'-'.$field['field_label'].'</a></li>';
		}
		if(isset($tax_combo) && strlen($tax_combo))
			$e.="<li class='taxonomies'><a href='javascript:void(null);'>".__('Additional Information',XYDAC_CMS_NAME)."</a></li>";

		$e.= '<li class="inputfields"><a href="javascript:void(null);">Shortcodes</a></li>';
		$e.='</ul>';
		$e.= "<div class='xydac-custom xydacfieldform'>";
		$e.="<input type='hidden' name='xydac_custom_nonce' id='xydac_custom_nonce' value='".wp_create_nonce( plugin_basename(__FILE__) )."' />";
		$e.=$t;
		$e .="</div>";
		foreach($notbasic as $k=>$field)
		{
			$e.= "<div class='xydac_cms_field xydacfieldform form-field ".$field['field_name']."' id='".$field['field_name']."' rel='".$post->post_type."'>";

			$field_temp = new $field['field_type']($field['field_name'],array('label'=>$field['field_label'],'desc'=>$field['field_desc'],'val'=>$field['field_val'],'hasmultiple'=>$field['field_has_multiple']));
			$e.= $field_temp->input($post->ID);

			$e.= "</div>";
			array_push($inputfields,$field['field_name']);
		}

		if(isset($tax_combo) && strlen($tax_combo))
			$e.="<div class='taxonomies'>".$tax_combo."</div>";

		$e.='<div class="inputfields">';
		// -- Begin shortcodes
		$e.='<div class="admin-tab-content">';
		$e.='<h4>'.__('Availaible Shortcodes For Use ',XYDAC_CMS_NAME).'</h4>';
		$e.='<p>'.__('You can use these shortcodes anywhere to get the values for them at used location.',XYDAC_CMS_NAME).'</p>';
		foreach($inputfields as $inputfields)
		{

			//$e.='<strong>'.__('Field Name',XYDAC_CMS_NAME).'</strong> : &nbsp;'.$inputfields;
			$e.='<pre>[xydac_field]'.$inputfields.'[/xydac_field]</pre>';
		}
		$e.='';
		$e.="</div>";
// -- end shortcodes
		$e.="</div>";
		
		$e.="</div>";
		$e.="<div class='clear'></div>";
		echo $e;
	}

	function getShortcodeContents(){
		
		return $e;
	}

	function custom_meta_save( $post_id ) {
		if (isset($_POST['xydac_custom_nonce']) && wp_verify_nonce( $_POST['xydac_custom_nonce'], plugin_basename(__FILE__) ))
		{
	  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	  	return $post_id;
	  $post = get_post($post_id);
	  $i=0;
	  $temp = array();
	  if(isset($_POST['xydac_custom']))
		  if(is_array($_POST['xydac_custom']))
	  	foreach($_POST['xydac_custom'] as $a=>$t)
				{
					$b=explode('-',$a);
					if(count($b)>=2)
					{
						unset($b[count($b)-1]);
						$field_name = implode('-',$b);
					}
					else
						$field_name = $b[0];
					$fieldtype = xydac()->modules->post_type->get_field_type($post->post_type,$field_name);//xydac_cms_get_cpt_fieldtype($post->post_type,$field_name);
					//echo 'Starting '.$t.': '.$a.'- '.$_POST[$a.'-old'].'<br/><br/>';
					
					if($fieldtype)
					{

						$temp_field = new $fieldtype($field_name);
						if(isset($_POST[$a.'-old']) && !empty($_POST[$a.'-old']))
							$temp_field->saving($temp,$post_id,$t,$_POST[$a.'-old']);
						else
							$temp_field->saving($temp,$post_id,$t);
					}

				}
			//	die();
	   return $temp;
		}
		else
			return $post_id;
	}

	/* END META BOX CODE */
}
?>