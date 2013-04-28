<?php

class xydac_post_type_manager extends xydac_ultimate_cms_core{


	function __construct()
	{
		$avl_types ='';
		$form_variables = array(
	   'heading-1' => array('arr_label' => __('Labels',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_label', 'type'=>'heading', 'initialclose'=>false),
				'label' =>  array( 'arr_label' => __('Label for Post Type ',XYDAC_CMS_NAME) , 'name' => '[args][label]', 'type'=>'string', 'desc'=> __('A plural descriptive name for the post type marked for translation.',XYDAC_CMS_NAME) , 'default'=>' '),
				'name' => array( 'arr_label' => __('Name of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][name]', 'type'=>'string', 'desc'=> __('general name for the post type, usually plural. The same as, and overridden by Label ',XYDAC_CMS_NAME) , 'default'=>' '),
				'singular_name' => array( 'arr_label' => __('Singular Name of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][singular_name]', 'type'=>'string', 'desc'=> __('name for one object of this post type. Defaults to value of name ',XYDAC_CMS_NAME) , 'default'=>' '),
				'add_new' => array( 'arr_label' => __('Add New Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][add_new]', 'type'=>'string', 'desc'=> __('the add new text. The default is Add New for both hierarchical and non-hierarchical types.',XYDAC_CMS_NAME) , 'default'=>' '),
				'add_new_item' => array( 'arr_label' => __('Add New Item Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][add_new_item]', 'type'=>'string', 'desc'=> __('the add new item text. Default is Add New Post/Add New Page',XYDAC_CMS_NAME) , 'default'=>' '),
				'edit_item' => array( 'arr_label' => __('Edit Item Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][edit_item]', 'type'=>'string', 'desc'=> __('the edit item text. Default is Edit Post/Edit Page',XYDAC_CMS_NAME) , 'default'=>' '),
				'new_item' => array( 'arr_label' => __('New Item Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][new_item]', 'type'=>'string', 'desc'=> __('the new item text. Default is New Post/New Page',XYDAC_CMS_NAME) , 'default'=>' '),
				'view_item' => array( 'arr_label' => __('View Item Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][view_item]', 'type'=>'string', 'desc'=> __('the view item text. Default is View Post/View Page',XYDAC_CMS_NAME) , 'default'=>' '),
				'search_items' => array( 'arr_label' => __('Search Item Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][search_item]', 'type'=>'string', 'desc'=> __('the search items text. Default is Search Posts/Search Pages',XYDAC_CMS_NAME) , 'default'=>' '),
				'not_found' => array( 'arr_label' => __('Not Found Item Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][not_found]', 'type'=>'string', 'desc'=> __('the not found text. Default is No posts found/No pages found',XYDAC_CMS_NAME) , 'default'=>' '),
				'not_found_in_trash' => array( 'arr_label' => __('Not Found in Thrash Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][not_found_in_trash]', 'type'=>'string', 'desc'=> __('the not found in trash text. Default is No posts found in Trash/No pages found in Trash',XYDAC_CMS_NAME) , 'default'=>' '),
				'parent_item_colon' => array( 'arr_label' => __('Parent Item with Colon Label of Post Type',XYDAC_CMS_NAME) , 'name' => '[args][labels][parent_item_colon]', 'type'=>'string', 'desc'=> __('the parent text. This string isn\'t used on non-hierarchical types. In hierarchical ones the default is Parent Page:',XYDAC_CMS_NAME) , 'default'=>' '),
				'menu_name' => array( 'arr_label' => __('Menu Name',XYDAC_CMS_NAME) , 'name' => '[args][labels][menu_name]', 'type'=>'string', 'desc'=> __('The menu name text. This string is the name to give menu items.',XYDAC_CMS_NAME) , 'default'=>' '),
				'enter_text_here' => array( 'arr_label' => __('Enter Text Here Label',XYDAC_CMS_NAME) , 'name' => '[args][labels][enter_text_here]', 'type'=>'string', 'desc'=> __('The Enter Text Here title label for post type.',XYDAC_CMS_NAME) , 'default'=>' '),
				'heading-2' => array('arr_label' => __('Options',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_options', 'type'=>'heading', 'initialclose'=>true),
				'public' => array( 'arr_label' => __('Public',XYDAC_CMS_NAME) , 'name' => '[args][public]', 'type'=>'boolean', 'desc'=> __('This field is used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search, But since the values for these fields is already present so I think this won\'t be used, Better leave this as it is.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'publicly_queryable' => array( 'arr_label' => __('Publicly Queryable',XYDAC_CMS_NAME) , 'name' => '[args][publicly_queryable]', 'type'=>'boolean', 'desc'=> __('Allows advanced query based operations from themes based on variable post_type.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'exclude_from_search' => array( 'arr_label' => __('Exclude from search ',XYDAC_CMS_NAME) , 'name' => '[args][exclude_from_search]', 'type'=>'boolean', 'desc'=> __('Whether to exclude posts with this post type from search results.',XYDAC_CMS_NAME) , 'default'=>'false'),
				'show_ui' => array( 'arr_label' => __('Show UI',XYDAC_CMS_NAME) , 'name' => '[args][show_ui]', 'type'=>'boolean', 'desc'=> __('Whether use the default User Interface for the this Post Type.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'query_var' => array( 'arr_label' => __('Query Var',XYDAC_CMS_NAME) , 'name' => '[args][query_var]', 'type'=>'boolean', 'desc'=> __('Use False to disable Querying, True sets the query_var to the name of Post Type.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'can_export' => array( 'arr_label' => __('Can Export',XYDAC_CMS_NAME) , 'name' => '[args][can_export]', 'type'=>'boolean', 'desc'=> __('Allow Exporting of Post Types\'s Content from EXPORT option of WordPress.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'show_in_nav_menus' => array( 'arr_label' => __('Show in Navigation Menu',XYDAC_CMS_NAME) , 'name' => '[args][show_in_nav_menus]', 'type'=>'boolean', 'desc'=> __('Whether post_type is available for selection in navigation menus.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'show_in_menu' => array( 'arr_label' => __('Show in Menu',XYDAC_CMS_NAME) , 'name' => '[args][show_in_menu]', 'type'=>'boolean', 'desc'=> __('Whether to show the post type in the admin menu and where to show that menu. Note that show_ui must be true. ',XYDAC_CMS_NAME) , 'default'=>'true'),
				'has_archive' => array( 'arr_label' => __('Has Archive',XYDAC_CMS_NAME) , 'name' => '[args][has_archive]', 'type'=>'boolean', 'desc'=> __('Enables post type archives. Will generate the proper rewrite rules if rewrite is enabled. ',XYDAC_CMS_NAME) , 'default'=>'true'),
				'map_meta_cap' => array( 'arr_label' => __('Map Meta Capabilities',XYDAC_CMS_NAME) , 'name' => '[args][map_meta_cap]', 'type'=>'boolean', 'desc'=> __('Whether to use the internal default meta capability handling.This is used for default handling of Capabilities and should be set to TRUE for proper use ',XYDAC_CMS_NAME) , 'default'=>'true'),
				'hierarchical' => array( 'arr_label' => __('Hierarchical ',XYDAC_CMS_NAME) , 'name' => '[args][hierarchical]', 'type'=>'boolean', 'desc'=> __('Whether the post type is hierarchical. Allows Parent to be specified.',XYDAC_CMS_NAME) , 'default'=>'false'),
				'menu_position' => array( 'arr_label' => __('Menu Position',XYDAC_CMS_NAME) , 'name' => '[args][menu_position]', 'type'=>'array', 'desc'=> __('The position in the menu order the post type should appear.',XYDAC_CMS_NAME) , 'default'=>'5', 'values'=>array('null'=>'Below Comments','5'=>'Below Post','10'=>'Below Media','20'=>'Below Pages','60'=>'Below First Seperator','100'=>'Below Second Seperator')),
				'heading-3' => array('arr_label' => __('Features',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_features', 'type'=>'heading', 'initialclose'=>true),
				'title' => array( 'arr_label' => __('Support for Title',XYDAC_CMS_NAME) , 'name' => '[args][supports][title]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'true'),
				'editor' => array( 'arr_label' => __('Support for  Editor',XYDAC_CMS_NAME) , 'name' => '[args][supports][editor]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'true'),
				'author' => array( 'arr_label' => __('Support for Author',XYDAC_CMS_NAME) , 'name' => '[args][supports][author]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'thumbnail' => array( 'arr_label' => __('Support for Thumbnail',XYDAC_CMS_NAME) , 'name' => '[args][supports][thumbnail]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'excerpt' => array( 'arr_label' => __('Support for  Excerpt',XYDAC_CMS_NAME) , 'name' => '[args][supports][excerpt]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'trackbacks' => array( 'arr_label' => __('Support for Trackbacks',XYDAC_CMS_NAME) , 'name' => '[args][supports][trackbacks]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'custom-fields' => array( 'arr_label' => __('Support for Custom Fields',XYDAC_CMS_NAME) , 'name' => '[args][supports][custom-fields]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'comments' => array( 'arr_label' => __('Support for Comments',XYDAC_CMS_NAME) , 'name' => '[args][supports][comments]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'revisions' => array( 'arr_label' => __('Support for Revisions',XYDAC_CMS_NAME) , 'name' => '[args][supports][revisions]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'page-attributes' => array( 'arr_label' => __('Support for Page attributes',XYDAC_CMS_NAME) , 'name' => '[args][supports][page-attributes]', 'type'=>'boolean', 'desc'=> __(' ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'def-cats' => array( 'arr_label' => __('Show Default Category',XYDAC_CMS_NAME) , 'name' => '[def][cat]', 'type'=>'boolean', 'desc'=> __('Show the Default Categories for this Custom Post Type ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'def-tag' => array( 'arr_label' => __('Show Default Tags',XYDAC_CMS_NAME) , 'name' => '[def][tag]', 'type'=>'boolean', 'desc'=> __('Show the Default Tags for this Custom Post Type ',XYDAC_CMS_NAME) , 'default'=>'false'),
				'heading-4' => array('arr_label' => __('Advanced Options',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_ad_options', 'type'=>'heading', 'initialclose'=>true),
				'description' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[args][description]', 'type'=>'string', 'desc'=> __('A short descriptive summary of what the post type is.',XYDAC_CMS_NAME) , 'default'=>' '),
				'capability_type' => array( 'arr_label' => __('Capability Type',XYDAC_CMS_NAME) , 'name' => '[args][capability_type]', 'type'=>'string', 'desc'=> __('The post type to use for checking read, edit, and delete capabilities.The Capabilities will be automatically created.',XYDAC_CMS_NAME) , 'default'=>''),
				'register_meta_box_cb' => array( 'arr_label' => __('Register Meta Box CB',XYDAC_CMS_NAME) , 'name' => '[args][register_meta_box_cb]', 'type'=>'string', 'desc'=> __('Provide a callback function that will be called when setting up the meta boxes for the edit form. Do remove_meta_box() and add_meta_box() calls in the callback.',XYDAC_CMS_NAME) , 'default'=>' '),
				'menu_icon' => array( 'arr_label' => __('Menu Icon',XYDAC_CMS_NAME) , 'name' => '[args][menu_icon]', 'type'=>'string', 'desc'=> __('The url to the icon to be used for this menu.',XYDAC_CMS_NAME) , 'default'=>' '),
				'heading-41' => array('arr_label' => __('Rewrite Options',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_rw_options', 'type'=>'heading', 'initialclose'=>true),
				'rewrite' => array( 'arr_label' => __('Rewrite',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][val]', 'type'=>'boolean', 'desc'=> __('Do you Want the Permalinks ceated for this post-type to be Rewritten.',XYDAC_CMS_NAME) , 'default'=>'true'),
				'slug' => array( 'arr_label' => __('Slug',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][slug]', 'type'=>'string', 'desc'=> __('Prepend posts with this slug. Uses Post-Type name if left blank.',XYDAC_CMS_NAME) , 'default'=>' '),
				'permalink_epmask' => array( 'arr_label' => __('Permalink_EPMASK',XYDAC_CMS_NAME) , 'name' => '[args][permalink_epmask]', 'type'=>'string', 'desc'=> __('The default rewrite endpoint bitmasks.',XYDAC_CMS_NAME) , 'default'=>' '),
				'with_front' => array( 'arr_label' => __('With Front',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][with_front]', 'type'=>'boolean', 'desc'=> __('allowing permalinks to be prepended with front base',XYDAC_CMS_NAME) , 'default'=>'true'),
				'feeds' => array( 'arr_label' => __('Feeds',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][feeds]', 'type'=>'boolean', 'desc'=> __('',XYDAC_CMS_NAME) , 'default'=>'false'),
				'pages' => array( 'arr_label' => __('Pages',XYDAC_CMS_NAME) , 'name' => '[args][rewrite][pages]', 'type'=>'boolean', 'desc'=> __('',XYDAC_CMS_NAME) , 'default'=>'true'),
				'heading-42' => array('arr_label' => __('Content Details',XYDAC_CMS_NAME) , 'name' => 'xydac_acc_con_details', 'type'=>'heading', 'initialclose'=>true),
				'content_html' => array( 'arr_label' => __('Content HTML',XYDAC_CMS_NAME) , 'name' => '[content_html]', 'type'=>'textarea', 'desc'=> __('Please Enter the default template for the content.Use the litrel [CONTENT] wherever you want to show the default content.Else use the Shortcodes for display of other fields.<br/><b>Availaible Field Types :</b> <br/>'.$avl_types.' ',XYDAC_CMS_NAME), 'default'=>''),
				'content_css' => array( 'arr_label' => __('Content CSS',XYDAC_CMS_NAME) , 'name' => '[content_css]', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom CSS Styles for this post type ',XYDAC_CMS_NAME), 'default'=>''),
				'content_js' => array( 'arr_label' => __('Content Javascript',XYDAC_CMS_NAME) , 'name' => '[content_js]', 'type'=>'textarea', 'desc'=> __('Please Enter the Custom Java script for this post type ',XYDAC_CMS_NAME), 'default'=>''),
				'heading-5' => array('name'=>'finalheading','type'=>'heading','initialclose'=>true, 'finalclose'=>true),
		);

		add_action('xydac_core_delete',array($this,'xydac_core_delete'));
		add_action('xydac_core_bulkaction',array($this,'xydac_core_bulkaction'));
		add_action('xydac_core_insert_update',array($this,'xydac_core_insert_update'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		//add_filter('xydac_core_rowactions',array($this,'xydac_core_rowactions'));
		add_filter('xydac_core_doactions',array($this,'xydac_core_doactions'));
		add_filter('xydac_core_insert',array($this,'xydac_core_insert'),10,1);
		//parent::__construct("xydac_post_type",__("Custom Post Type",XYDAC_CMS_NAME),XYDAC_CMS_POST_TYPE_PATH,XYDAC_CMS_POST_TYPE_OPTION,$form_variables,true,false);
		//parent::__construct(xydac()->modules->post_type->get_module_name(),xydac()->modules->post_type->get_module_label(),xydac()->modules->post_type->get_base_path(),xydac()->modules->post_type->get_registered_option('main'),$form_variables,true,false);
		$args = array('enableactivation'=>false,'xydac_core_show_additional' => true,'custom_css_id'=>'content_css','custom_jss_id'=>'content_js');
		parent::__construct(xydac()->modules->post_type,'main',$form_variables,$args);
		//if you make the call to constructor before adding filters and action then action and filters will not be enabled
	}
	function xydac_core_leftdiv()
	{
		return "id=accordion";
	}
	function headfootcolumn()
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME),'[args][label]'=>__("Label",XYDAC_CMS_NAME),'[args][hierarchical]'=>__("Hierarchical",XYDAC_CMS_NAME),'[args][description]'=>__("Description",XYDAC_CMS_NAME));
		return $headfootcolumn;
	}
	function xydac_core_delete($name)
	{
		//delete fields option
		//check active option
		delete_option(XYDAC_CMS_POST_TYPE_OPTION."_".$name);

	}
	function xydac_core_rowactions()
	{
		$action = array('Export'=>XYDAC_CMS_EXPORT_PATH."?cpt_name=");
		return $action;
	}
	function xydac_core_doactions()
	{
		$action = array('activate'=>__("Activate",XYDAC_CMS_NAME),'deactivate'=>__("Deactivate",XYDAC_CMS_NAME),'delete'=>__("Delete",XYDAC_CMS_NAME)/* ,'export'=>__("Export",XYDAC_CMS_NAME) */);
		return $action;
	}
	function xydac_core_bulkaction($post)
	{
		switch($_POST['action'])
		{
			case "export" :{
				$cpt ="";
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
					$cpt.=$v.",";
				$cpt = substr($cpt,0,-1);
				$l = get_bloginfo('wpurl')."/wp-content/plugins/".XYDAC_CMS_NAME."/export.php?cpt_name=".$cpt;
				echo "<div id='message' class='updated below-h2'><p><a href=$l>".__('Click Here to download the Export File',XYDAC_CMS_NAME)."</a></p></div>";
			}
		}
	}
	function xydac_core_insert_update()
	{
		if(function_exists('flush_rewrite_rules'))
			flush_rewrite_rules();
	}
	function xydac_core_insert($datas)
	{
		$datas = array(0=>$datas);
		foreach($datas as $k=>$data){
			$datas[$k]['args']['label'] = !empty($datas[$k]['args']['label']) ? $datas[$k]['args']['label'] : xydac_mods_inflect::pluralize($datas[$k]['name']);
			$datas[$k]['args']['labels']['name'] = !empty($datas[$k]['args']['labels']['name']) ? $datas[$k]['args']['labels']['name'] : $datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['singular_name'] = !empty($datas[$k]['args']['labels']['singular_name']) ? $datas[$k]['args']['labels']['singular_name'] : xydac_mods_inflect::singularize($datas[$k]['args']['labels']['name']);
			$datas[$k]['args']['labels']['add_new'] = !empty($datas[$k]['args']['labels']['add_new']) ? $datas[$k]['args']['labels']['add_new'] : 'Add New';
			$datas[$k]['args']['labels']['add_new_item'] = !empty($datas[$k]['args']['labels']['add_new_item']) ? $datas[$k]['args']['labels']['add_new_item'] : 'Add New '.$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['edit_item'] = !empty($datas[$k]['args']['labels']['edit_item']) ? $datas[$k]['args']['labels']['edit_item'] : 'Edit '.$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['new_item'] = !empty($datas[$k]['args']['labels']['new_item']) ? $datas[$k]['args']['labels']['new_item'] : 'New '.$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['view_item'] = !empty($datas[$k]['args']['labels']['view_item']) ? $datas[$k]['args']['labels']['view_item'] : 'View '.$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['search_item'] = !empty($datas[$k]['args']['labels']['search_item']) ? $datas[$k]['args']['labels']['search_item'] : 'Search '.$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['not_found'] = !empty($datas[$k]['args']['labels']['not_found']) ? $datas[$k]['args']['labels']['not_found'] : 'No '.$datas[$k]['args']['label'].' found';
			$datas[$k]['args']['labels']['not_found_in_trash'] = !empty($datas[$k]['args']['labels']['not_found_in_trash']) ? $datas[$k]['args']['labels']['not_found_in_trash'] : 'No '.$datas[$k]['args']['label'].' found in Thrash';
			$datas[$k]['args']['labels']['parent_item_colon'] = !empty($datas[$k]['args']['labels']['parent_item_colon']) ? $datas[$k]['args']['labels']['parent_item_colon'] : 'Parent '.$datas[$k]['args']['label'];
			$datas[$k]['args']['labels']['menu_name'] = !empty($datas[$k]['args']['labels']['menu_name']) ? $datas[$k]['args']['labels']['menu_name'] : $datas[$k]['name'];
			$datas[$k]['args']['public'] = "true";
			$datas[$k]['args']['publicly_queryable'] = "true";
			$datas[$k]['args']['exclude_from_search']= "false";
			$datas[$k]['args']['show_ui']= "true";
			$datas[$k]['args']['capability_type']= "";
			$datas[$k]['args']['hierarchical']= "false";
			$datas[$k]['args']['supports']= array();
			$datas[$k]['args']['supports']['title']= "true";
			$datas[$k]['args']['supports']['editor']= "true";
			$datas[$k]['args']['supports']['author']= "false";
			$datas[$k]['args']['supports']['thumbnail']= "false";
			$datas[$k]['args']['supports']['excerpt']= "false";
			$datas[$k]['args']['supports']['trackbacks']= "false";
			$datas[$k]['args']['supports']['custom-fields']= "false";
			$datas[$k]['args']['supports']['comments']= "false";
			$datas[$k]['args']['supports']['revisions']= "false";
			$datas[$k]['args']['supports']['page-attributes']= "false";
			$datas[$k]['args']['query_var']= "true";
			$datas[$k]['args']['can_export']= "true";
			$datas[$k]['args']['show_in_nav_menus']= "true";
			$datas[$k]['args']['show_in_menu']= "true";
			$datas[$k]['args']['has_archive']= "true";
			$datas[$k]['args']['map_meta_cap']= "true";
			$datas[$k]['args']['rewrite']['feeds'] = "false";
			$datas[$k]['args']['rewrite']['pages'] ="true";
			$datas[$k]['def']['cat']= "false";
			$datas[$k]['def']['tag']= "false";
		}
		return $datas[0];
	}
}

?>