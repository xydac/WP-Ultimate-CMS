<?php
/*
 Module Name:	Taxonomy
Type:			Module
Description:	Xydac Taxonomy module
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
include 'class-field.php';
include 'class-xydac-taxonomy-type-fields.php';
include 'class-xydac-taxonomy-type-functions.php';
include 'class-xydac-taxonomy-type-manager.php'; // used for defining taxonomy details

/*Include widget files*/
include 'class-xydac-tax-detail-widget.php'; //adds widget for taxonomy display.
include 'class-xydac-tax-term-list-widget.php'; //adds widget for taxonomy term list display.

class xydac_taxonomy_type extends xydac_cms_module{

	function __construct()
	{
		parent::__construct('taxonomy_type',array('module_label'=>'Xydac Taxonomy',
				'has_custom_fields'=>true,
				'uses_active'=>true,
				'registered_option'=>array('main'=>'xydac_taxonomies',
						'active'=>'xydac_taxonomies_active',
						'field'=>'xydac_taxonomies'),
				'base_path'=>get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms_taxonomy_type',
				'menu_position'=>'top'
		));
		new xydac_taxonomy_functions();

		//add_action( 'xydac_cms_activate', array($this,'xydac_taxonomy_activate'));
	}



	function init(){

		global $wp_version;
		global $wpdb;
		$wpdb->taxonomymeta = "{$wpdb->prefix}taxonomymeta";
		$taxonomies = stripslashes_deep($this->get_active());//xydac_get_active_taxonomy();
		if (is_array($taxonomies) && !empty($taxonomies))
			foreach ($taxonomies  as $k=>$taxonomy )
			{
				$xy_tax['name']= $taxonomy['name'];
				if(isset($taxonomy['object_type']))
					$xy_tax['object_type']= $taxonomy['object_type'];
				else
					$xy_tax['object_type']=null;
				$xy_tax['args']['labels']['name'] =  (!empty($taxonomy['args']['labels']['name'])  ? $taxonomy['args']['labels']['name'] : ( !empty($taxonomy['args']['label']) ? $taxonomy['args']['label'] : $taxonomy['name']));
				$xy_sname = $this->xydac_singular($xy_tax['name']);
				$xy_slabel = $this->xydac_singular($xy_tax['args']['labels']['name']);
				$xy_tax['args']['labels']['singular_name'] = ( !empty($taxonomy['args']['labels']["singular_label"]) ? $taxonomy['args']['labels']["singular_label"] : $xy_sname);
				$xy_tax['args']['labels']['search_items'] = ( !empty($taxonomy['args']['labels']["search_items"]) ) ? $taxonomy['args']['labels']["search_items"] : 'Search ' .$taxonomy['args']['label'];
				$xy_tax['args']['labels']['popular_items'] = ( !empty($taxonomy['args']['labels']["popular_items"]) ) ? $taxonomy['args']['labels']["popular_items"] : 'Popular ' .$taxonomy['args']['label'];
				$xy_tax['args']['labels']['all_items'] = ( !empty($taxonomy['args']['labels']["all_items"]) ) ? $taxonomy['args']['labels']["all_items"] : 'All ' .$taxonomy['args']['label'];
				$xy_tax['args']['labels']['parent_item'] = ( !empty($taxonomy['args']['labels']["parent_item"]) ) ? $taxonomy['args']['labels']["parent_item"] : 'Parent ' .$xy_slabel;
				$xy_tax['args']['labels']['parent_item_colon'] = ( !empty($taxonomy['args']['labels']["parent_item_colon"]) ) ? $taxonomy['args']['labels']["parent_item_colon"] : 'Parent '.$xy_slabel.':';
				$xy_tax['args']['labels']['edit_item'] = ( !empty($taxonomy['args']['labels']["edit_item"]) ) ? $taxonomy['args']['labels']["edit_item"] : 'Edit ' .$xy_slabel;
				$xy_tax['args']['labels']['update_item'] = ( !empty($taxonomy['args']['labels']["update_item"]) ) ? $taxonomy['args']['labels']["update_item"] : 'Update ' .$xy_slabel;
				$xy_tax['args']['labels']['add_new_item'] = ( !empty($taxonomy['args']['labels']["add_new_item"]) ) ? $taxonomy['args']['labels']["add_new_item"] : 'Add New ' .$xy_slabel;
				$xy_tax['args']['labels']['new_item_name'] = ( !empty($taxonomy['args']['labels']["new_item_name"]) ) ? $taxonomy['args']['labels']["new_item_name"] : 'New ' .$xy_slabel. ' Name';
				$xy_tax['args']['labels']['separate_items_with_commas'] = ( !empty($taxonomy['args']['labels']["separate_items_with_commas"]) ) ? $taxonomy['args']['labels']["separate_items_with_commas"] : 'Separate ' .$taxonomy['args']['label']. ' with commas';
				$xy_tax['args']['labels']['add_or_remove_items'] = ( !empty($taxonomy['args']['labels']["add_or_remove_items"]) ) ? $taxonomy['args']['labels']["add_or_remove_items"] : 'Add or remove ' .$taxonomy['args']['label'];
				$xy_tax['args']['labels']['choose_from_most_used'] = ( !empty($taxonomy['args']['labels']["choose_from_most_used"]) ) ? $taxonomy['args']['labels']["choose_from_most_used"] : 'Choose from the most used ' .$taxonomy['args']['label'];
				$xy_tax['args']['labels']['view_item'] = ( !empty($taxonomy['args']['labels']["view_item"]) ) ? $taxonomy['args']['labels']["view_item"] : 'View ' .$taxonomy['args']['label'];
				$xy_tax['args']['label'] = $xy_tax['args']['labels']['name'];
				$xy_tax['args']['public']= $this->xydac_checkbool($taxonomy['args']['public']);
				$xy_tax['args']['show_in_nav_menus']=$this->xydac_checkbool($taxonomy['args']['show_in_nav_menus']);
				$xy_tax['args']['show_ui']= $this->xydac_checkbool($taxonomy['args']['show_ui']);
				$xy_tax['args']['show_tagcloud']= $this->xydac_checkbool($taxonomy['args']['show_tagcloud']);
				$xy_tax['args']['hierarchical']= $this->xydac_checkbool($taxonomy['args']['hierarchical']);
				$xy_tax['args']['rewrite']= $this->xydac_checkbool($taxonomy['args']['rewrite']['val']);
				if($xy_tax['args']['rewrite']){
					$xy_tax['args']['rewrite'] = array();
					$xy_tax['args']['rewrite']['slug']= (!empty($taxonomy['args']['rewrite']['slug']) ? $taxonomy['args']['rewrite']['slug'] : $xy_tax['name']);
					$xy_tax['args']['rewrite']['with_front']= $this->xydac_checkbool($taxonomy['args']['rewrite']['with_front']);
					if(floatval($wp_version)>3.0)
						@$xy_tax['args']['rewrite']['hierarchical']= $this->xydac_checkbool($taxonomy['args']['rewrite']['hierarchical']);
				}
				$xy_tax['args']['query_var'] = ( !empty($taxonomy['args']['query_var']) ? $taxonomy['args']['query_var'] : $taxonomy['name']);
				if(isset($taxonomy['args']['capabilities']))
					$xy_tax['args']['capabilities'] =  $taxonomy['args']['capabilities'];
				if(isset($taxonomy['args']['update_count_callback']) && !empty($taxonomy['args']['update_count_callback']))
					$xy_tax['args']['update_count_callback'] =  $taxonomy['args']['update_count_callback'];
				register_taxonomy($xy_tax['name'],$xy_tax['object_type'],$xy_tax['args']);
				$taxonomies[$k]['args']['labels'] = $xy_tax['args']['labels'];
				$taxonomies[$k]['args']['query_var'] = $xy_tax['args']['query_var'];
				$taxonomies[$k]['args']['rewrite']['slug'] = $xy_tax['args']['rewrite']['slug'];
					
			}
	}
	//this is not working sumhow
	function xydac_taxonomy_activate()
	{
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$charset_collate = '';
		if (!empty ($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		if (!empty ($wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}taxonomymeta (
		meta_id bigint(20) unsigned NOT NULL auto_increment,
		taxonomy_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) default NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id),
		KEY taxonomy_id (taxonomy_id),
		KEY meta_key (meta_key)
		) $charset_collate;";
		$wpdb->query($sql);

	}


}

?>