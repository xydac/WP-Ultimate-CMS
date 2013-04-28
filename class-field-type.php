<?php 
// Abstract class for field type 
abstract class field_type{
	public $abspath;
	public $ftype; //class name of extension
	public $flabel; //Label for field types
	public $ver; //version of field type
	public $name; //name of field type
	public $label; //label of field type
	public $desc; // description of field type
	public $value; // value of field type
	public $helptext; // helptext of field type
	public $basic;	//bool :true if field type is a basic type;

	public $compaitable; //array containing allowed type viz posttype,pagetype,archive,taxonomy
	public $minwpver; //Minimum version of WordPress Required by FirldType to function properly
	public $maxwpver; //Maximum version of WordPress Required by FirldType to function properly
	public $hasmultiple; //boolean, TRUE: allows multiple values to be stored.
	public $fieldoptions; //boolean, TRUE: allows multiple type of fields in single field.
	public $hasmultiplefields; //boolean true: allows the fieldtype to have multiple fields

	/* The primary Constructor of the class, Defines the basic stuffs,Always call this primary constructor in subclass
	 *
	*
	*/
	public function __construct($name,$attr=array())//$label='',$desc='',$val='',$hasmultiple=false,$help='')
	{
		global $post;
		$x_field=array();
		$x_multiple = null;
		if(isset($post) && isset($post->post_type) &&(!isset($attr['showall']) || isset($attr['showall']) && $attr['showall']!="true"))
		{
			$fields='';
			if('page'==$post->post_type)
			{
				$pagetype = xydac()->modules->page_type->get_page_type($post->ID);
				$fields	= xydac()->modules->page_type->get_field($pagetype);//get_page_type_fields
			}
			else{
				$fields=xydac()->modules->post_type->get_field($post->post_type);
			}
			if(is_array($fields))
				foreach($fields as $field)
				if($name==$field['field_name']){
				$x_field = $field;
				if(strpos($x_field['field_val'],',')){
					$x_field['field_val'] = preg_replace('/,/','&',$x_field['field_val']);
					parse_str($x_field['field_val'],$x_field['field_val']);
					break;
				}
				$x_multiple = (isset($field['field_has_multiple']) && $field['field_has_multiple']=='true') ?	true: false;
			}
		}
		if(is_array($attr))
			extract($attr);
			
		$this->abspath = WP_PLUGIN_DIR."/".XYDAC_CMS_NAME."/fieldTypes/";
		$this->name = $name;
		(isset($label))? $this->label= $label : $this->label= $name;
		isset($desc)? $this->desc = $desc: $this->desc = '';
		isset($helptext)? $this->helptext = $help: $this->helptext = '';
		isset($ver)? $this->ver = $ver : $this->ver = "1.0";
		isset($flabel)? $this->ftype = $ftype : $this->ftype = "new_field";
		isset($val)? $this->value=$val : (isset($x_field['field_val']) ? $this->value=$x_field['field_val'] : $this->value='');
		isset($basic)? $this->basic = $basic : $this->basic = false;
		(isset($hasmultiple) && $hasmultiple=='true') ?	$this->hasmultiple = true: $this->hasmultiple = false;
		isset($fieldoptions) ? $this->fieldoptions = $fieldoptions: $this->fieldoptions = array();
		isset($compaitable) ? $this->compaitable = $compaitable: $this->compaitable = array('posttype','pagetype');
		isset($hasmultiplefields) ? $this->hasmultiplefields = $hasmultiplefields: $this->hasmultiplefields = false;
		
		if(null!= $x_multiple)
			$this->hasmultiple = $x_multiple;
	}
	/* The function for defining the input data for field type.
	 * @post_id - Provide the Post id Under use, Use to fetch the post meta data.
	* return the string containing html which generates the form
	*/
	public function input($post_id)
	{
		global $post;
		$input = "";
		$val = get_post_meta($post_id, $this->name, false);
		if($this->hasmultiplefields)
		{
			//@TODO: handle multiple fields in next version
			//this  is what to do: store the comma seperated keynames in actual metadata


		}
		if(is_array($val) && count($val)>0)
		{
			foreach($val as $k=>$v)
			{
				$input.= $this->get_input($k,$v);
			}
			if($this->hasmultiple)
				$input.= $this->get_input();
		}
		else
			$input.= $this->get_input();

		if($this->hasmultiple && $post->post_type=='page')
			return $input."<a href='#' class='xydac_add_more_page' id='".$this->name."'>".__('ADD MORE',XYDAC_CMS_NAME)."</a>";
		else if($this->hasmultiple)
			return $input."<a href='#' class='xydac_add_more' id='".$this->name."'>".__('ADD MORE',XYDAC_CMS_NAME)."</a>";
		else
			return $input;
	}
	public function taxonomy_input($tag,$tax)
	{
		//var_dump($this->get_options());
		if(isset($tag->term_id))
			$datas = get_metadata('taxonomy', $tag->term_id, $this->name, TRUE);
		else
			$datas = false;
		if(!isset($_GET['action']))
			return '<div class="form-field">'.$this->get_input(0,$datas).'</div>';
		else
			return '<tr class="form-field">'.$this->get_input(0,$datas,true).'</tr>';
	}
	/* The function for handling the form data on save.
	 * @temp = The array used to store all update metadata values
	* @post_id - Provide the Post id Under use, Use to fetch/save the post meta data.
	* @val - the variable containing the post form data.
	* @oval - Old value of the meta object
	*/
	public function saving(&$temp,$post_id,$val,$oval='')
	{
		if(esc_attr(stripslashes($val))==esc_attr(stripslashes($oval)))
			return;
		if($this->hasmultiple)
		{
			$v= esc_attr(stripslashes($val));
			if(empty($oval) && !empty($v))
				array_push($temp,add_post_meta($post_id, $this->name, $v));
			else if(!empty($oval) && empty($v))
				array_push($temp,delete_post_meta($post_id, $this->name, $oval)); 
			else
				array_push($temp,update_post_meta($post_id, $this->name, esc_attr(stripslashes($val)),esc_attr(stripslashes($oval))));
				
		}
		else
			array_push($temp,update_post_meta($post_id, $this->name, esc_attr(stripslashes($val)),esc_attr(stripslashes($oval))));

	}

