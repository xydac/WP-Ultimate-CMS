<?php
/* Handles input and output of page types */
/* uses get_reg_page_type_name() */
class xydac_page_type_use{

	function __construct()
	{
		add_shortcode('xydac_page_field',array($this,'xydac_pagetype_shortcode'));
		add_action('admin_init',array($this,'custom_meta_admin'));
		add_action('save_post',array($this,'custom_meta_save'));
		add_action('wp_ajax_xydac_cms_page_type', array($this,'xydac_cms_ajax' ));
	}
	function xydac_cms_ajax()
	{
		$post_type = esc_attr($_POST['type']);
		$fieldname = esc_attr($_POST['field_name']);
		$subaction = esc_attr($_POST['subaction']);
		$fields = xydac()->modules->page_type->get_field($post_type);//get_page_type_fields($post_type);
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

	function xydac_pagetype_shortcode($atts, $text) {
		global $post;
		$text = trim($text);
		$t=get_post_meta($post->ID, $text);
		$fieldtype = xydac()->modules->page_type->get_field_type(xydac()->modules->page_type->get_page_type($post->ID),$text,'field_name','field_type');//xydac_cms_get_page_fieldtype(xydac()->modules->page_type->get_page_type($post->ID),$text);
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
		add_thickbox();
		add_meta_box('xydac-page-custom-meta-div', __('XYDAC Page Custom Fields','xydac_cpt'), array($this,'custom_meta'), 'page', 'normal', 'high');
	}

	function custom_meta($page) {
		//$val = get_page_meta($page->ID, 'product-ver', TRUE);
		global $post;
		xydac()->log('custom_meta page');
		$page_type = xydac()->modules->page_type->get_page_type($page->ID);
		$page_type_names = xydac()->modules->page_type->get_main_names();//get_reg_page_type_name()
		$page = $post;
		if(!empty($page_type) && in_array($page_type,$page_type_names))
		{
			$fields = xydac()->modules->page_type->get_field($page_type);//get_page_type_fields($page_type);
			$notbasic = array();
			$inputfields=array();
			$t="";
			$e="";
			foreach($fields as $k=>$field)
			{
				$field_temp = new $field['field_type']($field['field_name'],array('label'=>$field['field_label'],'desc'=>$field['field_desc'],'val'=>$field['field_val'],'hasmultiple'=>$field['field_has_multiple']));
				if($field_temp->isBasic())
				{
					$t.= "<div id='".$field['field_name']."' class='xydac_cms_field' rel='".$post->post_type."'>".$field_temp->input($page->ID)."</div>";
					$t.= "<hr class='hrule clear'>";
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
			$e.= '<li class="inputfields"><a href="javascript:void(null);">'.__('Shortcodes',XYDAC_CMS_NAME).'</a></li>';
			$e.= '<li class="pagetype"><a href="javascript:void(null);">'.__('Page Type',XYDAC_CMS_NAME).'</a></li>';
			$e.='</ul>';
			$e.= "<div class='xydac-custom'>";
			$e.="<input type='hidden' name='xydac_page_custom_nonce' id='xydac_page_custom_nonce' value='".wp_create_nonce( plugin_basename(__FILE__) )."' />";
			$e.=$t;
			$e .="</div>";
			foreach($notbasic as $k=>$field)
			{
				$e.= "<div class='xydac_cms_field ".$field['field_name']."' id='".$field['field_name']."' rel='".$page_type."'>";
				$field_temp = new $field['field_type']($field['field_name'],array('label'=>$field['field_label'],'desc'=>$field['field_desc'],'val'=>$field['field_val'],'hasmultiple'=>$field['field_has_multiple']));
				$e.= $field_temp->input($page->ID);
				$e.= "</div>";
				array_push($inputfields,$field['field_name']);
			}
			$e.='<div class="inputfields">';
			$e.='<h4>'.__('Availaible Shortcodes For Use ',XYDAC_CMS_NAME).'</h4>';
			$e.= "<hr class='hrule clear'>";
			$e.='<p style="word-spacing:2px;letter-spacing:3px"><strong>'.__('You can use these shortcodes anywhere to get the values for them at used location.',XYDAC_CMS_NAME).'</strong></p>';
			foreach($inputfields as $inputfields)
			{

				$e.='<strong>'.__('Field Name',XYDAC_CMS_NAME).'</strong> : &nbsp;'.$inputfields;
				$e.='<p style="letter-spacing:2px">[xydac_page_field]'.$inputfields.'[/xydac_page_field]</p><br/>';
			}
			$e.='';
			$e.="</div>";
			$e.= "<div class='pagetype'>";
			$e.= "<input type='hidden' name='xydac_page_custom_nonce' id='xydac_page_custom_nonce' value='".wp_create_nonce( plugin_basename(__FILE__) )."' />";
			$e.= "<label for='xydac_page_type' style='padding:4px'>".__('Select Page Type',XYDAC_CMS_NAME)."</label>";
			$e.= "<select name='xydac_page_type'>";
			$pagetypes = xydac()->modules->page_type->get_active();//get_active_page_types();
			if(is_array($pagetypes))
				foreach($pagetypes as $pagetype)
				if($page_type ==$pagetype['name'])
				$e.= "<option selected value='".$pagetype['name']."'>".$pagetype['label']."</option>";
			else
				$e.= "<option value='".$pagetype['name']."'>".$pagetype['label']."</option>";
			$e.= "<option value='none'>NONE</option>";
			$e.= "</select>";
			$e.= "</div>";
			$e.="</div>";
			echo $e;
		}
		else
		{
			$e = "<div class='xydac-custom-meta'>";
			$e.= "<input type='hidden' name='xydac_page_custom_nonce' id='xydac_page_custom_nonce' value='".wp_create_nonce( plugin_basename(__FILE__) )."' />";
			$e.= "<label for='xydac_page_type' style='padding:4px'>".__('Select Page Type')."</label>";
			$e.= "<select name='xydac_page_type'>";
			$pagetypes = xydac()->modules->page_type->get_active();//get_active_page_types();
			if(is_array($pagetypes))
				foreach($pagetypes as $pagetype){
				$e.= "<option value='".$pagetype['name']."'>".$pagetype['label']."</option>";
			}
			$e.= "<option value='none' selected>NONE</option>";
			$e.= "</select>";
			$e.= "</div>";
			echo $e;
		}
	}

	function custom_meta_save( $page_id ) {
		if (isset($_POST['xydac_page_custom_nonce']) && wp_verify_nonce( $_POST['xydac_page_custom_nonce'], plugin_basename(__FILE__) ))
		{
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return $page_id;
			$page_type = xydac()->modules->page_type->get_page_type($page_id);//get_page_type($page_id);
			$page_type_names = xydac()->modules->page_type->get_main_names();//get_reg_page_type_name
			if(!empty($page_type) && in_array($page_type,$page_type_names))//get_reg_page_type_name
			{
				$page = get_post($page_id);
				$i=0;
				$temp = array();
				if(isset($_POST['xydac_custom']))
					if(is_array($_POST['xydac_custom']))
					foreach($_POST['xydac_custom'] as $a=>$t)
					{
						$b=explode('-',$a);
						if(count($b)>2)
						{
							unset($b[count($b)-1]);
							$field_name = implode('-',$b);
						}
						else
							$field_name = $b[0];
						$fieldtype = xydac()->modules->page_type->get_field_type($page_type,$field_name,'field_name','field_type');//xydac_cms_get_page_fieldtype($page_type,$field_name);
						//if get_field_type doens't give string there there is error
						if($fieldtype)
						{

							$temp_field = new $fieldtype($field_name);
							if(isset($_POST[$a.'-old']) && !empty($_POST[$a.'-old']))
								$temp_field->saving($temp,$page_id,$t,$_POST[$a.'-old']);
							else
								$temp_field->saving($temp,$page_id,$t);
						}
							
					}
					if(isset($_POST['xydac_page_type']))
						if(in_array($_POST['xydac_page_type'],$page_type_names))//get_reg_page_type_name()
						array_push($temp,update_post_meta($page_id, 'page_type', $_POST['xydac_page_type']));

					return $temp;
			}
			else
			{

				$temp = array();
				if(isset($_POST['xydac_page_type']))
					if(in_array($_POST['xydac_page_type'],$page_type_names))//get_reg_page_type_name()
					{
						array_push($temp,update_post_meta($page_id, 'page_type', $_POST['xydac_page_type']));
						return $temp;
					}

			}
		}
		else
			return $page_id;

	}

	/* END META BOX CODE */
}
?>