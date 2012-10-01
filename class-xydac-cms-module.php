<?php
require_once('dao.php');
/**
 * This class is used to create a module that can have fields can support active...
 * @author Xydac
 *
 */
abstract class xydac_cms_module{
	private $VALID_OPTION_LIST = array('main','active','field');
	//String: Name of the module
	private $module_name;
	//String: Label of the module
	private $module_label;
	//String: path
	private $path;
	//bool: does it uses custom field
	private $has_custom_fields;
	//bool: provides an option of active and inactive objects
	private $uses_active;
	//array: Name of the option used for registering
	private $registered_option;
	//xydac_options_dao: the Data Access Layer
	private $dao;
	//Base Path for module page
	private $base_path;
	//Default Tabs
	private $tabs;
	//Position of menu on plugin page valid option : top/sub
	private $menu_position;
	//XydacSync Module
	private $xydac_sync;

	/*----------------------------Constructor---------------------*/
	//array('module_label'=>'','has_custom_fields'=>,'uses_active'=>,'registered_option'=>array('main'=>'','active'=>'','field'=>''),'base_path'=>'','menu_position'=>'top/sub')
	function __construct($module_name,$args=null){
		xydac()->modules->$module_name =  $this;
		//initialise variables
		$this->module_name = $module_name;
		$this->module_label = (!empty($args) && is_array($args) && isset($args['module_label']) && !empty($args['module_label'])) ? $args['module_label'] : $module_name;
		$this->has_custom_fields = (!empty($args) && is_array($args) && isset($args['has_custom_fields']) && !empty($args['has_custom_fields']) && true==$args['has_custom_fields']) ? true : false;
		$this->uses_active = (!empty($args) && is_array($args) && isset($args['uses_active']) && !empty($args['uses_active']) && true==$args['uses_active']) ? true : false;
		$this->registered_option = (!empty($args) && is_array($args) && isset($args['registered_option']) && !empty($args['registered_option'])) ? $args['registered_option'] : null;
		$this->base_path = (!empty($args) && is_array($args) && isset($args['base_path']) && !empty($args['base_path'])) ? $args['base_path'] : null;
		$this->menu_position = (!empty($args) && is_array($args) && isset($args['menu_position']) && !empty($args['menu_position'])) ? $args['menu_position'] : null;
		$this->xydac_sync = (!empty($args) && is_array($args) && isset($args['xydac_sync']) && !empty($args['xydac_sync'])) ? true : false;
		$this->xydac_sync = true;
		// @todo: path has not been defined.
		$this->dao = xydac()->dao;
		//Create an array to hold dao option
		$dao_opt_arr = array();
		if(is_array($this->registered_option)){
			foreach($this->registered_option as $opt=>$val)
				$dao_opt_arr[$val]=$module_name." ".$opt;
			//Register options to be used by DAO.
			$this->dao->register_option($dao_opt_arr);
		}
		if($this->has_custom_fields){
			$active_opt_arr = array();
			$active_names = $this->get_active_names();
			if(is_array($active_names))
				foreach($active_names as $opt=>$val)
				$active_opt_arr[$this->get_registered_option('field').'_'.$val]=$module_name." ".$val;
			$this->dao->register_option($active_opt_arr);
		}
		//Creating default View Component Tabs
		if(!empty($this->base_path) && !isset($args["tabs"])){

			$this->tabs = array('module'=>array('name'=>$this->module_name,
					'href'=>$this->base_path.'&sub='.$this->module_name,
					'label'=>$this->module_label,
					'default'=>true));
			if($this->has_custom_fields){
				$this->tabs['fields']=array('name'=>$this->module_name.'_fields',
						'href'=>$this->base_path.'&sub='.$this->module_name.'_fields',
						'label'=>$this->module_label.' Fields',
						'default'=>false) ;
			}
			if($this->xydac_sync){
				$this->tabs['xydac_sync']=array('name'=>$this->module_name.'_xydac_sync',
						'href'=>$this->base_path.'&sub='.$this->module_name.'_xydac_sync',
						'label'=>$this->module_label.' Sync',
						'default'=>false) ;
			}
		}else if(isset($args["tabs"]) && !empty($args["tabs"]) && is_array($args["tabs"]))
			$this->tabs = $args["tabs"];

		//Creating Menu
		if('top'==$this->menu_position)
			add_action('admin_menu', array($this,'handle_menu'),100);
		add_action( 'init', array($this,'init') , 1);
	}

	/*-----------Getters--------------*/
	public function get_module_name(){
		return $this->module_name;
	}
	public function get_module_label(){
		return $this->module_label;
	}
	public function get_path(){
		return $this->path;
	}
	public function has_custom_fields(){
		return $this->has_custom_fields;
	}
	public function uses_active(){
		return $this->uses_active;
	}
	public function get_registered_option($type=false){
		if(!$type || !in_array($type,$this->VALID_OPTION_LIST))
			return $this->registered_option;
		else
			return $this->registered_option[$type];
	}
	public function get_base_path(){
		return $this->base_path;
	}
	public function get_tabs(){
		return $this->tabs;
	}
	public function init(){
	}//function to be overrided in child class.
	/*-----------Getters--------------*/