	public function output($vals,$atts)
	{
		$atts = wp_specialchars_decode(stripslashes_deep($atts),ENT_QUOTES);
	 extract(shortcode_atts(array(
	 		'pre' => '',
	 		'before_element'=>'',
	 		'after_element'=>'',
	 		'post' => '',
	 ), $atts));

		$s = "";
		foreach($vals as $val)
			$s.=wp_specialchars_decode($before_element).do_shortcode(wp_specialchars_decode(stripslashes_deep($val),ENT_QUOTES)).wp_specialchars_decode($after_element);
		return wp_specialchars_decode($pre).$s.wp_specialchars_decode($post);

	}
	public function taxonomy_output($vals,$atts)
	{
		//$atts = wp_specialchars_decode(stripslashes_deep($atts),ENT_QUOTES);
	 extract(shortcode_atts(array(
	 		$this->name.'_before'=>'',
	 		$this->name.'_after'=>'',
	 ), $atts));
	 	
		/*if(empty(${$this->name.'_before'}))
			${$this->name.'_before'} = $this->label." : ";*/
		if(!empty($vals))
			return wp_specialchars_decode(${
			$this->name.'_before'}).do_shortcode(wp_specialchars_decode(stripslashes_deep($vals),ENT_QUOTES)).wp_specialchars_decode(${
				$this->name.'_after'});
				else
					return "";
	}

	/* The function for generating the select options.
	 */
	public function option($sel)
	{
		if($sel== $this->ftype)
			$t = "<option value='".$this->ftype."' Selected>".$this->flabel."</option>";
		else
			$t = "<option value='".$this->ftype."'>".$this->flabel."</option>";
		return $t;
	}
	/* This function returns True if basic variable is set to true, and false on false.
	 * ON TRUE : All the basic field types fall into same tab
	* ON FALSE: All Non basic field types get their on tab.
	*/
	public function isBasic()
	{
		if($this->basic)
			return true;
		else
			return false;

	}
	/* Function that returns the script to be included in head section of admin panel
	 */
	public function adminscript()
	{
		return;
	}
	/* Function that returns the style to be included in head section of admin panel
	 */
	public function adminstyle()
	{
		return;
	}
	/* Function that returns the script to be included in head section of admin panel
	 */
	public function sitescript()
	{
		return;
	}

