<?php
/*
Plugin Name: Ultimate CMS
Plugin URI: http://xydac.com/ultimate-cms/
Description: This is an Easy to use Plugin to Create, Customize, Manage Custom Post Type,Custom Page Type, Custom Archives, Custom Taxonomies.
Author: XYDAC
Author URI: http://xydac.com/
Version: 1.0.6
License: GPL2*/

if ( !defined( 'XYDAC_CMS_NAME' ) )define('XYDAC_CMS_NAME',"ultimate-cms");
if ( !defined( 'XYDAC_CMS_USER_API_KEY' ) )define('XYDAC_CMS_USER_API_KEY',"xydac_cms_api_key");
if ( !defined( 'XYDAC_CMS_OPTIONS' ) )define('XYDAC_CMS_OPTIONS',"XYDAC_CMS_OPTIONS");
if ( !defined( 'XYDAC_CMS_MODULES' ) )define('XYDAC_CMS_MODULES',"xydac_cms_modules");
if ( !defined( 'DS' ) )define('DS', DIRECTORY_SEPARATOR);
global $xydac_cms_fields;

require_once ABSPATH . 'wp-admin'.DS.'includes'.DS.'plugin.php' ;
add_action('plugins_loaded', create_function('','deactivate_plugins("ultimate-taxonomy-manager'.DS.'ultimate-taxonomy-manager.php");'));
//File includes
require_once 'class-field-type.php';
require_once 'class-xydac-ultimate-cms-core.php';
require_once 'dao.php';
require_once 'class-xydac-cms-module.php';
require_once 'class-xydac-cms-home.php';
require_once ABSPATH.'wp-includes'.DS.'class-IXR.php';
require_once 'class-xydac-synckeys.php';

function xydac()
{
	return xydac_ultimate_cms::cms();
}

class xydac_ultimate_cms{

	protected static $instance;

	//has path of all important directories
	protected static $dirpath = array();
	protected static $menu_slug;
	protected  static $log_messages;
	protected  static $debug =true;

