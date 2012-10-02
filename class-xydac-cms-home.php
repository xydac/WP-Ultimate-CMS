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
			echo '<table class="form-table"><tbody>';
			foreach($formcontents as $formcontent){
			extract($formcontent);
				echo "
			<tr>
				<th><label for='$name'>$label</label></th>
				<td><input type='text' name='$name' id='$name' value='$value'/><br>
				<span class='$name'>$description.</span></td>
			</tr>";
			}
			echo '</tbody></table>';
			echo '<input type="hidden" name="xydac_cms_home_form_nonce" value="'.wp_create_nonce(__FILE__).'"/>';
			echo '<input type="submit" value="Submit"/>';
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
			<h3>Important Links</h3>
			<ul>
			<li>Plugin Home Page : <a href='http://www.xydac.com/'>http://www.xydac.com/</a></li>
			<li>Post Types on WordPress: <a href='http://codex.wordpress.org/Function_Reference/register_post_type'>WordPress Codex refrence for Post Types</a></li>
			<li>Plugin Page on WordPress : <a href='http://wordpress.org/extend/plugins/ultimate-cms/'>http://wordpress.org/extend/plugins/ultimate-cms/</a></li>
			
			</ul><br/><br/><br/>
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


?>