<?php
/*
 Module Name:	Post Type
Type:			Module
Description:	Xydac Post Type module
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-xydac-post-type-manager.php';
include 'class-xydac-post-type-fields.php';
include 'class-xydac-post-type-use.php';
include 'class-xydac-post-type-functions.php';
class xydac_post_type extends xydac_cms_module{

	function __construct()
	{
		parent::__construct('post_type',array('module_label'=>'Xydac Post Type',
				'has_custom_fields'=>true,
				'uses_active'=>true,
				'registered_option'=>array('main'=>'xydac_cpt',
						'active'=>'xydac_cpt_active',
						'field'=>'xydac_cpt'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms_post_type',
				'menu_position'=>'top'
		));
		new xydac_post_type_use();
		new xydac_post_type_functions();


	}

	function init(){
		$cpts = stripslashes_deep($this->get_active());
		if (is_array($cpts) && !empty($cpts))
			foreach ($cpts  as $k=>$cpt )
			{
				$xy_cpt['name'] = $cpt['name'];
				$xy_cpt['args']['label'] = !empty($cpt['args']['label']) ? $cpt['args']['label'] : $xy_cpt['name'];
				$xy_cpt['args']['labels']['name'] = !empty($cpt['args']['labels']['name']) ? $cpt['args']['labels']['name'] : __($xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['singular_name'] = !empty($cpt['args']['labels']['singular_name']) ? $cpt['args']['labels']['singular_name'] : __($xy_cpt['args']['labels']['name']);
				$xy_cpt['args']['labels']['add_new'] = !empty($cpt['args']['labels']['add_new']) ? $cpt['args']['labels']['add_new'] : __('Add New');
				$xy_cpt['args']['labels']['add_new_item'] = !empty($cpt['args']['labels']['add_new_item']) ? $cpt['args']['labels']['add_new_item'] : __('Add New '.$xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['edit_item'] = !empty($cpt['args']['labels']['edit_item']) ? $cpt['args']['labels']['edit_item'] : __('Edit '.$xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['new_item'] = !empty($cpt['args']['labels']['new_item']) ? $cpt['args']['labels']['new_item'] : __('New '.$xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['view_item'] = !empty($cpt['args']['labels']['view_item']) ? $cpt['args']['labels']['view_item'] : __('View '.$xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['search_item'] = !empty($cpt['args']['labels']['search_item']) ? $cpt['args']['labels']['search_item'] : __('Search '.$xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['not_found'] = !empty($cpt['args']['labels']['not_found']) ? $cpt['args']['labels']['not_found'] : __('No '.$xy_cpt['args']['label'].' found');
				$xy_cpt['args']['labels']['not_found_in_trash'] = !empty($cpt['args']['labels']['not_found_in_trash']) ? $cpt['args']['labels']['not_found_in_trash'] : __('No '.$xy_cpt['args']['label'].' found in Thrash');
				$xy_cpt['args']['labels']['parent_item_colon'] = !empty($cpt['args']['labels']['parent_item_colon']) ? $cpt['args']['labels']['parent_item_colon'] : __('Parent '.$xy_cpt['args']['label']);
				$xy_cpt['args']['labels']['menu_name'] = !empty($cpt['args']['labels']['menu_name']) ? $cpt['args']['labels']['menu_name'] : $xy_cpt['name'];
				$xy_cpt['args']['description'] = !empty($cpt['args']['description']) ? $cpt['args']['description'] : '';
				$xy_cpt['args']['public'] = $this->xydac_checkbool($cpt['args']['public']);
				$xy_cpt['args']['publicly_queryable'] = $this->xydac_checkbool($cpt['args']['publicly_queryable']);
				$xy_cpt['args']['exclude_from_search'] = $this->xydac_checkbool($cpt['args']['exclude_from_search']);
				$xy_cpt['args']['show_ui'] = $this->xydac_checkbool($cpt['args']['show_ui']);
				$xy_cpt['args']['capability_type'] =  !empty($cpt['args']['capability_type']) ? $cpt['args']['capability_type'] : 'post';
				$xy_cpt['args']['hierarchical'] = $this->xydac_checkbool($cpt['args']['hierarchical']);
				$xy_cpt['args']['supports'] = array();
				if($this->xydac_checkbool($cpt['args']['supports']['title'])) array_push($xy_cpt['args']['supports'],'title');
				if($this->xydac_checkbool($cpt['args']['supports']['editor'])) array_push($xy_cpt['args']['supports'],'editor');
				if($this->xydac_checkbool($cpt['args']['supports']['author'])) array_push($xy_cpt['args']['supports'],'author');
				if($this->xydac_checkbool($cpt['args']['supports']['thumbnail'])) array_push($xy_cpt['args']['supports'],'thumbnail');
				if($this->xydac_checkbool($cpt['args']['supports']['excerpt'])) array_push($xy_cpt['args']['supports'],'excerpt');
				if($this->xydac_checkbool($cpt['args']['supports']['trackbacks'])) array_push($xy_cpt['args']['supports'],'trackbacks');
				if($this->xydac_checkbool($cpt['args']['supports']['custom-fields'])) array_push($xy_cpt['args']['supports'],'custom-fields');
				if($this->xydac_checkbool($cpt['args']['supports']['comments'])) array_push($xy_cpt['args']['supports'],'comments');
				if($this->xydac_checkbool($cpt['args']['supports']['revisions'])) array_push($xy_cpt['args']['supports'],'revisions');
				if($this->xydac_checkbool($cpt['args']['supports']['page-attributes'])) array_push($xy_cpt['args']['supports'],'page-attributes');
				$xy_cpt['args']['register_meta_box_cb'] = !empty($cpt['args']['register_meta_box_cb']) ? $cpt['args']['register_meta_box_cb'] : '';
				$xy_cpt['args']['menu_position'] = intval($cpt['args']['menu_position']);
				$xy_cpt['args']['menu_icon'] = !empty($cpt['args']['menu_icon']) ? $cpt['args']['menu_icon'] : null;
				$xy_cpt['args']['permalink_epmask'] = !empty($cpt['args']['permalink_epmask']) ? $cpt['args']['permalink_epmask'] : 'EP_PERMALINK';
				//$xy_cpt['args']['rewrite'] = false;
				$xy_cpt['args']['rewrite'] = $this->xydac_checkbool($cpt['args']['rewrite']['val']);
			 if(isset($cpt['args']['rewrite']['val']) && $this->xydac_checkbool($cpt['args']['rewrite']['val'])){
			 	$xy_cpt['args']['rewrite'] =array();
			 	$xy_cpt['args']['rewrite']['slug'] = !empty($cpt['args']['rewrite']['slug']) ? $cpt['args']['rewrite']['slug'] :$xy_cpt['name'];
			 	$xy_cpt['args']['rewrite']['with_front'] = $this->xydac_checkbool($cpt['args']['rewrite']['with_front']);
			 	$xy_cpt['args']['rewrite']['feeds'] = $this->xydac_checkbool($cpt['args']['rewrite']['feeds']);
			 	$xy_cpt['args']['rewrite']['pages'] = $this->xydac_checkbool($cpt['args']['rewrite']['pages']);
			 }
			 else
			 	$xy_cpt['args']['rewrite'] = $this->xydac_checkbool($cpt['args']['rewrite']['val']);
			 $xy_cpt['args']['query_var'] = $this->xydac_checkbool($cpt['args']['query_var']);
			 $xy_cpt['args']['can_export'] = $this->xydac_checkbool($cpt['args']['can_export']);
			 $xy_cpt['args']['show_in_nav_menus'] = $this->xydac_checkbool($cpt['args']['show_in_nav_menus']);
			 $xy_cpt['args']['show_in_menu'] = $this->xydac_checkbool($cpt['args']['show_in_menu']);
			 $xy_cpt['args']['has_archive'] = $this->xydac_checkbool($cpt['args']['has_archive']);
			 $xy_cpt['args']['map_meta_cap'] = $this->xydac_checkbool($cpt['args']['map_meta_cap']);
			 register_post_type( $xy_cpt['name'], $xy_cpt['args'] );
			 //register_post_type_with_rewrite_rules( $xy_cpt['name'], $xy_cpt['args'], array('front'=>$xy_cpt['name'],'structure'=>$cpt['args']['rewrite']['slug']) );
			 	
			 //adding enter_text_here to keep the data in db
			 $xy_cpt['args']['labels']['enter_text_here'] =  $cpt['args']['labels']['enter_text_here'];
			 if(!empty($cpt['def']['cat']) && $this->xydac_checkbool($cpt['def']['cat']))
			 	register_taxonomy_for_object_type('category',  $xy_cpt['name']);
			 if(!empty($cpt['def']['cat']) && $this->xydac_checkbool($cpt['def']['tag']))
			 	register_taxonomy_for_object_type('post_tag',  $xy_cpt['name']);
			 $cpts[$k]['args']['label'] = $xy_cpt['args']['label'];
			 $cpts[$k]['args']['labels'] = $xy_cpt['args']['labels'];
			}


	}

	function get_xydac_cms_tax_combo($post_id)
	{
		$post = get_post($post_id);
		if(isset(xydac()->modules->taxonomy_type)){
			$taxonomies = xydac()->modules->taxonomy_type->get_active();//array();//@todo: xydac_get_active_taxonomy();
			$e = '';
			if(is_array($taxonomies))
				foreach($taxonomies as $taxonomy)
				if(isset($taxonomy['object_type']) && is_array($taxonomy['object_type']))
				if(in_array($post->post_type,$taxonomy['object_type']) && $taxonomy['showascombobox']=='true')
				{
					$xy_terms = get_terms($taxonomy['name'], 'hide_empty=0');
					$val = wp_get_object_terms($post_id, $taxonomy['name']);
					wp_nonce_field( "XYDAC_CMS", 'xydac_cms_field_nonce' );
					$e.="<div class='xydac_cms_field'>";
					$e.="<label for='".$taxonomy['name']."' class='neo'>".$taxonomy['args']['label']."</label>";
					$e.='<input type="hidden" name="xydac_taxonomy_hidden[]" value="'.$taxonomy["name"].'" />';
					$e.='<select name='.$taxonomy['name'].' class="neo" id='.$taxonomy['name'].' >';
					$e.='<option class="'.$taxonomy['name'].'-option" value="" >'.__('NONE',XYDAC_CMS_NAME).'</option>';
					if(is_array($xy_terms))
						foreach ($xy_terms as $xy_term) {
						if (!is_wp_error($val) && !empty($val) && !strcmp($xy_term->slug, $val[0]->slug)  )
							$e.="<option class='". $taxonomy['name']."-options' value='" . $xy_term->slug . "' selected>" . $xy_term->name . "</option>\n";
						else
							$e.="<option class='". $taxonomy['name']."-options' value='" . $xy_term->slug . "'>" . $xy_term->name . "</option>\n";
					}

					$e.="</select>";
					$e.="</div>";
				}
				return $e;
		}
	}

}

?>