	/*This Function is used to register an option used by Data Access Layer.
	 */
	public  function register_options($option_name,$option_type,$args=null){
		if(!in_array($option_type,$this->VALID_OPTION_LIST))
			return;
		$this->registered_option[$option_type] = $option_name;
	}
	/**
	 * This function creates a database call to fetch value
	 * @param String $type The type of option to be used viz main/active/field...
	 * @param Array $args The parameters to be supplied to get_option method of DAO
	 * @param String $name The name of Post Type/Page type or whatever
	 * @return NULL of value returned.
	 */
	private function _get_option($type,$args=null,$name=null){
		$backtrace = debug_backtrace();
		xydac()->log("_get_option name: ".$this->registered_option[$type].'_'.$name.", called by :". $backtrace[1]['function']." type:".$type." args:",$args);

		if('field'!=$type && isset($this->registered_option[$type])){
			return $this->dao->get_options($this->registered_option[$type],$args);
		}
		else if('field'==$type && null!=$name && isset($this->registered_option[$type]))
			return $this->dao->get_options($this->registered_option[$type].'_'.$name,$args);
		else
			return null;
	}
	/*----------------------------End FInal Functions---------------------*/
	/*----------------------------Start Major Getters---------------------*/
	/* This function returns the array of active items */
	function get_active($args=null){
		if(!$this->uses_active || !isset($this->registered_option['active']))
			return $this->_get_option('main',$args);
		else{
			if(null==$args)
				return $this->_get_option('main',array('values'=>$this->_get_option('active'),'is_value_array'=>'true','match_keys'=>'false','final_val_array'=>'true'));
			else
				return $this->_get_option('active',$args);
		}
	}
	/* This function returns the array of active item's name */
	function get_active_names(){
		if(!$this->uses_active || !isset($this->registered_option['active']))
			return $this->get_main_names();
		else
			return $this->_get_option('active');//,array('fields'=>array('name'),'is_value_array'=>'true')
	}
	/* This function returns the array of main items */
	function get_main($args=null){
		if(!isset($this->registered_option['main']))
			return;
		return $this->_get_option('main',$args);
	}
	/* This function returns the array of main item's  name */
	function get_main_names($name=null){
		xydac()->log('get_main_names');
		if(!isset($this->registered_option['main']))
			return;
		if(!empty($name))
			return $this->_get_option('main',array('fields'=>array($name),'is_value_array'=>'true'));
		else
			return $this->_get_option('main',array('fields'=>array('name'),'is_value_array'=>'true','filter'=>'value'));
	}
	/* This function returns the array of field items
	 ^ $name: specifies the object name of which field is to be fetched.
	*/
	function get_field($name,$args=null){
		if(!$this->has_custom_fields || !isset($this->registered_option['field']))
			return;
		return $this->_get_option('field',$args,$name);
	}
	/* This function returns the array of field item's name
	 ^ $name: specifies the object name(post_type) of which field is to be fetched.
	*/

	function get_field_names($name,$fieldname_colname=null){
		if(!$this->has_custom_fields || !isset($this->registered_option['field']))
			return;
		if(!empty($fieldname_colname))
			return $this->_get_option('field',array('fields'=>array($fieldname_colname)),$name);
		else
			return $this->_get_option('field',array('fields'=>array('field_name')),$name);
	}
	/* This function returns the string of field's type
	 ^ $name: specifies the object name(post_type) of which field is to be fetched.
	*/
	function get_field_type($name,$field_name,$fieldname_colname=null,$fieldtype_colname=null){
		if(!$this->has_custom_fields || !isset($this->registered_option['field']))
			return;
		if(!empty($fieldname_colname)&&!empty($fieldtype_colname))
			return $this->_get_option('field',array('fields'=>array($fieldtype_colname),
					'is_value_array'=>'true',
					'values'=>array($fieldname_colname=>array($field_name)),
					'filter'=>array('value','value')
			),$name);
		else
			return $this->_get_option('field',array('fields'=>array('field_type'),
					'is_value_array'=>'true',
					'values'=>array('field_name'=>array($field_name)),
					'filter'=>array('value','value')
			),$name);
	}
	/*----------------------------End Major Getters---------------------*/
	function get_registered(){
		return $this->registered_option;
	}
	function xydac_checkbool($string)
	{
		if($string=='false')
			return false;
		else
			return true;
	}
	function xydac_singular($name)
	{
		return ((substr($name,-1)=='s') ? substr($name,0,-1) : $name);
	}