	public static function cms(){
		if(!(self::$instance instanceof self)){
			self::$instance = new self();
			self::$dirpath	= array(	
					'modules'=>dirname(__FILE__).DS.'modules'.DS.'',
					'mods'=> dirname(__FILE__).DS.'mods'.DS.'',
					'fieldTypes'=>dirname(__FILE__).DS.'fieldTypes'.DS.'',
					'userMods'=> ABSPATH.DS.'wp-content'.DS.'ultimate_cms'.DS.''
			);
			
			
			
			self::$menu_slug = 'xydac_ultimate_cms';
			self::cms()->dao = new xydac_options_dao();
			self::cms()->modules = new stdClass();
			self::cms()->dao->register_option(XYDAC_CMS_MODULES);
			self::cms()->dao->register_option(XYDAC_CMS_MODULES.'_active');
			self::cms()->active = self::cms()->dao->get_options(XYDAC_CMS_MODULES.'_active');
			self::cms()->allModules = array();
			self::cms()->apikey = get_option(XYDAC_CMS_USER_API_KEY);
			self::cms()->synkeys = new xydac_synckeys();
			self::load_modules();
			

			//die(var_dump(self::cms()->modules));
			//--------------------------Action and filters Zone
			add_action('init',array(self::$instance,'xydac_cms_init'));
			add_action('admin_menu', array(self::$instance,'xydac_cms_admin_menu'));
			add_action('admin_head',array(self::$instance, 'xydac_cms_admin_head'));
			add_action('wp_head', array(self::$instance,'xydac_cms_site_head'));
			add_action('admin_footer', array(self::$instance,'xydac_cms_admin_foot'));
			add_action( 'xydac_cms_activate', array(self::$instance,'xydac_taxonomy_activate'));
			register_activation_hook( __FILE__, array(self::$instance,'xydac_cms_activate') );
		};
		
		return self::$instance;
	}
	/*------------------------------------------MODULES SECTION-----------------------------*/
	private static function load_modules(){
		self::get_module_data();
		self::cms()->dao->delete_all_object(XYDAC_CMS_MODULES);
		$module_insert = array();
		foreach(self::cms()->allModules as $k=>$module){
				
				require_once $module['file']['dirpath'].$module['file']['filename'];
				if ( substr($module['file']['filename'], -4) == '.php' )
					$classname = str_replace('-','_',substr($module['file']['filename'],strlen($module['file']['dirname'])+7,-4));
				if(class_exists($classname) && (($module['type']!='Core-Module' && is_array(self::cms()->active) && in_array($module['name'],self::cms()->active)) ||$module['type']=='Core-Module')){
					new $classname();
				}
				//if($module['type']=='Core-Module')
					//unset(self::cms()->allModules[$k]);
				//else
					array_push($module_insert,array('name'=>$module['name'],'type'=>$module['type'],'author'=>$module['author'],'description'=>$module['description']));

		}
		self::cms()->dao->insert_object(XYDAC_CMS_MODULES,$module_insert,true);
	}
	private static function get_module_data(){
		foreach (self::$dirpath as $mname=>$path){
			$modules = array();
			$module_headers = array(
					'name'			=> 'Module Name',
					'type'			=> 'Type',
					'description'	=> 'Description',
					'author'		=> 'Author',
					'url'			=> 'Author URI',
					'version'		=> 'Version',
			);
	
			$modules_root = rtrim( $path, '\\' );//Knut Sparhell patch.
			if(is_dir($modules_root)){
				$modules_dir = @opendir($modules_root);
				$module_files = array();
		
				if($modules_dir)
				{
					while (($file = readdir( $modules_dir ) ) !== false ) {
						if ( substr($file, 0, 1) == '.' )
							continue;
						if ( is_dir( $modules_root.DS.$file ) ) {
							$modules_subdir = @ opendir( $modules_root.DS.$file );
							if ( $modules_subdir ) {
								while (($subfile = readdir( $modules_subdir ) ) !== false ) {
									if ( substr($subfile, 0, 1) == '.' )
										continue;
									if ( substr($subfile, -4) == '.php' )
										$module_files[] = "$file".DS."$subfile";
								}
								closedir( $modules_subdir );
							}
						} else {
							if ( substr($file, -4) == '.php' )
								$module_files[] = $file;
						}
					}
					@closedir( $modules_dir );
					@closedir($modules_dir);
					@closedir($modules_dir);
					
					foreach($module_files as $file){
						if(!is_readable($modules_root.'/'.$file)) continue;
						$data = get_file_data($modules_root.'/'.$file, $module_headers);
					
						if(empty($data['name'])) continue;
						if(($mname!='userMods' && $data['type']!='Core-Module') || ($mname=='userMods' && $data['type']!='Core-Module'))
							$data['type'] = $mname;
						else if($mname=='userMods' && $data['type']=='Core-Module')
							continue;
						$data['file']['filename'] = $file;
						$data['file']['dirpath'] = $path;
						$data['file']['dirname'] = dirname($file);
						array_push(self::cms()->allModules,$data);
						//$modules[dirname($file)] = $data;
					}
				}
			}
		}
	}
	public function xml_rpc_client($method,$id=null,$args=array()) {
		$nonce =  wp_create_nonce($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_TIME']);
		delete_transient('xydac_ultimate_cms_nonce');
		set_transient( 'xydac_ultimate_cms_nonce',$nonce, 50 );
		$log =  plugins_url( 'ultimate-cms/xydacverify.php?nonce='.$nonce , dirname(__FILE__) );
		$pwd =  xydac()->apikey;
		$xmlrpc = 'http://www.xydac.com/xmlrpc.php';
		$client = new IXR_Client($xmlrpc);
		if($id==null)
			$client->query($method, '', $log, $pwd,$args);
		else if(empty($args))
			$client->query($method, '', $log, $pwd,$id);
		else
			$client->query($method, '', $log, $pwd,$id,$args);
		return $client;
		
	}
	/*------------------------------------------MODULES SECTION-----------------------------*/
	//@todo: handle ajax 
	function xydac_ajax_handler()
	{
		add_action("xydac_ajax_handler");
		die;
	}
	//-----------------------------------------------------Old Methods below
	function xydac_cms_init()
	{
		$role = get_role("administrator");
		$role->add_cap("manage_xydac_cms");
		wp_enqueue_script("jquery");
		xydac_fieldtypes_init();
		
	}

	function xydac_cms_site_head()
	{
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.get_bloginfo('wpurl').'/wp-content/plugins/'.XYDAC_CMS_NAME.'/css.php" />';
		echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/'.XYDAC_CMS_NAME.'/script.php"></script>';
	}

	function xydac_cms_admin_head()
	{
		$style_url = apply_filters('xydac_cms_admin_style_url',get_bloginfo('wpurl').'/wp-content/plugins/'.XYDAC_CMS_NAME.'/css.php?type=admin');
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$style_url.'" />';
		$script_url = apply_filters('xydac_cms_admin_script_url',get_bloginfo('wpurl').'/wp-content/plugins/'.XYDAC_CMS_NAME.'/script.php?type=admin');		
		echo '<script type="text/javascript" src="'.$script_url.'"></script>';
	}
	function xydac_cms_admin_foot(){
		self::xydac_cms_display_logs();
	}

	function xydac_cms_main1(){
		new xydac_ultimate_cms_home();
		
	}
	function xydac_cms_admin_menu()
	{
		$xydac_main_menu = add_menu_page('Ultimate CMS', 'Ultimate CMS', 'manage_xydac_cms', 'xydac_ultimate_cms', array($this,'xydac_cms_main1'));
	}
	function xydac_cms_build_active_field_types()
	{
		$active = array();
		foreach (self::cms()->modules as $module)
		{
			if($module->has_custom_fields())
			{
				$names = $module->get_active_names();
				if(is_array($names))
				foreach($names as $name)
				{
					$types = $module->get_active_fieldtypes($name);
					if(is_array($types))
						foreach ($types as $type)
							if(!in_array($type, $active))
								array_push($active,$type);
				}
			}
		}
		//update_option('xydac_active_field_types',$active);
		return $active; 
	}

	/**
	 * Not used now....cleanup required.
	 */
	function xydac_cms_cpt_field_convert()
	{
		global $wpdb;
		$cpts = get_reg_cptName();
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
			update_option('xydac_cms_ver','1.0');
	}
	function xydac_cms_activate()
	{
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					//$this->xydac_cms_cpt_field_convert();
					$this->xydac_taxonomy_fix();
					do_action('xydac_cms_activate');
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		$this->xydac_taxonomy_fix();
		//$this->xydac_cms_cpt_field_convert();
		do_action('xydac_cms_activate');
	}
	public static function xydac_taxonomy_fix(){
	global $wpdb;
		$fields = $wpdb->get_results($wpdb->prepare("SELECT tax_name,field_name,field_label,field_type,field_desc,field_val FROM {$wpdb->prefix}taxonomyfield order by tax_name"));
		if(isset(self::cms()->modules->taxonomy_type)){
			$field_option = self::cms()->modules->taxonomy_type->get_registered_option('field');
			$tax_names = array();
			foreach($fields as $field){
				
				$tax_option = $field_option.'_'.$field->tax_name;
				
				self::cms()->dao->register_option($tax_option);
				$tax_field_data = self::cms()->dao->get_options($tax_option);

				if(!isset($tax_names[$field->tax_name]) && !$tax_field_data)
					$tax_names[$field->tax_name] = true;
				if(isset($tax_names[$field->tax_name]) && $tax_names[$field->tax_name])
				{
					$field_arr = array('field_name'=>$field->field_name,'field_label'=>$field->field_label,'field_type'=>$field->field_type,'field_desc'=>$field->field_desc,'field_val'=>$field->field_val,'field_order'=>0);
					self::cms()->dao->insert_object($tax_option,$field_arr);
				
				}
			}
		}
	}
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
		$a = $wpdb->query($sql);

	}
	function is_xydac_ucms_pro(){
		//return false;
		$k = get_option(XYDAC_CMS_USER_API_KEY);
		if(empty($k))
			return false;
		else
			return true;
	}
	
