<?php

class xydac_ultimate_cms_home extends xydac_cms_module{
	var $tabs = array();
	var $base_path;
	function __construct()
	{

		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms';
        
		$tab = apply_filters("xydac_cms_home_tab", $this->tabs);
		if(!empty($tab))
			$this->tabs = $tab;
			 
		add_action('xydac_cms_module_view_main',array($this,'xydac_cms_module_view_main_func'));
		
		parent::__construct('home',array('module_label'=>'Xydac Home',
				'has_custom_fields'=>false,
				'uses_active'=>false,
				'base_path'=>$this->base_path,
				'tabs'=>$this->tabs
		));
		if(!isset($_GET['sub']))
			$this->view_main("module_manager");
		else
			$this->view_main();
	}
	function xydac_cms_module_view_main_func($tabname){
		if($tabname == 'home'){
			echo "This should never be shown any more";
		}
	}
    
}


?>