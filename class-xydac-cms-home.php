<?php

class xydac_ultimate_cms_home extends xydac_cms_module{
	var $tabs;
	var $base_path;
    var $homeformfield = array();
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
    function initialize_form_fields(){
        
            
        array_push($this->homeformfield,array('name'=>XYDAC_CMS_USER_API_KEY,
                                              'label'=>'API Key',
                                              'descriptionempty'=>'Xydac Cloud Sync: You can enter the API key to enable the sync facility at Xydac.com to backup your Xydac types. You can get your free API key from <strong><a href="http://xydac.com/account/">Xydac.com</a>. Once you get the API, you need to add this website address on your account pageat Xydac.com to enable sync from this website.',
                                              'description'=>'<a href="http://xydac.com/account/">My Account</a><br/><pre>Xydac Cloud Sync works only with live website currently.</pre>',
                                              'type'=>'text',
                                              'value'=>get_option(XYDAC_CMS_USER_API_KEY)));
        
    }
    
    function render_formelements($name,$label,$type,$description,$value,$options=array()){
        if($type=='text'){
            echo "<input type='text' name='$name' id='$name' value='$value'/><br>";
        }elseif($type=='textarea'){
            echo "<textarea name='$name' id='$name' style='height:200px'> $value</textarea><br>";
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
		if($tabname == 'home'){
            
			if(isset($_POST) && !empty($_POST) && wp_verify_nonce($_POST['xydac_cms_home_form_nonce'], __FILE__))
			{

				if(isset($_POST[XYDAC_CMS_USER_API_KEY]))
					update_option(XYDAC_CMS_USER_API_KEY,$_POST[XYDAC_CMS_USER_API_KEY]);
                if(isset($_POST[xydac_ucms_form])){
                        xydac()->options->formsubmit();
                    unset($_POST[xydac_ucms_form]);
                    
                }
				do_action('xydac_cms_homeprocessform');
				
			}
            $this->initialize_form_fields();
			echo "<h3>Xydac Ultimate-CMS Options</h3>";
			$formcontents = apply_filters('xydac_cms_homeformoption', $this->homeformfield);
            
            
			echo '<form method="post" name="xydac_cms_home_option" action="'.$this->base_path.'">';
			echo '<table class="form-table admin-table"><tbody>';
			foreach($formcontents as $formcontent){
                
			extract($formcontent);
				echo "
			<tr>
				<th><label for='$name'>$label</label></th>
				<td>";
                
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
			echo '<input type="hidden" name="xydac_cms_home_form_nonce" value="'.wp_create_nonce(__FILE__).'"/>';
			echo '<p class="submit"><input type="submit" class="button-primary" value="Submit"/></p>';
			echo '</td></tr>';
			echo '</tbody></table>';
			echo '</form>';
			$this->home_footer();
		}
	}
    
	function home_footer(){
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
			$this->xydac_show_donate_link(false);
			echo "<br/><h2 style=\"font-family: 'Trade Winds', cursive;font-size:40px;text-align:center;text-shadow: -1px 1px 4px #333;\">
			<span style=\"font-size:20px;text-shadow:1px 1px 1px #333\">Code is Poetry</span>
			&nbsp;&nbsp;-&nbsp;XYDAC&nbsp;-&nbsp;&nbsp;
			<span style=\"font-size:20px;text-shadow:1px 1px 1px #333\">Adding Music to poetry</span></h2>";
		
    }
	function xydac_show_donate_link($showimage=true){
		xydac()->xydac_show_donate_link();
	}
}


?>