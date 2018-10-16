<?php
/*
Plugin Name: Ultimate CMS
Plugin URI: http://xydac.com/ultimate-cms/
Description: Ultimate Tool for working with  Custom Post Type, Page Type, Custom Archives, Taxonomies, Forms. Enhances WordPress for All kinds of Custom Types.
Author: XYDAC
Author URI: http://xydac.com/
Version: 2.1.0
License: GPL2*/

if ( !defined( 'XYDAC_CMS_NAME' ) )define('XYDAC_CMS_NAME',"ultimate-cms");
if ( !defined( 'XYDAC_CMS_USER_API_KEY' ) )define('XYDAC_CMS_USER_API_KEY',"xydac_cms_api_key");
if ( !defined( 'XYDAC_CMS_OPTIONS' ) )define('XYDAC_CMS_OPTIONS',"XYDAC_CMS_OPTIONS");
if ( !defined( 'XYDAC_CMS_MODULES' ) )define('XYDAC_CMS_MODULES',"xydac_cms_modules");
if ( !defined( 'XYDAC_CMS_MODULES_BACKUP' ) )define('XYDAC_CMS_MODULES_BACKUP',"xydac_cms_modules_backup");
if ( !defined( 'XYDAC_CMS_MODULES_OLD_HASH' ) )define('XYDAC_CMS_MODULES_OLD_HASH',"xydac_cms_modules_old_hash");
if ( !defined( 'XYDAC_UCMS_FORMOPTION' ) )define('XYDAC_UCMS_FORMOPTION',"XYDAC_UCMS_FORMOPTION");

if ( !defined( 'DS' ) )define('DS', DIRECTORY_SEPARATOR);
global $xydac_cms_fields;

require_once ABSPATH . 'wp-admin'.DS.'includes'.DS.'plugin.php' ;
//add_action('plugins_loaded', create_function('','deactivate_plugins("ultimate-taxonomy-manager'.DS.'ultimate-taxonomy-manager.php");'));
//File includes
require_once 'class-field-type.php';
require_once 'class-xydac-ultimate-cms-core.php';
require_once 'dao.php';
require_once 'class-xydac-cms-module.php';
require_once ABSPATH.'wp-includes'.DS.'class-IXR.php';
require_once 'class-xydac-synckeys.php';
require_once 'class-xydac-ucms-dbupdates.php';
require_once 'class-xydac-ucmsoption.php';
require_once 'class-xydac-snippets.php';
require_once 'class-xydac-cms-home.php';


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
                    'snippets'=>dirname(__FILE__).DS.'snippets'.DS.'',
					'userMods'=> ABSPATH.DS.'wp-content'.DS.'ultimate_cms'.DS.''
			);
			
			
			
			self::$menu_slug = 'xydac_ultimate_cms';
			self::cms()->dao = new xydac_options_dao();
			self::cms()->modules = new stdClass();
			self::cms()->dao->register_option(XYDAC_CMS_MODULES);
			self::cms()->dao->register_option(XYDAC_CMS_MODULES_BACKUP);
			self::cms()->dao->register_option(XYDAC_CMS_MODULES.'_active');
            self::cms()->dao->register_option(XYDAC_CMS_MODULES_OLD_HASH);
            self::cms()->options = new xydac_ucmsoption();
			self::cms()->oldhash = self::cms()->dao->get_options(XYDAC_CMS_MODULES_OLD_HASH);
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
			add_action( 'xydac_cms_activate', array(self::$instance,'xydac_activate_all_modules'));
			register_activation_hook( __FILE__, array('xydac_ucms_dbupdates','xydac_cms_activate') );

			
		};
		
		return self::$instance;
	}
	
    
	/*------------------------------------------MODULES SECTION-----------------------------*/
	private static function load_modules(){
		self::get_module_data();
		//self::cms()->dao->delete_all_object(XYDAC_CMS_MODULES);//--removing deletion @performance issue
		$module_insert = array();
		foreach(self::cms()->allModules as $k=>$module){
				
				require_once $module['file']['dirpath'].$module['file']['filename'];
                $classname='';
				if ( substr($module['file']['filename'], -4) == '.php' ){
                    if($module['file']['dirname']!='.')
					   $classname = str_replace('-','_',substr($module['file']['filename'],strlen($module['file']['dirname'])+7,-4));
                    else
                        $classname = str_replace('-','_',substr($module['file']['filename'],6,-4));
                }
				if(class_exists($classname) && (($module['type']!='Core-Module' && is_array(self::cms()->active) && in_array($module['name'],self::cms()->active)) ||$module['type']=='Core-Module')){
					new $classname();
				}
				//if($module['type']=='Core-Module')
					//unset(self::cms()->allModules[$k]);
				//else
					array_push($module_insert,array('name'=>$module['name'],'type'=>$module['type'],'author'=>$module['author'],'description'=>$module['description'],'moduleurl'=>$module['moduleurl'],'url'=>$module['url'],'classname'=>$classname));

		}
        if(!is_serialized($module_insert))
            $newhash = md5(maybe_serialize($module_insert));
        else
            $newhash = md5($module_insert);
    
        if($newhash!=self::cms()->oldhash && !empty($newhash)){
		  self::cms()->dao->insert_object_hard(XYDAC_CMS_MODULES,$module_insert);
		  self::cms()->dao->insert_object_hard(XYDAC_CMS_MODULES_OLD_HASH,$newhash);   
        }
        
	}
	private static function get_module_data(){
		foreach (self::$dirpath as $mname=>$path){
			$modules = array();
			$module_headers = array(
					'name'			=> 'Module Name',
					'type'			=> 'Type',
					'description'	=> 'Description',
					'author'		=> 'Author',
					'moduleurl'		=> 'Module URI',
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
		wp_enqueue_code_editor( array( 'type' => 'text/html', 'css', 'javscript', 'application/json' ) );
		add_thickbox();
		xydac_fieldtypes_init();


		$plugin = plugin_basename( __FILE__ );
		add_filter( "plugin_action_links_$plugin", array($this,'xydac_plugin_add_settings_link') ,10,1);
		
		add_action( 'admin_notices', array($this,'wp_info_notice' ));
	}
	
	/**
	 * @Since 2.0
	 */
	function xydac_plugin_add_settings_link( $links ) {
		array_push($links, 
			'<a href="admin.php?page=xydac_ultimate_cms">' . __( 'Settings' ) . '</a>'
		);
		return $links;
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
		$xydac_main_menu = add_menu_page('Ultimate CMS', 'Ultimate CMS', 'manage_xydac_cms', 'xydac_ultimate_cms', array($this,'xydac_cms_main1'), 'dashicons-layout');
	}
    
    //This method is used by class-field-type to get a list of active field types
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

	function wp_info_notice() {
		if( get_transient( XYDAC_CMS_NAME.'_activated' ) ){
		?>
		<div class="updated notice is-dismissible">
			<p><?php _e( 'Ultimate CMS Activated. Please Activate Modules from <a href="admin.php?page=xydac_ultimate_cms">Ultimate CMS Settings</a> Page to use the plugin.', XYDAC_CMS_NAME ); ?></p>
		</div>
		<?php
		  delete_transient( XYDAC_CMS_NAME.'_activated' );
		}
	}

	function xydac_activate_all_modules(){
		set_transient( XYDAC_CMS_NAME.'_activated', true, 10 );
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
	public function xydac_show_donate_link($showimage=false){
		// remove donate link :)
	
	}

}

xydac();

?>