	/* Function that returns the style to be included in head section of admin panel
	 */
	public function sitestyle()
	{
		return;
	}
	public function get_ajax_output($subaction)
	{
		return;
	}
	public function get_options()
	{
		//var_dump($this->value);
		if(empty($this->value))
			return array();
		if(!is_array($this->value))
			$options_temp = explode(',',$this->value);
		else
			$options_temp = $this->value;
		$options = array();
		foreach($options_temp as $v)
			if(count(explode('=',$v))==2)
			{
				$v = explode('=',$v);
				$key = $v[0];
				$value = $v[1];
				$options[$key]=$value;
			}
			else
			{
				$options[$v]=$v;
			}
			return $options;
	}
	public function wp_admin_head(){
		return;
	}
	public function get_include_contents($filename) {
		if (is_file($filename)) {
			ob_start();
			include $filename;
			return ob_get_clean();
		}
		return false;
	}

}


function xydac_fieldtypes_init()
{
	//@todo: add a button on main page to do this as this creates a performance issue
	$xydac_active_field_types = xydac()->xydac_cms_build_active_field_types();
	//var_dump($xydac_active_field_types);die();
	global $xydac_cms_fields,$wp_version;
	//$xydac_active_field_types = get_option("xydac_active_field_types");
	$xydac_fields = array();
	$adminscript = "";
	$adminstyle = "";
	$sitescript = "";
	$sitestyle = "";
	$added = array();
	foreach(glob(WP_PLUGIN_DIR.'/'.XYDAC_CMS_NAME.'/fieldTypes/*.php') as $file)
	{
		include_once($file);
		$filename = explode("-",basename($file,'.php'));
		$temp = new $filename[1]('t1');
		if((isset($temp->minwpver) && !empty($temp->minwpver)) || (isset($temp->maxwpver) && !empty($temp->maxwpver)))
			if(floatval($wp_version)<$temp->minwpver || floatval($wp_version)>$temp->maxwpver)
			continue;
		
		if(is_array($xydac_active_field_types))
			if(in_array($temp->ftype,$xydac_active_field_types) && !in_array($temp->ftype,$added))
			{
				array_push($added,$temp->ftype);
				$adminscript.= "\n/*============START $temp->ftype=============================*/\n".$temp->adminscript()."\n/*============END $temp->ftype=============================*/\n";
				$adminstyle.= "\n/*============START $temp->ftype=============================*/\n".$temp->adminstyle()."\n/*============END $temp->ftype=============================*/\n";
				$sitescript.= "\n/*============START $temp->ftype=============================*/\n".$temp->sitescript()."\n/*============END $temp->ftype=============================*/\n";
				$sitestyle.= "\n/*============START $temp->ftype=============================*/\n".$temp->sitestyle()."\n/*============END $temp->ftype=============================*/\n";

				add_action('admin_head', array($temp,"wp_admin_head"));
			}
			if(is_array($temp->compaitable) && in_array('posttype',$temp->compaitable))
				$xydac_fields['fieldtypes']['posttype'][$temp->ftype] = $temp->flabel;
			if(is_array($temp->compaitable) && in_array('pagetype',$temp->compaitable))
				$xydac_fields['fieldtypes']['pagetype'][$temp->ftype] = $temp->flabel;
			if(is_array($temp->compaitable) && in_array('taxonomy',$temp->compaitable))
				$xydac_fields['fieldtypes']['taxonomy'][$temp->ftype] = $temp->flabel;
	}
	
	$xydac_fields['adminscript'] = $adminscript;
	$xydac_fields['adminstyle'] = $adminstyle;
	$xydac_fields['sitescript'] = $sitescript;
	$xydac_fields['sitestyle'] = $sitestyle;
	$xydac_cms_fields = $xydac_fields;
}
?>