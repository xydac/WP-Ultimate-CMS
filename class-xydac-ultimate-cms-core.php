<?php
/* Copyright: 2011 XYDAC */
/* @todo:use wp-list-table class */
/**
 * This class is used to create a two sided master detail view, having the form on one side and list of objects on another.
 * @author Xydac
 *
 */
abstract class xydac_ultimate_cms_core{

	var $xydac_core_name;
	var $xydac_core_label;
	var $xydac_core_form_action;
	var $xydac_core_form_array;
	var $xydac_core_editmode; 
	var $xydac_editdata; 
	var $xydac_core_message;
	var $xydac_core_error;
	var $option_name;
	var $baselink;
	var $activation;
	var $namefield_name;
	var $xydac_core_show_additional;
	var $arr;
	function __construct($name,$label,$baselink,$optionname,$formarray=array(),$enableactivation=false,$xydac_core_show_additional = true,$arr=array())
	{
		$this->xydac_core_name = $name;
		$this->xydac_core_label = $label;
		$this->baselink = $baselink;
		$this->xydac_core_form_array = $formarray;
		$this->xydac_core_editmode = false;
		$this->xydac_core_message = "";
		$this->xydac_core_error = "";
		$this->option_name = $optionname;
		$this->xydac_core_show_additional = $xydac_core_show_additional;
		$this->activation = $enableactivation;
		$this->xydac_core_form_action = $baselink;
		$this->namefield_name = apply_filters( 'xydac_core_field_name', 'name' );
		$this->arr = $arr;
		if(isset($_POST[$this->xydac_core_name.'_doaction_submit']) || isset($_POST[$this->xydac_core_name.'_update_submit']) || isset($_POST[$this->xydac_core_name.'_add_submit']))
			$this->postHandler();
		$_get = $_GET;
		unset($_get['page']);
		if(!empty($_get))
			$this->getHandler();
		
		if(isset($_GET['sub']) && !empty($_GET['sub'])){
			$baselink_comp = $this->parse_url_detail($this->xydac_core_form_action);
			if(!in_array('sub',$baselink_comp))
				$this->xydac_core_form_action = $baselink."&sub=".$_GET['sub'];
		}
		
		$this->init();
		
		//var_dump($this);die();
	}
	
	/**
	 * Split a given URL into its components.
	 * Uses parse_url() followed by parse_str() on the query string.
	 *
	 * @param string $url The string to decode.
	 * @return array Associative array containing the different components.
	 */
	function parse_url_detail($url){
		$parts = parse_url($url);
	
		if(isset($parts['query'])) {
			parse_str(urldecode($parts['query']), $parts['query']);
		}
	
		return $parts;
	}
	
