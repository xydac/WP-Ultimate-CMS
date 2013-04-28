<?php

class xydac_taxonomy_type_manager extends xydac_ultimate_cms_core{


	function __construct()
	{
		$post_types = get_post_types(array('public'   => true),'objects');
		$_postarr = array();
		foreach($post_types as $posttype)
			$_postarr[$posttype->name] = $posttype->label;
		$form_variables = array(
				'heading-1' => array('arr_label' => __('Post Types',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_label', 'type'=>'heading', 'initialclose'=>false),
				'post_types' => array( 'arr_label' => __('Select the Post Types to associate the Taxonomy to',XYDAC_CMS_NAME) , 'name' => '[object_type]', 'type'=>'checkbox', 'desc'=>__('Select all those Post Types where you want to use your Taxonomy.',XYDAC_CMS_NAME), 'default'=>'', 'values'=>$_postarr),
				'heading-1-2' => array('arr_label' => __('Labels',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_options', 'type'=>'heading', 'initialclose'=>true),
				'label' => array( 'arr_label' => __('Label of Taxonomy ',XYDAC_CMS_NAME) , 'name' => '[args][label]', 'type'=>'string', 'desc'=>__('A plural descriptive name for the taxonomy marked for translation.',XYDAC_CMS_NAME), 'default'=>''),
				'name' => array( 'arr_label' => __('Plural Name ',XYDAC_CMS_NAME), 'name' => '[args][labels][name]' ,  'type' => 'string'  , 'desc' => __('general name for the taxonomy, usually plural.',XYDAC_CMS_NAME) ),
				'singular_name' => array( 'arr_label' => __('Singular Name ',XYDAC_CMS_NAME), 'name' => '[args][labels][singular_name]' ,  'type' => 'string'  , 'desc' => __('name for one object of this taxonomy.',XYDAC_CMS_NAME) ),
				'search_items' => array( 'arr_label' => __('Search Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][search_items]' ,  'type' => 'string'  , 'desc' => __('the search items text.',XYDAC_CMS_NAME) ),
				'popular_items' => array( 'arr_label' => __('Popular Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][popular_items]' , 'type' => 'string'  , 'desc' => __('the popular items text.',XYDAC_CMS_NAME) ),
				'all_items' => array( 'arr_label' => __('All Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][all_items]' ,  'type' => 'string'  , 'desc' => __('the all items text.',XYDAC_CMS_NAME) ),
				'parent_item' => array( 'arr_label' => __('Parent Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][parent_item]' ,  'type' => 'string'  , 'desc' => __('the parent item text. This string is not used on non-hierarchical taxonomies such as post tags.',XYDAC_CMS_NAME) ),
				'parent_item_colon' => array( 'arr_label' => __('Parent Item Label with colon ',XYDAC_CMS_NAME), 'name' => '[args][labels][parent_item_colon]',  'type' => 'string'  , 'desc' => __('The same as parent_item, but with colon : in the end.',XYDAC_CMS_NAME) ),
				'edit_item' => array( 'arr_label' => __('Edit Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][edit_item]' ,  'type' => 'string'  , 'desc' => __('the edit item text.',XYDAC_CMS_NAME) ),
				'update_item' => array( 'arr_label' => __('Update Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][update_item]' ,  'type' => 'string'  , 'desc' => __('the update item text.',XYDAC_CMS_NAME) ),
				'add_new_item' => array( 'arr_label' => __('Add New Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][add_new_item]' ,  'type' => 'string'  , 'desc' => __('the add new item text.',XYDAC_CMS_NAME) ),
				'new_item_name' => array( 'arr_label' => __('New Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][new_item_name]' ,  'type' => 'string'  , 'desc' => __('the new item name text.',XYDAC_CMS_NAME) ),
				'separate_items_with_commas' => array( 'arr_label' => __('Seperate Item With Commas Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][separate_items_with_commas]',  'type' => 'string'  , 'desc' => __('the separate item with commas text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies.',XYDAC_CMS_NAME) ),
				'add_or_remove_items' => array( 'arr_label' => __('Add or Remove Items Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][add_or_remove_items]',  'type' => 'string'  , 'desc' => __('the add or remove items text and used in the meta box when JavaScript is disabled. This string isn\'t used on hierarchical taxonomies.',XYDAC_CMS_NAME) ),
				'choose_from_most_used' => array( 'arr_label' => __('Choose From Most Used Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][choose_from_most_used]',  'type' => 'string'  , 'desc' => __('the choose from most used text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies.',XYDAC_CMS_NAME) ),
				'view_item' => array( 'arr_label' => __('View Item Label ',XYDAC_CMS_NAME), 'name' => '[args][labels][view_item]',  'type' => 'string'  , 'desc' => __('The View Item Label used in Admin Panel,Admin Menu.',XYDAC_CMS_NAME) ),
				'heading-2' => array('arr_label' => __('Options',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_options', 'type'=>'heading', 'initialclose'=>true),
				'showascombobox' => array( 'arr_label' => __('Show as ComboBox ',XYDAC_CMS_NAME) , 'name' => '[showascombobox]', 'type'=>'boolean', 'desc'=>__('Show the Terms in Combo-box on the Add/Edit Post/Custom Post Page',XYDAC_CMS_NAME), 'default'=>'false'),
				'public' => array( 'arr_label' => __('Public ',XYDAC_CMS_NAME) , 'name' => '[args][public]', 'type'=>'boolean', 'desc'=>__('Should this taxonomy be exposed in the admin UI.',XYDAC_CMS_NAME), 'default'=>'true'),
				'show_in_nav_menus' => array( 'arr_label' => __('Show in Navigation Menu ',XYDAC_CMS_NAME) , 'name' => '[args][show_in_nav_menus]', 'type'=>'boolean', 'desc'=>__('Selecting TRUE makes this Taxonomy available for selection in navigation menus.',XYDAC_CMS_NAME), 'default'=>'true'),
				'show_ui' => array( 'arr_label' => __('Show UI ',XYDAC_CMS_NAME) , 'name' => '[args][show_ui]', 'type'=>'boolean', 'desc'=>__('Whether to generate a default User Interface for managing this taxonomy.',XYDAC_CMS_NAME), 'default'=>'true'),
				'show_tagcloud' => array( 'arr_label' => __('Show Tag Cloud ',XYDAC_CMS_NAME) , 'name' => '[args][show_tagcloud]', 'type'=>'boolean', 'desc'=>__('Whether to show a tag cloud in the admin UI for this Taxonomy Name',XYDAC_CMS_NAME), 'default'=>'true'),
				'hierarchical' => array( 'arr_label' => __('Hierarchical ',XYDAC_CMS_NAME) , 'name' => '[args][hierarchical]', 'type'=>'boolean', 'desc'=>__('Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.',XYDAC_CMS_NAME), 'default'=>'false'),
				'heading-3' => array('arr_label' => __('Rewrite Options',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_options', 'type'=>'heading', 'initialclose'=>true),
				'rewrite' => array( 'arr_label' => __('Rewrite ',XYDAC_CMS_NAME), 'name' => '[args][rewrite][val]', 'type'=>'boolean', 'desc'=>__('Set to false to prevent rewrite, or array to customize customize query var. Default will use Taxonomy Name as query var',XYDAC_CMS_NAME), 'default'=>'true'),
				'rewrite_slug' => array( 'arr_label' => __('Slug ',XYDAC_CMS_NAME), 'name' => '[args][rewrite][slug]', 'type'=>'string', 'desc'=>__(' prepend posts with this slug',XYDAC_CMS_NAME), 'default'=>'true'),
				'rewrite_with_front' => array( 'arr_label' => __('With-Front ',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][with_front]', 'type'=>'boolean', 'desc'=>__('allowing permalinks to be prepended with front base',XYDAC_CMS_NAME), 'default'=>'true'),
				'rewrite_hierarchical' => array( 'arr_label' => __('Hierarchical',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][hierarchical]', 'type'=>'boolean', 'desc'=>__('Allows permalinks to be rewritten hierarchically(Works with WP-3.1)',XYDAC_CMS_NAME), 'default'=>'false'),
				'query_var' => array( 'arr_label' => __('Query var ',XYDAC_CMS_NAME) , 'name' => '[args][query_var]', 'type'=>'string', 'desc'=>__('False to prevent queries, or string to customize query var. Default will use Taxonomy Name as query var',XYDAC_CMS_NAME), 'default'=>''),
				'update_count_callback' => array( 'arr_label' => __('Update Count Callback function Name ',XYDAC_CMS_NAME) , 'name' => '[args][update_count_callback]', 'type'=>'string', 'desc'=>__('<strong>For Advanced Users only</strong> A function name that will be called to update the count of an associated $object_type, such as post, is updated.',XYDAC_CMS_NAME)),
				'heading-4' => array('arr_label' => __('Permission and Capabilities',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_options', 'type'=>'heading', 'initialclose'=>true),
				'manage_terms' => array( 'arr_label' => __('Manage Terms ',XYDAC_CMS_NAME) , 'name' => '[args][capabilities][manage_terms]', 'type'=>'array', 'desc'=>__('Assign the permissions. who can manage the Taxonomy Terms',XYDAC_CMS_NAME), 'default' => 'manage_categories', 'values'=>array('manage_options' => 'Administrator', 'manage_categories' => 'Editor', 'publish_posts' => 'Author', 'edit_posts' => 'Contributor', 'read' => 'Subscriber')),
				'edit_terms' => array( 'arr_label' => __('Edit Terms ',XYDAC_CMS_NAME) , 'name' => '[args][capabilities][edit_terms]', 'type'=>'array', 'desc'=>__('Assign the permissions. who can edit the Taxonomy Terms',XYDAC_CMS_NAME), 'default' => 'manage_categories', 'values'=>array('manage_options' => 'Administrator', 'manage_categories' => 'Editor', 'publish_posts' => 'Author', 'edit_posts' => 'Contributor', 'read' => 'Subscriber')),
				'delete_terms' => array( 'arr_label' => __('Delete Terms ',XYDAC_CMS_NAME) , 'name' => '[args][capabilities][delete_terms]', 'type'=>'array', 'desc'=>__('Assign the permissions. who can delete the Taxonomy Terms',XYDAC_CMS_NAME), 'default' => 'manage_categories', 'values'=>array('manage_options' => 'Administrator', 'manage_categories' => 'Editor', 'publish_posts' => 'Author', 'edit_posts' => 'Contributor', 'read' => 'Subscriber')),
				'assign_terms' => array( 'arr_label' => __('Assign Terms ',XYDAC_CMS_NAME) , 'name' => '[args][capabilities][assign_terms]', 'type'=>'array', 'desc'=>__('Assign the permissions. who can assign the Taxonomy Terms',XYDAC_CMS_NAME), 'default' => 'edit_posts', 'values'=>array('manage_options' => 'Administrator', 'manage_categories' => 'Editor', 'publish_posts' => 'Author', 'edit_posts' => 'Contributor', 'read' => 'Subscriber')),
				'heading-42' => array('arr_label' => __('Content Details',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_con_details', 'type'=>'heading', 'initialclose'=>true),
				'content_html' => array( 'arr_label' => __('Content HTML',XYDAC_CMS_NAME) , 'name' => '[content_html]', 'type'=>'textarea', 'desc'=> __('Please Enter the default template for the content.You can use shortcodes.',XYDAC_CMS_NAME), 'default'=>''),
				'content_css' => array( 'arr_label' => __('Content CSS',XYDAC_CMS_NAME) , 'name' => '[content_css]', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom CSS Styles for this taxonomy ',XYDAC_CMS_NAME), 'default'=>''),
				'content_js' => array( 'arr_label' => __('Content Javascript',XYDAC_CMS_NAME) , 'name' => '[content_js]', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom Java script for this taxonomy ',XYDAC_CMS_NAME), 'default'=>''),
				'heading-5' => array('name'=>'finalheading','type'=>'heading','initialclose'=>true, 'finalclose'=>true)
		);
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		add_action('xydac_core_insert_update',array($this,'xydac_core_insert_update'));
		add_action('xydac_core_delete',array($this,'xydac_core_delete'));
		add_action('xydac_core_bulkaction',array($this,'xydac_core_bulkaction'));
		//add_filter('xydac_core_rowactions',array($this,'xydac_core_rowactions'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		add_filter('xydac_core_insert',array($this,'xydac_core_insert'),10,1);
		//parent::__construct(xydac()->modules->taxonomy_type->get_module_name(),xydac()->modules->taxonomy_type->get_module_label(),xydac()->modules->taxonomy_type->get_base_path(),xydac()->modules->taxonomy_type->get_registered_option('main'),$form_variables,true,false);
		$args = array('enableactivation'=>false,'xydac_core_show_additional' => true,'custom_css_id'=>'content_css','custom_jss_id'=>'content_js');
		parent::__construct(xydac()->modules->taxonomy_type,'main',$form_variables,$args);
		//parent::__construct("xydac_taxonomy",__("Custom Taxonomy Type",XYDAC_CMS_NAME),XYDAC_CMS_TAXONOMY_TYPE_PATH,XYDAC_CMS_TAXONOMY_TYPE_OPTION,$form_variables,true,false);
	}
	function xydac_core_leftdiv()
	{
		return "id=accordion";
	}
	function xydac_core_insert_update()
	{
		if(function_exists('flush_rewrite_rules'))
			flush_rewrite_rules();
	}
	function xydac_core_delete($name)
	{
		//delete fields option
		//check active option
		delete_option(XYDAC_CMS_TAXONOMY_TYPE_OPTION."_".$name);

	}
	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[args][label]'=>__("Label",XYDAC_CMS_NAME),'[args][hierarchical]'=>__("Hierarchical",XYDAC_CMS_NAME),'[object_type]'=>__("Post Types",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function xydac_core_rowactions()
	{
		$action = array('Export'=>XYDAC_CMS_EXPORT_PATH."?taxonomy_name=");
		return $action;
	}
	function xydac_core_doactions()
	{
		$action = array('activate'=>__("Activate",XYDAC_CMS_NAME),'deactivate'=>__("Deactivate",XYDAC_CMS_NAME),'delete'=>__("Delete",XYDAC_CMS_NAME));
		return $action;
	}
	function xydac_core_bulkaction($taxonomy)
	{
		switch($_POST['action'])
		{
			case "export" :{
				$cpt ="";
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
					$cpt.=$v.",";
				$cpt = substr($cpt,0,-1);
				$l = get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php?taxonomy_name=".$cpt;
				echo "<div id='message' class='updated below-h2'><p><a href=$l>".__('Click Here to download the Export File',XYDAC_CMS_NAME)."</a></p></div>";
			}
		}
	}
	function xydac_core_insert($datas)
	{
		foreach($datas as $k=>$data){
			$datas[$k]['args']['labels']['name'] = xydac_mods_inflect::pluralize($datas[$k]['name']);
			$datas[$k]['args']['label'] = (isset($datas[$k]['args']['labels']['name']) && !empty($datas[$k]['args']['labels']['name']))? $datas[$k]['args']['labels']['name']: xydac_mods_inflect::pluralize($datas[$k]['name']);
			$datas[$k]['args']['labels']['singular_name'] = ( isset($datas[$k]['args']['labels']["singular_label"]) && !empty($datas[$k]['args']['labels']["singular_label"])) ? $datas[$k]['args']['labels']["singular_label"] :  xydac_mods_inflect::singularize($datas[$k]['args']['labels']['name']);
			$datas[$k]['args']['labels']['search_items'] = ( isset($datas[$k]['args']['labels']["search_items"]) && !empty($datas[$k]['args']['labels']["search_items"])) ? $datas[$k]['args']['labels']["search_items"] : 'Search ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['popular_items'] = ( isset($datas[$k]['args']['labels']["popular_items"]) && !empty($datas[$k]['args']['labels']["popular_items"]) ) ? $datas[$k]['args']['labels']["popular_items"] : 'Popular ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['all_items'] = ( isset($datas[$k]['args']['labels']["all_items"]) && !empty($datas[$k]['args']['labels']["all_items"]) ) ? $datas[$k]['args']['labels']["all_items"] : 'All ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['parent_item'] = ( isset($datas[$k]['args']['labels']["parent_item"]) && !empty($datas[$k]['args']['labels']["parent_item"]) ) ? $datas[$k]['args']['labels']["parent_item"] : 'Parent ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['parent_item_colon'] = ( isset($datas[$k]['args']['labels']["parent_item_colon"]) && !empty($datas[$k]['args']['labels']["parent_item_colon"]) ) ? $datas[$k]['args']['labels']["parent_item_colon"] : 'Parent '.$datas[$k]['args']['label'].':';
			$datas[$k]['args']['labels']['edit_item'] = ( isset($datas[$k]['args']['labels']["edit_item"]) && !empty($datas[$k]['args']['labels']["edit_item"]) ) ? $datas[$k]['args']['labels']["edit_item"] : 'Edit ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['update_item'] = ( isset($datas[$k]['args']['labels']["update_item"]) && !empty($datas[$k]['args']['labels']["update_item"]) ) ? $datas[$k]['args']['labels']["update_item"] : 'Update ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['add_new_item'] = ( isset($datas[$k]['args']['labels']["add_new_item"]) && !empty($datas[$k]['args']['labels']["add_new_item"]) ) ? $datas[$k]['args']['labels']["add_new_item"] : 'Add New ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['new_item_name'] = ( isset($datas[$k]['args']['labels']["new_item_name"]) && !empty($datas[$k]['args']['labels']["new_item_name"]) ) ? $datas[$k]['args']['labels']["new_item_name"] : 'New ' .$datas[$k]['args']['label']. ' Name';
			$datas[$k]['args']['labels']['separate_items_with_commas'] = ( isset($datas[$k]['args']['labels']["separate_items_with_commas"]) && !empty($datas[$k]['args']['labels']["separate_items_with_commas"]) ) ? $datas[$k]['args']['labels']["separate_items_with_commas"] : 'Separate ' .$datas[$k]['args']['label']. ' with commas';
			$datas[$k]['args']['labels']['add_or_remove_items'] = ( isset($datas[$k]['args']['labels']["add_or_remove_items"]) && !empty($datas[$k]['args']['labels']["add_or_remove_items"]) ) ? $datas[$k]['args']['labels']["add_or_remove_items"] : 'Add or remove ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['choose_from_most_used'] = ( isset($datas[$k]['args']['labels']["choose_from_most_used"]) && !empty($datas[$k]['args']['labels']["choose_from_most_used"]) ) ? $datas[$k]['args']['labels']["choose_from_most_used"] : 'Choose from the most used ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['view_item'] = ( isset($datas[$k]['args']['labels']["view_item"]) && !empty($datas[$k]['args']['labels']["view_item"]) ) ? $datas[$k]['args']['labels']["view_item"] : 'View ' .$datas[$k]['args']['label'];
			$datas[$k]['args']['public']= "true";
			$datas[$k]['showascombobox']=  "false";
			$datas[$k]['args']['show_in_nav_menus']="true";
			$datas[$k]['args']['rewrite']=array('val'=>true,'slug'=>$datas[$k]['name'],'with_front'=>true,'hierarchical'=>false);
			$datas[$k]['args']['show_ui']= "true";
			$datas[$k]['args']['show_tagcloud']= "true";
			$datas[$k]['args']['hierarchical']= "false";
		}
		return $datas;
	}
}

?>