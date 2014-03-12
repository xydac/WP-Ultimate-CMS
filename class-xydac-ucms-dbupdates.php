<?php
/**
 * Database update scripts are stored here.
 */
if ( !defined( 'XYDAC_CMS_DBVER' ) )define('XYDAC_CMS_DBVER',"xydac_cms_ver");
class xydac_ucms_dbupdates{

    
    
    function xydac_cms_activate()
	{
        $var = new xydac_ucms_dbupdates();
		global $wpdb;
        $dbver = get_option('xydac_cms_ver');
		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$var->trigger_update();
					do_action('xydac_cms_activate');
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		$var->trigger_update();
		do_action('xydac_cms_activate');
	}
    function trigger_update(){
        if($dbver<1.1){
            //$this->xydac_cms_cpt_field_convert();
        }
        if($dbver<1.2)
            $this->xydac_taxonomy_fix();   
    }
	
    
    /**
     * DB Version 1.1
     */
    public static function xydac_taxonomy_fix(){
	global $wpdb;
        
        $fields = $wpdb->get_results("SELECT tax_name,field_name,field_label,field_type,field_desc,field_val FROM {$wpdb->prefix}taxonomyfield order by tax_name");
        
		if(isset(xydac()->modules->taxonomy_type)){
			$field_option = xydac()->modules->taxonomy_type->get_registered_option('field');
			$tax_names = array();
			foreach($fields as $field){
				
				$tax_option = $field_option.'_'.$field->tax_name;
				
				xydac()->dao->register_option($tax_option);
				$tax_field_data = xydac()->dao->get_options($tax_option);

				if(!isset($tax_names[$field->tax_name]) && !$tax_field_data)
					$tax_names[$field->tax_name] = true;
				if(isset($tax_names[$field->tax_name]) && $tax_names[$field->tax_name])
				{
					$field_arr = array('field_name'=>$field->field_name,'field_label'=>$field->field_label,'field_type'=>$field->field_type,'field_desc'=>$field->field_desc,'field_val'=>$field->field_val,'field_order'=>0);
					xydac()->dao->insert_object($tax_option,$field_arr);
				
				}
			}
		}
        update_option(XYDAC_CMS_DBVER,'1.1');
	}
    /**
	 * db version 1.0
     * Not being used currently
	 */
	function xydac_cms_cpt_field_convert()
	{
		global $wpdb;
		$cpts = xydac()->modules->post_type->get_main();
		if(is_array($cpts))
			foreach($cpts as $cpt)
			{
				$fields = getCptFields($cpt);

				if(is_array($fields) && !empty($fields))
					foreach($fields as $field)
					{
						$metas = $wpdb->get_results("SELECT meta_id, meta_value FROM ".$wpdb->postmeta." WHERE meta_key ='".$field['field_name']."'");
						foreach($metas as $meta)
						{
							$meta->meta_value = maybe_unserialize($meta->meta_value);
							$r = false;
							if(is_array($meta->meta_value))
							{
								foreach($meta->meta_value as $k=>$v)
									if($k==$field['field_type'])
									{
										$wpdb->query("UPDATE ".$wpdb->postmeta." SET meta_value='".$v."' WHERE meta_id = ".$meta->meta_id);
									}
							}
						}
						if(!in_array($field['field_type'],$xydac_active_field_types))
							array_push($xydac_active_field_types,$field['field_type']);
					}
			}
				
			update_option('xydac_active_field_types',$xydac_active_field_types);
			update_option(XYDAC_CMS_DBVER,'1.0');
	}
    
    }
    ?>
