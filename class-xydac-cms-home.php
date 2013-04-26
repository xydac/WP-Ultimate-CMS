<?php

class xydac_ultimate_cms_home extends xydac_cms_module{
	var $tabs;
	var $base_path;
	function __construct()
	{

		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms';
		$this->tabs = array('home'=>array('name'=>'home',
				'href'=>$this->base_path,
				'label'=>'Home',
				'default'=>true));
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
		$this->view_main();
		
	}
	function xydac_cms_module_view_main_func($tabname){
		if($tabname == 'home'){
			if(isset($_POST) && !empty($_POST) && wp_verify_nonce($_POST['xydac_cms_home_form_nonce'], __FILE__))
			{
				if(isset($_POST[XYDAC_CMS_USER_API_KEY]))
					update_option(XYDAC_CMS_USER_API_KEY,$_POST[XYDAC_CMS_USER_API_KEY]);
				do_action('xydac_cms_homeprocessform');
				
			}
			echo "<h3>Xydac Ultimate-CMS Options</h3>";
			//array('name'=>'','label'=>'','description'=>'');
			$formcontents = apply_filters('xydac_cms_homeformoption', array('0'=>array('name'=>XYDAC_CMS_USER_API_KEY,'label'=>'API Key','description'=>'Please enter your API Key from Xydac.com','value'=>get_option(XYDAC_CMS_USER_API_KEY))));
			echo '<form method="post" name="xydac_cms_home_option" action="'.$this->base_path.'">';
			echo '<table class="form-table admin-table"><tbody>';
			foreach($formcontents as $formcontent){
			extract($formcontent);
				echo "
			<tr>
				<th><label for='$name'>$label</label></th>
				<td><input type='text' name='$name' id='$name' value='$value'/><br>
				<span class='$name'>$description.</span></td>
			</tr>";
			}
			echo '<tr><th></th><td>';
			echo '<input type="hidden" name="xydac_cms_home_form_nonce" value="'.wp_create_nonce(__FILE__).'"/>';
			echo '<input type="submit" class="button-primary" value="Submit"/>';
			echo '</td></tr>';
			echo '</tbody></table>';
			echo '</form>';
			echo "
			<script type='text/javascript'>
			WebFontConfig = {
			google: { families: [ 'Trade+Winds::latin' ] }
			};
			(function() {
			var wf = document.createElement('script');
			wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
			'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
			wf.type = 'text/javascript';
			wf.async = 'true';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(wf, s);
			})(); </script>
			<br/><br/><br/>
			";
			//update_option('xydac_cms_api_key','e83d1d2d159733c72d');
			$this->xydac_show_donate_link(false);
			echo "<br/><h2 style=\"font-family: 'Trade Winds', cursive;font-size:40px;text-align:center;text-shadow: -1px 1px 4px #333;\">
			<span style=\"font-size:20px;text-shadow:1px 1px 1px #333\">Code is Poetry</span>
			&nbsp;&nbsp;-&nbsp;XYDAC&nbsp;-&nbsp;&nbsp;
			<span style=\"font-size:20px;text-shadow:1px 1px 1px #333\">Adding Music to poetry</span></h2>";
			
		
		}
	}
	
	function xydac_show_donate_link($showimage=true){
		xydac()->xydac_show_donate_link();
	}
}


?>