	//-ERROR LOGGING AND HANDLING BEGIN --NOT being used anywhere currently
	function xydac_cms_display_logs($type='WARNING')
	{
		if(false){
		echo<<<DEBUG
		<style>
		 #debug{color:#999;background:#252628;font:14px Monaco, Andale Mono, monospace;position:fixed;width:400px;height:32px;overflow:scroll;left:20px;top:30px;z-index: 100;opacity: 0.25;-webkit-transition: 0.15s linear all;-moz-transition: 0.15s linear all;-ms-transition: 0.15s linear all;-o-transition: 0.15s linear all;transition: 0.15s linear all;}
          #debug:hover{opacity:1;-webkit-transition: 0.15s linear all;-moz-transition: 0.15s linear all;-ms-transition: 0.15s linear all;-o-transition: 0.15s linear all;transition: 0.15s linear all;width:70%;height:400px;}
          #debug p{padding:7px 10px;margin:0;}
          #debug p.lead{background: #000;color:#ddd;font-weight:bold;}
          #debug p.warning{background: #a2281d;color:#ffa074;}
          #debug p.warning.even{background: #b02e21;}
          #debug p.notice.even{background: #2b2c2e;}
          #debug a{color:#fff;text-decoration:underline;}
          #page #debug{display:none;} /* to avoid flicker before messages are appended to the <body> */
		</style>
		<div id="debug">
DEBUG;
		foreach(self::$log_messages[$type] as $key => $message)
			echo '<p class="warning '.(($key %2) ? 'even' : '').'">'.$message.'</p>';
		echo "</div>";
		}
	}

	public function log($message,$obj=null, $code = "WARNING"){
		if(self::$debug)
		{
			if( is_array( $obj ) || is_object( $obj ) ){
				self::$log_messages[$code][] = $message." ".print_r( $obj, true );
			} else {
				self::$log_messages[$code][] = $message." ".$obj;
			}

		}

		return $obj;
	}
	//-ERROR LOGGING AND HANDLING END
	public function xydac_show_donate_link($showimage=true){
		if(!xydac()->is_xydac_ucms_pro()){
		echo '
		<p class="xydacdonation">
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=nikhilseth1989%40gmail%2ecom&item_name=WordPress%20Plugin%20(Ultimate%20CMS)&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8">
		';
		if($showimage)
			echo '<img src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"/>';
		echo 'You might want to help building this plugin with some Donations. Please Click here to Donate';
		if($showimage)
			echo '<img src="http://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"/>';
		echo'
		</a>
		</p>
		';
		}
	
	}

}

xydac();

?>