	function postHandler()
	{
		if(isset($_POST[$this->xydac_core_name.'_doaction_submit'])&& isset($_POST['action']))
			$this->bulk_action();
		else if(isset($_POST[$this->xydac_core_name.'_update_submit']))
			$this->update();
		else if(isset($_POST[$this->xydac_core_name.'_add_submit']))
			$this->insert();
		do_action('xydac_core_posthandler',$this->xydac_core_name); 
	}	
	function getHandler()
	{
		if(isset($_GET["edit_".$this->xydac_core_name]) && 'true'==$_GET["edit_".$this->xydac_core_name] && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->xydac_core_editmode = true;
				$this->xydac_editdata = $this->get_data_byname($_GET[$this->xydac_core_name."_name"]);
			}
		elseif(isset($_GET["activate_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->_activate($_GET[$this->xydac_core_name."_name"]);
			}
		elseif(isset($_GET["deactivate_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->_deactivate($_GET[$this->xydac_core_name."_name"]);
			}
		elseif(isset($_GET["delete_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->delete($_GET[$this->xydac_core_name."_name"]);
			}
		elseif(isset($_GET["sync_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->sync($_GET[$this->xydac_core_name."_name"]);
			}
		do_action('xydac_core_gethandler'); 
		
	}
	
	/* changed $xydac_options = get_option($this->option_name) to $xydac_options = !is_array($this->option_name)?get_option($this->option_name):($this->option_name);
	 * 
	 */
	function _activate($name)
	{
		$xydac_options = !is_array($this->option_name)?get_option($this->option_name):($this->option_name);
		$xydac_active_options = !is_array($this->option_name)?get_option($this->option_name."_active"):get_option($this->arr["active"]);
		if(!is_array($xydac_active_options))
			$xydac_active_options = array();
		if(is_array($xydac_options))
			foreach($xydac_options as $k=>$v)
				if($v[$this->namefield_name]==$name)
					if(!in_array($v[$this->namefield_name],$xydac_active_options))
						array_push($xydac_active_options,$v[$this->namefield_name]);
		if(!is_array($this->option_name))
			update_option($this->option_name."_active",$xydac_active_options);
		else 
			update_option($this->arr["active"],$xydac_active_options);
		
				
	}
	function _deactivate($name)
	{
		$xydac_options = !is_array($this->option_name)?get_option($this->option_name):($this->option_name);
		$xydac_active_options = !is_array($this->option_name)?get_option($this->option_name."_active"):get_option($this->arr["active"]);
		if(!is_array($xydac_active_options))
			return false;
		if(is_array($xydac_active_options))
			foreach($xydac_active_options as $k=>$v)
				if($v==$name)
					unset($xydac_active_options[$k]);
		if(!is_array($this->option_name))
			update_option($this->option_name."_active",$xydac_active_options);
		else 
			update_option($this->arr["active"],$xydac_active_options);
		
	}
	function isActive($name)
	{
		$xydac_active_options = !is_array($this->option_name)?get_option($this->option_name."_active"):get_option($this->arr["active"]);
		//$xydac_active_options = get_option($this->option_name."_active");
		if(is_array($xydac_active_options))
			if(in_array($name,$xydac_active_options))
				return true;
		else
			return false;
	}

	//can be user directly now 
	function get_data_byname($name)
	{
		$xydac_options = get_option($this->option_name);
		if(is_array($xydac_options))
		{foreach($xydac_options as $k=>$v)
			if($v[$this->namefield_name]==$name)
				{$v[$this->xydac_core_name.'_old'] = $name;return $v;}
		}
		else
			return false;
	}
	//should return an array with names
	function get_reg_name()
	{
	$a =array();
	return $a;
	}
	function get_array_val($arr,$key)
	{
		$key = substr(preg_replace('/\]\[/', '-', $key),1,-1);
		$e= explode('-',$key);
		$ar = &$arr;
		foreach($e as $v)
			@$ar = $ar[$v];
		unset($ar);
		return $arr;
		/*if(!is_array($arr))//adding this showed output in text box as ARRAY
			{ $tmp = array();array_push($tmp,$arr);
			return $tmp;}
		else
			return $arr;*/

	}
	private function xy_cmp($a, $b)
	{
		if(isset($a['field_order']) && isset($b['field_order']))
		$k = 'field_order';
	else
		$k = $this->namefield_name;
		if($a[$k]> $b[$k])
			return 1;
		elseif($a[$k]< $b[$k])
			return -1;
		else
			return 0;
	}
	/*
	$_POST['action'] : action to be performed
	$_POST['cbval'] : array of names.
	*/
	function bulk_action()
	{
		switch($_POST['action'])
		{
		
			case "delete" :{
			$i =0;
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
						if($this->delete($v))
							$i++;
			$this->xydac_core_message = $i." ".$this->xydac_core_label.__(' Deleted.',XYDAC_CMS_NAME);break;
			}
			case "activate" :{
				$i =0;
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
						if($this->_activate($v))
							$i++;
				$this->xydac_core_message = $i." ".$this->xydac_core_label.__(' Activated.',XYDAC_CMS_NAME);break;
			}
			case "deactivate" :{
				$i =0;
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
						if($this->_deactivate($v))
							$i++;
				$this->xydac_core_message = $i." ".$this->xydac_core_label.__(' Deactivated.',XYDAC_CMS_NAME);break;
			}
			
		}
		do_action('xydac_core_bulkaction',$_POST,$this); 
	}
	function insert()
	{
		if((isset($_POST[$this->xydac_core_name][$this->namefield_name]) && empty($_POST[$this->xydac_core_name][$this->namefield_name])))
            $this->xydac_core_error= new WP_Error('err', $this->xydac_core_label.__(" Name is required to create ",XYDAC_CMS_NAME).$this->xydac_core_label);
        elseif(in_array(sanitize_title_with_dashes($_POST[$this->xydac_core_name][$this->namefield_name]),$this->get_reg_name()))
            $this->xydac_core_error= new WP_Error('err', $this->xydac_core_label.__(" Name already registered !!!",XYDAC_CMS_NAME));
		elseif(sanitize_title_with_dashes($_POST[$this->xydac_core_name][$this->namefield_name])=="active"){
            $this->xydac_core_error= new WP_Error('err', $this->xydac_core_label.__(" Name Not allowed",XYDAC_CMS_NAME));
        }
        else{
            if(isset($_POST[$this->xydac_core_name][$this->namefield_name]))
            $_POST[$this->xydac_core_name][$this->namefield_name] = sanitize_title_with_dashes($_POST[$this->xydac_core_name][$this->namefield_name]);
            $xydac_options = get_option($this->option_name);
            
            if(!$xydac_options)
            {
                $temp = array();
                array_push($temp,$_POST[$this->xydac_core_name]);
                update_option($this->option_name,apply_filters( 'xydac_core_insert',$temp ));
                
            }
            if(is_array($xydac_options))
            {
                array_push($xydac_options,$_POST[$this->xydac_core_name]);
				usort($xydac_options, array($this,'xy_cmp')); 
                update_option($this->option_name,apply_filters( 'xydac_core_insert',$xydac_options ));
            }
            $this->xydac_core_message = $this->xydac_core_label.__(' Added.',XYDAC_CMS_NAME);
        $this->xydac_core_editmode = false;
        }
		do_action('xydac_core_insert_update');
	}
	function update()
	{
		$this->xydac_core_editmode = true;
        if((isset($_POST[$this->xydac_core_name][$this->namefield_name]) && empty($_POST[$this->xydac_core_name][$this->namefield_name])))
            $this->xydac_core_error = new WP_Error('err', __($this->xydac_core_label.__(" Name is required to create ",XYDAC_CMS_NAME).$this->xydac_core_label));
        elseif(sanitize_title_with_dashes($_POST[$this->xydac_core_name][$this->namefield_name])!=$_POST[$this->xydac_core_name."_old"]){
            $this->xydac_core_error = new WP_Error('err', __("Changing ",XYDAC_CMS_NAME).$this->xydac_core_label.__(" Name is not allowed !!!",XYDAC_CMS_NAME));
        }
        else{
            $_POST[$this->xydac_core_name][$this->namefield_name] = sanitize_title_with_dashes($_POST[$this->xydac_core_name][$this->namefield_name]);
            $xydac_options = get_option($this->option_name);
            if(is_array($xydac_options))
            {
                foreach($xydac_options as $k=>$xydac_option)
                     if($xydac_option[$this->namefield_name]==$_POST[$this->xydac_core_name."_old"])
                     {unset($xydac_options[$k]);break;}
                array_push($xydac_options,$_POST[$this->xydac_core_name]);
				usort($xydac_options, array($this,'xy_cmp')); 
                update_option($this->option_name,apply_filters( 'xydac_core_update',$xydac_options ));
                $this->xydac_core_message = $this->xydac_core_label.__(' Updated.',XYDAC_CMS_NAME);
				$this->xydac_core_editmode = false;
            }
			else
			{
				$this->xydac_core_editmode = true;
			}
        
        }
		do_action('xydac_core_insert_update');
	}
	/*
		Return @true : deleted
		@false : not deleted
	*/
	function delete($name)
	{
		$xydac_options = get_option($this->option_name);
		foreach($xydac_options as $k=>$xydac_option)
			if($xydac_option[$this->namefield_name]==$name)
				{
					unset($xydac_options[$k]);
					usort($xydac_options, array($this,'xy_cmp')); 
					if($this->activation)
						$this->_deactivate($name);
					update_option($this->option_name,$xydac_options);
					$this->xydac_core_message = $this->xydac_core_label.__(' Deleted.',XYDAC_CMS_NAME);
					do_action('xydac_core_delete',$name);
					return true;
				}
            
		$this->xydac_core_error = new WP_Error('err', $this->xydac_core_label.__(" Not Found",XYDAC_CMS_NAME));
		return false;
	}
	//@todo: Code clean up required on this function	
	function sync($name)
	{
		if(xydac()->apikey){
		$xydac_options = get_option($this->option_name);
		foreach($xydac_options as $k=>$xydac_option)
			if($xydac_option[$this->namefield_name]==$name)
			{
				if(isset($xydac_option['sync_id']) && $xydac_option['sync_id']>0)
					$xy_rpc_post = xydac()->xml_rpc_client('wp.getPost',$xydac_option['sync_id'], array('custom_fields'));
				$actual_code_id =-1;
				$field_code_id =-1;
				if(isset($xy_rpc_post) && $xy_rpc_post->isError()){
					if(404==$xy_rpc_post->getErrorCode())
						{
							unset($xydac_options[$k]['sync_id']);
							update_option($this->option_name,$xydac_options);
						}
					$this->xydac_core_error = new WP_Error($xy_rpc_post->getErrorCode(), $xy_rpc_post->getErrorMessage().' Sync ID:'.$xydac_option['sync_id']);
					return false;
				}else if(isset($xy_rpc_post) && !$xy_rpc_post->isError()) {
					$xy_rpc_post = $xy_rpc_post->getResponse(); 
					foreach($xy_rpc_post['custom_fields'] as $arr)
						{
							if($arr['key']=='actual_code')
								$actual_code_id = (int)$arr['id'];
							else if($arr['key']=='field_code')
								$field_code_id = (int)$arr['id'];
						}
				}
				$content['post_title'] = $xydac_option[$this->namefield_name];
				$content['post_type'] = 'xydac_'.$this->xydac_core_name;
				$content['post_content'] = '<p>'.$xydac_option['description'].'</p>';
				if($actual_code_id>0 && $field_code_id>0)
					$content['custom_fields'] = array( array('id'=>$actual_code_id,'key' => 'actual_code','value'=>base64_encode(maybe_serialize($xydac_option))),array('id'=>$field_code_id,'key' => 'field_code','value'=>base64_encode(maybe_serialize(''))));
				else
					$content['custom_fields'] = array( array('key' => 'actual_code','value'=>base64_encode(maybe_serialize($xydac_option))),
													array('key' => 'field_code','value'=>base64_encode(maybe_serialize(''))));
				
				
				 if(isset($xydac_option['sync_id']) && (int)$xydac_option['sync_id']>0)
					  $result = xydac()->xml_rpc_client('wp.editPost',$xydac_option['sync_id'], $content);
				  else
					$result = xydac()->xml_rpc_client('wp.newPost', $content);
				
				if($result->isError()){
					if(404==$result->getErrorCode())
						{
							unset($xydac_options[$k]['sync_id']);
							update_option($this->option_name,$xydac_options);
						}
					$this->xydac_core_error = new WP_Error($result->getErrorCode(), $result->getErrorMessage().' Sync ID:'.$xydac_option['sync_id']);
					return false;
				}else{
					$result = $result->getResponse();
					if(!isset($xydac_option['sync_id']) && $result!='1')
					{
						$xydac_options[$k]['sync_id'] = $result;
						update_option($this->option_name,$xydac_options);
					}
					$this->xydac_core_message = $this->xydac_core_label.__(' Synced '.$result.' .',XYDAC_CMS_NAME);
					do_action('xydac_core_sync',$name);
					return true;
				}
			}
	
			$this->xydac_core_error = new WP_Error('err', $this->xydac_core_label.__(" Not Found",XYDAC_CMS_NAME));
			return false;
		}else {
			$this->xydac_core_error = new WP_Error('err', $this->xydac_core_label.__(" Api key is not defined",XYDAC_CMS_NAME));
			return false;
		}
			
			
	}
	
	function init()
	{ 
	$xydac_rowdata = !is_array($this->option_name)?get_option($this->option_name):($this->option_name);
	$this->xydac_editdata = stripslashes_deep($this->xydac_editdata);
	?>
		<?php //if(!xydac()->is_xydac_ucms_pro())xydac()->xydac_show_donate_link(); ?>
		<?php do_action('xydac_core_head'); ?>
		<?php if(!empty($this->xydac_core_message)) { ?>
		<div id="message" class="updated below-h2"><p><?php echo $this->xydac_core_message; ?></p></div>
		<?php } ?>
		<?php if(!empty($this->xydac_core_error) && is_wp_error($this->xydac_core_error)) { ?>
		<div id="error" class="error below-h2"><p><?php echo $this->xydac_core_error->get_error_message(); ?></p></div>
		<?php } ?>
		<br class="clear" />
		<div id="col-container" class="<?php echo $this->xydac_core_name;?>">
			<div id="col-right">
				<div class="form-wrap">
				<?php do_action('xydac_core_righthead'); ?>
					<form id="form_edit_doaction" action="<?php if($this->xydac_core_editmode) echo $this->xydac_core_form_action.'&edit_'.$this->xydac_core_name.'=true&'.$this->xydac_core_name.'_name='.$this->xydac_editdata[$this->namefield_name]; else echo $this->xydac_core_form_action; ?>" method="post">
						<div class="tablenav">
							<select name="action">
								<option value=""><?php _e('Bulk Actions',XYDAC_CMS_NAME); ?></option>
								<?php 
								
								$doactions = array('delete'=>__("Delete",XYDAC_CMS_NAME));
								$act = array('activate'=>__("Activate",XYDAC_CMS_NAME),'deactivate'=>__("Deactivate",XYDAC_CMS_NAME));
								if($this->activation)
									$doaction = array_merge($doactions,$act);
								$doactions = apply_filters( 'xydac_core_doactions', $doactions );
								foreach($doactions as $val=>$label)
									echo "<option value=".$val.">".$label."</option>";
								?>
							</select>
							
							<input type="submit" class="button-secondary action"  id="<?php echo $this->xydac_core_name.'_doaction_submit'; ?>" name="<?php echo $this->xydac_core_name.'_doaction_submit'; ?>" value="Apply"/>
						</div>
						<br class="clear">
						<table class="widefat tag fixed" cellspacing="0">
							<thead class="content-types-list">
								<tr>
									<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
									
									<?php 
										$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME));
										$headfootcolumn = apply_filters( 'xydac_core_headfootcolumn', $headfootcolumn );
										foreach($headfootcolumn as $name=>$label)
											echo '<th class="manage-column column-name" id="'.$name.'" scope="col">'.$label.'</th>';
									?>
								</tr>
							</thead>
							<tbody id="the-list">
								<?php
								if(is_array($xydac_rowdata))
								foreach($xydac_rowdata as $value)
								{ $name = $value[$this->namefield_name];
								?>
									<tr id="content-type-<?php echo $name; ?>" class="<?php if(!$this->isActive($name)) echo "xydacinactive"; else echo "xydacactive"; ?>">
										<th class="check-column" scope="row">
											<input type="checkbox" value="<?php echo $name; ?>" name="cbval[]"/>
										</th>
										<td class="name column-name">
											<strong>
											<?php if(!isset($this->arr['show_link']) || isset($this->arr['show_link'])&& $this->arr['show_link']=='true') { ?>
												<a class="row-title" title="Edit &ldquo;<?php echo $name; ?>&rdquo;" href="<?php echo $this->baselink."&edit_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name; ?>">
													<?php echo $name; ?>
												</a>
												<?php } else {  echo $name; } ?>
											</strong>
											<br />
											<?php 
												$rowactions = array();
												$rowactions = apply_filters( 'xydac_core_rowactions', $rowactions );
												if($this->activation)
												{
													echo '<div class="row-actions-visible" style="display:inline">';
													if(!$this->isActive($name))
														echo '<span><a href="'.$this->baselink."&activate_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name.'">'."Activate".'</a></span>';
													else
														echo '<span class="delete"><a href="'.$this->baselink."&deactivate_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name.'">'."Deactivate".'</a></span>';
													echo '</div>';
												}
												if(!empty($rowactions))
												{	
													echo '<div class="row-actions" style="display:inline">';
													foreach($rowactions as $actionname=>$actionlink)
														echo '<span class="'.strtolower($actionname).'"> | <a href="'.$actionlink.$name.'">'.$actionname.'</a></span>';
													echo '<span class="delete"> | <a href="'.$this->baselink."&delete_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name.'">'."Delete".'</a></span>';
													echo '<span class="sync"> | <a href="'.$this->baselink."&sync_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name.'">'."Sync".'</a></span>';
													echo '</div>';
											    }?>
										</td>
										<?php 
										/*put value as array and $v as [field_label]*/
										if(is_array($value) && is_array($headfootcolumn)) 
											foreach($headfootcolumn as $v=>$k)  
												{
												if($v!=$this->namefield_name){
												$val = $this->get_array_val($value,$v);
												if(is_array($val))
													$val = implode(',',$val);	
										?><td class="categories column-categories">
										<?php echo $val; ?>
										</td>
										<?php }}  ?>

									</tr>
								<?php //echo $row->field_name;
								}   ?>
							</tbody>
								<tfoot>
									<tr>
										<th class="manage-column column-cb check-column"  scope="col"><input type="checkbox"></th>
										<?php 
											foreach($headfootcolumn as $name=>$label)
											echo '<th class="manage-column column-name" id="'.$name.'" scope="col">'.$label.'</th>';
										?>
									</tr>
							</tfoot>
						</table>
					</form>
					<?php do_action('xydac_core_rightfoot'); ?>
					<br class="clear">
					<br class="clear">
				</div>
			</div>

			<div id='col-left'>
				<div class='col-wrap'>
					<div class='form-wrap'>
					<?php do_action('xydac_core_lefthead'); ?>
						
						<form <?php if($this->xydac_core_editmode) echo "id='form_edit_".$this->xydac_core_name."'"; else echo "id='form_create_'".$this->xydac_core_name."'"; ?> action='<?php if($this->xydac_core_editmode) echo $this->xydac_core_form_action.'&edit_'.$this->xydac_core_name.'=true&'.$this->xydac_core_name.'_name='.$this->xydac_editdata[$this->namefield_name]; else echo $this->xydac_core_form_action; ?>' method='post'>
							<div class="xydacfieldform">
							<h3><?php if($this->xydac_core_editmode) echo __('Edit ',XYDAC_CMS_NAME).$this->xydac_core_label; else echo __('Add ',XYDAC_CMS_NAME).$this->xydac_core_label; ?>
							<?php if($this->xydac_core_editmode) echo "<a  style='color:red;float:right;'  href='".$this->xydac_core_form_action."&edit_".$this->xydac_core_name."=false'>Cancel Edit</a>";  ?>
							</h3>
							<div class="form-field form-required <?php if(isset($_POST[$this->xydac_core_name.'_update_submit']) || isset($_POST[$this->xydac_core_name.'_add_submit'])) if(isset($_POST[$this->xydac_core_name][$this->namefield_name]) && empty($_POST[$this->xydac_core_name][$this->namefield_name])) echo 'form-invalid';?>"  >
								<label for='<?php echo $this->xydac_core_name.'['.$this->namefield_name.']'; ?>'><?php _e('The Name of the ',XYDAC_CMS_NAME);?><?php echo $this->xydac_core_label; ?></label>
								<input type='text' name='<?php echo $this->xydac_core_name.'['.$this->namefield_name.']'; ?>' <?php if($this->xydac_core_editmode) echo "readonly"; ?> class='name' id='<?php echo $this->xydac_core_name.'['.$this->namefield_name.']'; ?>' value="<?php if($this->xydac_core_editmode) echo $this->xydac_editdata[$this->namefield_name]; ?>" />
								<p><?php echo $this->xydac_core_label;  _e('Name identifies your ',XYDAC_CMS_NAME); echo $this->xydac_core_label; _e('among others. It is usually all lowercase and contains only letters, numbers, and hyphens.',XYDAC_CMS_NAME); ?></p>
							</div>
							</div>
							<?php if($this->xydac_core_editmode || $this->xydac_core_show_additional) {?>
							<!--START ADDED FORM SECTION -->
							
							<?php
							
							echo "<div ".apply_filters( 'xydac_core_leftdiv', '' ).">";
							/*
							'arr_label' => '' ,
							'name' => '[][][]', 
							'type'=>'textarea', 
							'desc'=> '', 
							'default'=>''
							*/
							if(is_array($this->xydac_core_form_array))
							foreach($this->xydac_core_form_array as $name=>$data)
							{
							extract($data);
							if($type == 'heading'){ 
								if($initialclose) echo "</div>"; 
								if(!isset($finalclose)) {  echo "<h3>".$arr_label."</h3><div>"; } 
							} else {
							?>
								<div class='form-field' id="xydac_panel_<?php echo $this->xydac_core_name.$name ?>"  >
									<?php if($type=='boolean')
									{?><label for='<?php echo $this->xydac_core_name.$name ?>' style="display:inline;font-weight:bold;"><?php echo $arr_label; ?></label>
										<select id='<?php echo $this->xydac_core_name.$name; ?>' name='<?php echo $this->xydac_core_name.$name ?>' class='postform' style="float:right;width:100px;margin-right:5%">
											<option value='true' <?php  if($default=='true' && !$this->xydac_core_editmode) {echo 'selected';}elseif($this->xydac_core_editmode) { if('true'==$this->get_array_val($this->xydac_editdata,$name)) echo "selected"; }  ?>><?php echo 'True'; ?></option>
											<option value='false' <?php if($default=='false' && !$this->xydac_core_editmode){ echo 'selected';}elseif($this->xydac_core_editmode) {if('false'==$this->get_array_val($this->xydac_editdata,$name)) echo "selected";} ?>><?php echo 'False'; ?></option>
										</select>
									<?php } elseif($type=='array') { ?>
										<label for='<?php echo $this->xydac_core_name.$name ?>' style="display:inline;font-weight:bold;"><?php echo $arr_label ?></label>
										<select id='<?php echo $this->xydac_core_name.$name; ?>' name='<?php echo $this->xydac_core_name.$name ?>' class='postform' style="float:right;width:150px;margin-right:5%">
											<?php  foreach($values as $n=>$c) {   ?>
												<option value='<?php echo $n; ?>' <?php if($default==$n && !$this->xydac_core_editmode) {echo 'selected';}elseif($this->xydac_core_editmode) { if($n==$this->get_array_val($this->xydac_editdata,$name)) echo 'selected'; }  ?>><?php echo $c ?></option>
												<?php } ?>
										</select>
									<?php } elseif($type=='string') { ?><label for='<?php echo $this->xydac_core_name.$name ?>' style="font-weight:bold;"><?php echo $arr_label ?></label>
										<input type='text' name='<?php echo $this->xydac_core_name.$name ?>' class='name' id='<?php echo $this->xydac_core_name.$name ?>' value="<?php if($this->xydac_core_editmode) { echo $this->get_array_val($this->xydac_editdata,$name);} ?>"/>
									
									<?php } elseif($type=='checkbox') { ?><label for='<?php echo $this->xydac_core_name.$name ?>' style="font-weight:bold;"><?php echo $arr_label ?></label>
										<?php $_checkboxeditdata = $this->get_array_val($this->xydac_editdata,$name);$_i=0; 
										if(!is_array($_checkboxeditdata)) {$_checkboxeditdata= array($_checkboxeditdata);}
										foreach($values as $val_name=>$val_label){ ?>
											<div style="width:180px;float:left;"><input type='checkbox' style="width:15px;margin-left:20px" name="<?php echo $this->xydac_core_name.$name."[]"; ?>" id="<?php echo $this->xydac_core_name.$name; ?>" value="<?php _e($val_name,'xydac'); ?>" <?php if($this->xydac_core_editmode && in_array($val_name,$_checkboxeditdata)) echo "checked=checked"; ?>  />&nbsp;<?php _e($val_label,'xydac'); ?></div><?php if($_i==1) {$_i=0;echo "<br />";}else $_i++; ?>
										<?php } ?>
									<?php } elseif($type=='textarea') { ?><label for='<?php echo $this->xydac_core_name.$name ?>' style="font-weight:bold;"><?php echo $arr_label ?></label>
										<textarea style="height:<?php if(isset($height)) echo $height; else echo "300px"; ?>" name='<?php echo $this->xydac_core_name.$name; ?>' class='name' id='<?php echo $this->xydac_core_name.$name; ?>'><?php if($this->xydac_core_editmode) {echo $this->get_array_val($this->xydac_editdata,$name); } ?></textarea>
									<?php } ?>
								<p><?php echo $desc ?></p>
								</div>
							<?php }}  ?>
							<!--END ADDED FORM SECTION -->
							</div>
							<?php do_action("xydac_core_form"); ?>
							
								<input type="hidden" name="<?php echo $this->xydac_core_name."_old"; ?>" value="<?php echo $this->xydac_editdata[$this->xydac_core_name.'_old'];?>">
						<?php } ?>
						
						<p class='submit'>
						<input type="submit"  name="<?php if($this->xydac_core_editmode) echo $this->xydac_core_name.'_update_submit'; else  echo $this->xydac_core_name.'_add_submit'; ?>" class="button-primary" value="<?php if($this->xydac_core_editmode) _e('Update '.$this->xydac_core_label,$this->xydac_core_name); else  _e('Add '.$this->xydac_core_label,$this->xydac_core_name); ?>"></p>
						</form>
					<?php do_action('xydac_core_leftfoot'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php do_action('xydac_core_foot'); ?>
	<?php //if(!xydac()->is_xydac_ucms_pro())xydac()->xydac_show_donate_link(false); ?>
	
	<?php }
	
} ?>