	/*----------------------------View Components---------------------*/
	/*Main View Function :  view_main()*/
	/* $tabs = array ('name'=>array('label','href','default'))*/
	function handle_menu()
	{
		//xydac()->$menu_slug
		add_submenu_page( 'xydac_ultimate_cms', $this->module_label, $this->module_label, 'manage_xydac_cms', 'xydac_ultimate_cms_'.$this->module_name, array($this,'view_main'));
	}
	function front_header($tabs = null){
		echo "<div class='wrap'>";
		echo '<div id="icon-options-general" class="icon32"><br></div>';
		if(!empty($tabs)){
			echo '<h2 style="border-bottom: 1px solid #CCC;padding-bottom:0px;">';
			$sub = isset($_GET['sub']) ? $_GET['sub'] : (isset($_GET['edit_xydac_'.$this->module_name])?$this->module_name : (isset($_GET['edit_'.$this->module_name.'_field'])?$this->module_name.'_fields':false));
			foreach($tabs as $tab_name=>$tab){
				?>
<a href="<?php echo $tab['href']; ?>"
	class="nav-tab <?php if(($sub && $sub===$tab['name']) || (!$sub && true ==$tab['default']))  echo 'nav-tab-active' ?>"><?php echo $tab['label']; ?>
</a>
<?php
			}
			echo '</h2> <br class="clear" />';
		}
	}
	function front_footer(){
		echo "</div>";
	}
	/*
	 * For creating view component create a function with function name 'screen_name'_func()
	*
	* $tab : 'href','label','default'
	*/
	public function view_main(){
		$sub = isset($_GET['sub']) ? $_GET['sub'] : (isset($_GET['edit_xydac_'.$this->module_name])?$this->module_name : (isset($_GET['edit_'.$this->module_name.'_field'])?$this->module_name.'_fields':false));
		$this->front_header($this->tabs);
		if($sub)
			foreach($this->tabs as $tab_name=>$tab){
			if($sub===$tab['name']){
				if(method_exists($this, 'view_'.$tab_name.'_func'))
					call_user_func(array($this, 'view_'.$tab_name.'_func'),$tab);
				else 
					do_action("xydac_cms_module_view_main",$tab["name"]);
				break;
			}
		}
		else
			foreach($this->tabs as $tab_name=>$tab)
			{
				if(method_exists($this, 'view_'.$tab_name.'_func'))
					call_user_func(array($this, 'view_'.$tab_name.'_func'),$tab);
				else 
					do_action("xydac_cms_module_view_main",$tab["name"]);
				break;
			}
			$this->front_footer();
	}
	//default tab page
	function view_module_func($tab)
	{
		$method = 'xydac_'.$this->module_name.'_manager';
		new $method();
	}
	//default field page
	function view_fields_func($tab)
	{
		if(!isset($_GET['manage_'.$this->module_name]))
		{
			$formaction = $tab['href'];
			$selectdata = $this->get_active_names();
			xydac()->log('view_fields_func',$selectdata);
			?>
<form name='manage_<?php echo $this->module_name ?>_fields'
	action='<?php echo $formaction ?>' method='get'>
	<h3>
		<?php echo __('Select the ',XYDAC_CMS_NAME).$this->module_label.__(' To manage ',XYDAC_CMS_NAME); ?>
	</h3>
	<select name='manage_<?php echo $this->module_name ?>'
		id='manage_<?php echo $this->module_name ?>' style="margin: 20px;">
		<?php foreach ($selectdata  as $name=>$label) {?>
		<option value="<?php echo $label; ?>">
			<?php echo $label; ?>
		</option>
		<?php } ?>
	</select> <input type="hidden" name="page"
		value="xydac_ultimate_cms_<?php echo $this->module_name ?>" /> <input
		type="hidden" name="sub"
		value="<?php echo $this->module_name ?>_fields" /> <input
		type="submit"
		id="manage_<?php echo $this->module_name ?>_fields_submit"
		class="button" value="Manage">
</form>
<?php }
else
{
	$method = 'xydac_'.$this->module_name.'_fields';
	new $method($_GET['manage_'.$this->module_name]);
}
	
	}
	//default sync page
	function view_xydac_sync_func($tab)
	{
		
		if(xydac()->apikey){
			$result = xydac()->xml_rpc_client('wp.getPosts',array('post_type'=>'xydac_'.$this->module_name,));
			//var_dump($result->getResponse());
			$resultsarr = $result->getResponse();
			if(is_array($resultsarr)){
				foreach($resultsarr as $resultarr){
					echo "Post Title : ".$resultarr['post_title'].'<br/>';
					$cont = $resultarr['custom_fields'][0]['id'];
					echo "Post Code : ".base64_decode($cont).'<br/>';
				}
			echo "<br/><br/>";
			}
			
		}
	}
}
?>