<?php
/*
Module Name:	Configuration
Type:			Core-Module
Description:	Xydac Configuration
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0

*/
class xydac_ultimate_cms_configuration extends xydac_cms_module{
	var $tabs;
	var $base_path;
    var $configurationformfield = array();
	function __construct()
	{

		$this->base_path = get_bloginfo('wpurl').'/wp-admin/admin.php?page=xydac_ultimate_cms'."&sub=configuration";

		add_action('xydac_cms_module_view_main',array($this,'xydac_cms_module_view_main_func'));
		
		parent::__construct('configuration',array('module_label'=>'Configuration',
				'has_custom_fields'=>false,
				'uses_active'=>false,
				'base_path'=>$this->base_path,
				'tabs'=>$this->tabs
		));
		add_filter('xydac_cms_home_tab',array($this,'xydac_cms_home_tab_func'),30);
		
	}
	public function xydac_cms_home_tab_func($tab){
		$mytab = array('name'=>'configuration',
				'href'=>$this->base_path."&sub=configuration",
				'label'=>'Configuration',
				'default'=>false);
		$github = array('name'=>'github_issue',
				'href'=>"https://github.com/xydac/WP-Ultimate-CMS/issues/new",
				'label'=>'Log an Issue',
				'clazz' => 'right',
				'default'=>false);
				
		if(is_array($tab))
			$tab['configuration'] = $mytab;
		else
			$tab = $mytab; 
		$tab['github_issue'] = $github;
		if(empty(apply_filters('xydac_cms_homeformoption', $this->configurationformfield)))
			unset($tab['configuration']);
		return $tab;
		
	}
    function initialize_form_fields(){
        
		//removeapi - 
		/*  
        array_push($this->configurationformfield,array('name'=>XYDAC_CMS_USER_API_KEY,
                                              'label'=>'API Key',
                                              'descriptionempty'=>'Xydac Cloud Sync: You can enter the API key to enable the sync facility at Xydac.com to backup your Xydac types. You can get your free API key from <strong><a href="http://xydac.com/account/">Xydac.com</a>. Once you get the API, you need to add this website address on your account pageat Xydac.com to enable sync from this website.',
                                              'description'=>'<a href="http://xydac.com/account/">My Account</a><br/><pre>Xydac Cloud Sync works only with live website currently.</pre>',
                                              'type'=>'text',
                                              'value'=>get_option(XYDAC_CMS_USER_API_KEY)));
        */
    }
    
    function render_formelements($name,$label,$type,$description,$value,$options=array()){
        if($type=='text'){
            echo "<input type='text' class='regular-text' name='$name' id='$name' value='$value'/><br>";
        }elseif($type=='textarea'){
            echo "<textarea name='$name' id='$name' class='large-text code' style='height:200px'>$value</textarea><br>";
        }elseif($type=='checkbox'){
            foreach($options as $k=>$v){
                if(is_array($value) && in_array($k,$value))
                    echo "<input id='".$name."[$k]' type='checkbox' checked='checked' name='".$name."[$k]' value='$k' /><label class='checkbox'  for='".$name."[$k]'>$v</label>";
                else
                    echo "<input id='".$name."[$k]' type='checkbox' name='".$name."[$k]' value='$k' /><label class='checkbox'  for='".$name."[$k]'>$v</label>";

            }
        }elseif($type=='radio'){
            foreach($options as $k=>$v){
                if($value==$k)
                    echo "<input id='".$name."[$k]' type='radio' checked='checked' name='".$name."' value='$k' /><label class='checkbox'  for='".$name."[$k]'>$v</label>";
                else
                    echo "<input id='".$name."[$k]' type='radio' name='".$name."' value='$k' /><label class='checkbox'  for='".$name."[$k]'>$v</label>";
            }
        }
                
    }
   
    
	function xydac_cms_module_view_main_func($tabname){
		if($tabname == 'configuration'){
            
			if(isset($_POST) && !empty($_POST) && wp_verify_nonce($_POST['xydac_cms_configuration_form_nonce'], __FILE__))
			{

				//removeapi - 
				/*if(isset($_POST[XYDAC_CMS_USER_API_KEY]))
					update_option(XYDAC_CMS_USER_API_KEY,$_POST[XYDAC_CMS_USER_API_KEY]);
				*/
					if(isset($_POST[xydac_ucms_form])){
                        xydac()->options->formsubmit();
                    unset($_POST[xydac_ucms_form]);
                    
                }
				do_action('xydac_cms_homeprocessform');
				
			}
            $this->initialize_form_fields();
			echo "<h2>Xydac Ultimate-CMS Configuration</h2>";
			$formcontents = apply_filters('xydac_cms_homeformoption', $this->configurationformfield);
            
            
			echo '<form method="post" name="xydac_cms_home_option" action="'.$this->base_path.'">';
			echo '<table class="form-table"><tbody>';
			foreach($formcontents as $formcontent){
                
			extract($formcontent);
				echo "
			<tr>
				<th class='leftthcol'><label for='$name'>$label</label></th>
				<td class='righttdcol'>";
                
                if(isset($contenttype) && $contenttype=='array'){
                    echo '<table>';
                    foreach($array as $f=>$ar){
                       extract($ar);
						echo"<tr><td>$label</td><td>";
						$this->render_formelements($name,$label,$type,$description,$value,$options);
                        echo"</td></tr>";
                        unset($name);
                        unset($label);
                        unset($description);
                        unset($type);
                        unset($value);
                        unset($order);
                    }
                    echo '</table>';
                }else{
                    
                    $this->render_formelements($name,$label,$type,$description,$value,$options);
                }
                if($type=='text' && empty($value)&&isset($descriptionempty) && !empty($descriptionempty))
                        echo "<span class='$name'>$descriptionempty.</span>";
                elseif(!empty($description)){
                        echo "<span class='$name'>$description.</span>";}
                echo "</td>
			</tr>";
                unset($name);
                unset($label);
                unset($description);
                unset($type);
                unset($value);
                unset($order);
                unset($contenttype);
			}
			echo '<tr><th></th><td>';
			echo '<input type="hidden" name="xydac_cms_configuration_form_nonce" value="'.wp_create_nonce(__FILE__).'"/>';
			echo '<p class="submit"><input type="submit" class="button-primary" value="Submit"/></p>';
			echo '</td></tr>';
			echo '</tbody></table>';
			echo '</form>';
			$this->configuration_footer();
		}
	}
    
	function configuration_footer(){
			$this->xydac_show_donate_link(false);
			echo "<br/><h2 style='text-align:center'>
			<span style=\"font-size:20px;text-shadow:1px 1px 1px #333\">Code is Poetry</span>
			&nbsp;&nbsp;-&nbsp;XYDAC&nbsp;-&nbsp;&nbsp;
			<span style=\"font-size:20px;text-shadow:1px 1px 1px #333\">Adding Music to poetry</span></h2>";
		
    }
	function xydac_show_donate_link($showimage=true){
		xydac()->xydac_show_donate_link();
	}
}


?>