<?php
/* Copyright: 2011 XYDAC */
/* @todo:use wp-list-table class */
/**
 * This class is used to create a two sided master detail view, having the form on one side and list of objects on another.
 * @author Xydac
 *
 */
abstract class xydac_ultimate_cms_core{
	var $parent_class; // The main class that uses object of this class
	var $type; //used in parent class; 
	var $field_val; //this has the name of type of which field is being edited.
	
		var $xydac_core_name;
		var $xydac_core_label;
		var $xydac_core_form_action;
	var $xydac_core_form_array;
	var $xydac_core_editmode = false; 
	var $xydac_editdata; 
	var $xydac_core_message = "";
	var $xydac_core_error = "";
		var $option_name;
		var $option_value;
		var $baselink;
		var $activation;
	var $namefield_name;
	var $xydac_core_show_additional;//Is used to show/hide additional form elements on left side.
	var $xydac_core_show_left;//Is used to show/hide left side.
	var $xydac_core_show_doaction;//Is used to show/hide left side.
	var $xydac_core_show_delete;//Is used to show/hide delete row action.
	var $xydac_core_show_sync;//Is used to show/hide sync rowaction.
	/**
	 * array used to store additional data
	 *  $xydac_core_show_additional
	 * @var array
	 */
	var $args; 
	
	function __construct($obj,$type,$formarray=array(),$args=array())
	{
		//var_dump($obj);die();
		if(!($obj instanceof xydac_cms_module) || !($type=='main' || $type=='field'))
			return;
		
		extract($args);
		$this->parent_class = $obj;
		$this->xydac_core_name = $obj->get_module_name();
		$this->xydac_core_label = ($type=='main') ? $obj->get_module_label() : $obj->get_module_label().' Fields';
		$this->type = $type;
		$tab = (($this->type=='main')?'modules':(($this->type=='field')?'fields':'xydac_sync'));
		$this->baselink = apply_filters( 'xydac_core_editlink',$obj->get_base_path($tab));
			$this->xydac_core_form_array = apply_filters('xydac_core_form_array', $formarray,$this->xydac_core_name);
			$this->field_val = $field_val = isset($field_val)?$field_val:'';
		/* if(isset($optionname) && isset($optionvalue) && is_array($optionvalue)){
			$this->option_value = $optionvalue;
		} */
		$this->option_name = ($type=='main') ? $obj->get_registered_option('main'):(($type=='field') ? $obj->get_registered_option('field').'_'.$field_val:'');
			$this->xydac_core_show_additional = isset($xydac_core_show_additional)?$xydac_core_show_additional : true;
			$this->xydac_core_show_left = isset($xydac_core_show_left)?$xydac_core_show_left : true;
			$this->xydac_core_show_doaction = isset($xydac_core_show_doaction)?$xydac_core_show_doaction : true;
			$this->xydac_core_show_delete = isset($xydac_core_show_delete)?$xydac_core_show_delete : true;
			$this->xydac_core_show_sync = isset($xydac_core_show_sync)?$xydac_core_show_sync : true;
		if(!xydac()->is_xydac_ucms_pro())
			$this->xydac_core_show_sync = false;
			
		$this->activation = $obj->uses_active($this->type);
		$this->xydac_core_form_action =  apply_filters( 'xydac_core_editlink',$obj->get_base_path($tab));
			$this->namefield_name = apply_filters( 'xydac_core_field_name', 'name' );
			$this->args = $args;
		if(isset($_POST['xydac_form_'.$this->xydac_core_name.'_doaction_submit']) || isset($_POST['xydac_form_'.$this->xydac_core_name.'_update_submit']) || isset($_POST['xydac_form_'.$this->xydac_core_name.'_add_submit']))
			$this->postHandler();
		$_get = $_GET;
		unset($_get['page']);
		if(!empty($_get))
			$this->getHandler();
		$this->init();
		
		//var_dump($this);die();
	}
	
	function postHandler()
	{

		if(isset($_POST['xydac_form_'.$this->xydac_core_name.'_doaction_submit'])&& isset($_POST['action']))
			$this->bulk_action();
		else if(isset($_POST['xydac_form_'.$this->xydac_core_name.'_update_submit']))
			$this->update();
		else if(isset($_POST['xydac_form_'.$this->xydac_core_name.'_add_submit']))
			$this->insert();
		do_action('xydac_core_posthandler',$this->xydac_core_name); 
	}	
	function getHandler()
	{
		if(isset($_GET["edit_".$this->xydac_core_name]) && 'true'==$_GET["edit_".$this->xydac_core_name] && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->xydac_core_editmode = true;
				$this->xydac_editdata = ($this->type=='main')? $this->parent_class->get_main_by_name($_GET[$this->xydac_core_name."_name"]) : $this->parent_class->get_field_by_name($this->field_val,$_GET[$this->xydac_core_name."_name"]) ;
				//if manage is set it means we are at fields
				if(isset($_GET["manage_".$this->xydac_core_name])){
					$key = $_GET[$this->xydac_core_name."_name"];
					$this->xydac_editdata = $this->xydac_editdata[$key];
				}
				
				//$this->xydac_editdata = $this->parent_class->get_main_by_name($_GET[$this->xydac_core_name."_name"]);
				$this->xydac_editdata[$this->xydac_core_name.'_old'] = $_GET[$this->xydac_core_name."_name"];
			}
		elseif(isset($_GET["activate_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->parent_class->activate_main($_GET[$this->xydac_core_name."_name"]);
			}
		elseif(isset($_GET["deactivate_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->parent_class->deactivate_main($_GET[$this->xydac_core_name."_name"]);
			}
		elseif(isset($_GET["delete_".$this->xydac_core_name]) && isset($_GET[$this->xydac_core_name."_name"]))
			{
				$this->delete($_GET[$this->xydac_core_name."_name"]);
				$this->xydac_core_editmode = false;
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
	function isActive($name)
	{
		return $this->parent_class->is_main_active($name);
	}

	//can be used directly now 
	function get_array_val($arr,$key)
	{
		$key = substr(preg_replace('/\]\[/', '$$', $key),1,-1);
		$e= explode('$$',$key);
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
						if($this->$this->parent_class->activate_main($v))
							$i++;
				$this->xydac_core_message = $i." ".$this->xydac_core_label.__(' Activated.',XYDAC_CMS_NAME);break;
			}
			case "deactivate" :{
				$i =0;
				if(isset($_POST['cbval']))
					foreach($_POST['cbval'] as $v)
						if($this->parent_class->deactivate_main($v))
							$i++;
				$this->xydac_core_message = $i." ".$this->xydac_core_label.__(' Deactivated.',XYDAC_CMS_NAME);break;
			}
			
		}
		do_action('xydac_core_bulkaction',$_POST,$this); 
	}
	function insert()
	{
		
		$msg = $this->parent_class->insert_object($this->type, $_POST['xydac_form_'.$this->xydac_core_name][$this->namefield_name],isset($_GET['manage_'.$this->xydac_core_name])?$_GET['manage_'.$this->xydac_core_name]:'', apply_filters( 'xydac_core_insert',$_POST['xydac_form_'.$this->xydac_core_name]),$this->namefield_name);
		if(is_wp_error(($msg)))
			$this->xydac_core_error= $msg;
		else{
			$this->xydac_core_message = $msg;
			$this->xydac_core_editmode = false;
		}
		do_action('xydac_core_insert_update');
	}
	function update()
	{
	
		$this->xydac_core_editmode = true;
		$msg= $this->parent_class->update_object($this->type, $_POST['xydac_form_'.$this->xydac_core_name][$this->namefield_name],isset($_GET['manage_'.$this->xydac_core_name])?$_GET['manage_'.$this->xydac_core_name]:'', apply_filters( 'xydac_core_update',$_POST['xydac_form_'.$this->xydac_core_name]),$_POST['xydac_form_'.$this->xydac_core_name."_old"],$this->namefield_name);
		if(is_wp_error(($msg))){
			$this->xydac_core_error= $msg;
			$this->xydac_core_editmode = true;
		}
		else{
			$this->xydac_core_message = $msg;
			$this->xydac_core_editmode = false;
		}
		do_action('xydac_core_insert_update');
	}
	/*
		Return @true : deleted
	@false : not deleted
	*/
	function delete($name)
	{
		$msg = $this->parent_class->delete_object($this->type, $name,isset($_GET['manage_'.$this->xydac_core_name])?$_GET['manage_'.$this->xydac_core_name]:'', $this->namefield_name);
		if(is_wp_error(($msg))){
			$this->xydac_core_error= $msg;
			do_action('xydac_core_insert_update');
			return false;
		}
		else{
			$this->xydac_core_message = $msg;
			do_action('xydac_core_insert_update');
			return true;
		}

	}
	//@todo: Code clean up required on this function
	
	function sync($name)
	{
	$msg = $this->parent_class->sync_object($this->type, $name, $this->namefield_name);
		if(is_wp_error(($msg))){
			$this->xydac_core_error= $msg;
			do_action('xydac_core_sync');
					return false;
					}
			
			
			
		else{
			$this->xydac_core_message = $msg;
			do_action('xydac_core_sync');
					return true;
			
	
		}
			
			
	}
	
	function init()
	{ 
	$xydac_rowdata = apply_filters( 'xydac_core_rowdata', ($this->type=='main')? $this->parent_class->get_main() : $this->parent_class->get_field($this->field_val) );//!is_array($this->option_name)?get_option($this->option_name):($this->option_name);
	$this->xydac_editdata = stripslashes_deep($this->xydac_editdata);
	?>
		<?php if(!xydac()->is_xydac_ucms_pro())xydac()->xydac_show_donate_link(); ?>
		<?php do_action('xydac_core_head'); ?>
		<?php if(!empty($this->xydac_core_message)) { ?>
		<div id="message" class="updated below-h2"><p><?php echo $this->xydac_core_message; ?></p></div>
		<?php } ?>
		<?php if(!empty($this->xydac_core_error) && is_wp_error($this->xydac_core_error)) { ?>
		<div id="error" class="error below-h2"><p><?php echo $this->xydac_core_error->get_error_message(); ?></p></div>
		<?php } ?>
		<br class="clear" />
		<div id="col-container" class="<?php echo $this->xydac_core_name;?>">
		<?php if($this->xydac_core_show_left){?>
			<div id="col-right">
			<?php }else{?>
			<div id="col">
			<?php }?>
				<div class="form-wrap">
				<?php do_action('xydac_core_righthead'); ?>
				
					<form id="form_edit_doaction" action="<?php if($this->xydac_core_editmode) echo $this->xydac_core_form_action.'&edit_'.$this->xydac_core_name.'=true&'.$this->xydac_core_name.'_name='.$this->xydac_editdata[$this->namefield_name]; else echo $this->xydac_core_form_action; ?>" method="post">
					<?php if($this->xydac_core_show_doaction){ ?>
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
						<?php } ?>
						<br class="clear">
						<table class="widefat tag fixed">
							<thead class="content-types-list">
								<tr>
									<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
									
									<?php 
										$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME));
										$headfootcolumn = apply_filters( 'xydac_core_headfootcolumn', $headfootcolumn );
										foreach($headfootcolumn as $name=>$label)
											echo '<th class="manage-column xydac-col-'.$this->xydac_core_name.'-'.str_replace(array('[',']',' '),'',$name).'" id="'.$name.'" scope="col">'.$label.'</th>';
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
											<?php if(!isset($this->args['show_link']) || isset($this->args['show_link'])&& $this->args['show_link']=='true') { ?>
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
												/* if(!empty($rowactions))
												{	 */
													echo '<div class="row-actions" style="display:inline">';
													foreach($rowactions as $actionname=>$actionlink)
														echo '<span class="'.strtolower($actionname).'"> | <a href="'.$actionlink.$name.'">'.$actionname.'</a></span>';
													if($this->xydac_core_show_delete)
														echo '<span class="delete"> | <a href="'.$this->baselink."&delete_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name.'">'."Delete".'</a></span>';
													if($this->xydac_core_show_sync)
														echo '<span class="sync"> | <a href="'.$this->baselink."&sync_".$this->xydac_core_name."=true&".$this->xydac_core_name."_name=".$name.'">'."Sync".'</a></span>';
													echo '</div>';
											   /*  } */?>
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
										?><td class="categories xydac-col-<?php echo $this->xydac_core_name.'-'.str_replace(array('[',']',' '),'',$v);?>">
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
											echo '<th class="manage-column xydac-col-'.$this->xydac_core_name.'-'.str_replace(array('[',']',' '),'',$name).'" id="'.$name.'" scope="col">'.$label.'</th>';
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
<?php if($this->xydac_core_show_left){ ?>
			<div id='col-left'>
				<div class='col-wrap'>
					<div class='form-wrap'>
					<?php do_action('xydac_core_lefthead'); ?>
						
						<form <?php if($this->xydac_core_editmode) echo "id='form_edit_".$this->xydac_core_name."'"; else echo "id='form_create_".$this->xydac_core_name."'"; ?> action='<?php if($this->xydac_core_editmode) echo $this->xydac_core_form_action.'&edit_'.$this->xydac_core_name.'=true&'.$this->xydac_core_name.'_name='.$this->xydac_editdata[$this->namefield_name]; else echo $this->xydac_core_form_action; ?>' method='post'>
							<div class="xydacfieldform">
							<h3><?php if($this->xydac_core_editmode) echo __('Edit ',XYDAC_CMS_NAME).$this->xydac_core_label; else echo __('Add ',XYDAC_CMS_NAME).$this->xydac_core_label; ?>
							<?php if($this->xydac_core_editmode) echo "<a  style='color:red;float:right;'  href='".$this->xydac_core_form_action."&edit_".$this->xydac_core_name."=false"."'>Cancel Edit</a>";  ?>
							</h3>
							<div class="form-field form-required <?php if(isset($_POST['xydac_form_'.$this->xydac_core_name.'_update_submit']) || isset($_POST['xydac_form_'.$this->xydac_core_name.'_add_submit'])) if(isset($_POST['xydac_form_'.$this->xydac_core_name][$this->namefield_name]) && empty($_POST['xydac_form_'.$this->xydac_core_name][$this->namefield_name])) echo 'form-invalid';?>"  >
								<label for='<?php echo $this->xydac_core_name.'['.$this->namefield_name.']'; ?>'><?php _e('The Name of the ',XYDAC_CMS_NAME);?><?php echo $this->xydac_core_label; ?></label>
								<input type='text' name='xydac_form_<?php echo $this->xydac_core_name.'['.$this->namefield_name.']'; ?>' <?php if($this->xydac_core_editmode) echo "readonly"; ?> class='name' id='<?php echo $this->xydac_core_name.'['.$this->namefield_name.']'; ?>' value="<?php if($this->xydac_core_editmode) echo $this->xydac_editdata[$this->namefield_name]; ?>" />
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
										<select id='<?php echo $this->xydac_core_name.$name; ?>' name='xydac_form_<?php echo $this->xydac_core_name.$name ?>' class='postform' style="float:right;width:100px;margin-right:5%">
											<option value='true' <?php  if($default=='true' && !$this->xydac_core_editmode) {echo 'selected';}elseif($this->xydac_core_editmode) { if('true'==$this->get_array_val($this->xydac_editdata,$name)) echo "selected"; }  ?>><?php echo 'True'; ?></option>
											<option value='false' <?php if($default=='false' && !$this->xydac_core_editmode){ echo 'selected';}elseif($this->xydac_core_editmode) {if('false'==$this->get_array_val($this->xydac_editdata,$name)) echo "selected";} ?>><?php echo 'False'; ?></option>
										</select>
									<?php } elseif($type=='array') { ?>
										<label for='<?php echo $this->xydac_core_name.$name ?>' style="display:inline;font-weight:bold;"><?php echo $arr_label ?></label>
										<select id='<?php echo $this->xydac_core_name.$name; ?>' name='xydac_form_<?php echo $this->xydac_core_name.$name ?>' class='postform' style="float:right;width:150px;margin-right:5%">
											<?php  foreach($values as $n=>$c) {   ?>
												<option value='<?php echo $n; ?>' <?php if($default==$n && !$this->xydac_core_editmode) {echo 'selected';}elseif($this->xydac_core_editmode) { if($n==$this->get_array_val($this->xydac_editdata,$name)) echo 'selected'; }  ?>><?php echo $c ?></option>
												<?php } ?>
										</select>
									<?php } elseif($type=='string') { ?><label for='<?php echo $this->xydac_core_name.$name ?>' style="font-weight:bold;"><?php echo $arr_label ?></label>
										<input type='text' name='xydac_form_<?php echo $this->xydac_core_name.$name ?>' class='name' id='<?php echo $this->xydac_core_name.$name ?>' value="<?php if($this->xydac_core_editmode) { echo $this->get_array_val($this->xydac_editdata,$name);} ?>"/>
									
									<?php } elseif($type=='checkbox') { ?><label for='<?php echo $this->xydac_core_name.$name ?>' style="font-weight:bold;"><?php echo $arr_label ?></label>
										<?php $_checkboxeditdata = $this->get_array_val($this->xydac_editdata,$name);$_i=0; 
										if(!is_array($_checkboxeditdata)) {$_checkboxeditdata= array($_checkboxeditdata);}
										foreach($values as $val_name=>$val_label){ ?>
											<div style="width:180px;float:left;"><input type='checkbox' style="width:15px;margin-left:20px" name="xydac_form_<?php echo $this->xydac_core_name.$name."[]"; ?>" id="<?php echo $this->xydac_core_name.$name; ?>" value="<?php _e($val_name,'xydac'); ?>" <?php if($this->xydac_core_editmode && in_array($val_name,$_checkboxeditdata)) echo "checked=checked"; ?>  />&nbsp;<?php _e($val_label,'xydac'); ?></div><?php if($_i==1) {$_i=0;echo "<br />";}else $_i++; ?>
										<?php } ?>
									<?php } elseif($type=='textarea') { ?><label for='<?php echo $this->xydac_core_name.$name ?>' style="font-weight:bold;"><?php echo $arr_label ?></label>
										<textarea style="height:<?php if(isset($height)) echo $height; else echo "300px"; ?>" name=xydac_form_'<?php echo $this->xydac_core_name.$name; ?>' class='name' id='<?php echo $this->xydac_core_name.$name; ?>'><?php if($this->xydac_core_editmode) {echo $this->get_array_val($this->xydac_editdata,$name); } ?></textarea>
									<?php } ?>
								<p><?php echo $desc ?></p>
								</div>
							<?php }}  ?>
							<!--END ADDED FORM SECTION -->
							</div>
							<?php do_action("xydac_core_form"); ?>
							
								<input type="hidden" name="xydac_form_<?php echo $this->xydac_core_name."_old"; ?>" value="<?php echo $this->xydac_editdata[$this->xydac_core_name.'_old'];?>">
						<?php } ?>
						
						<p class='submit'>
						<input type="submit"  name="xydac_form_<?php if($this->xydac_core_editmode) echo $this->xydac_core_name.'_update_submit'; else  echo $this->xydac_core_name.'_add_submit'; ?>" class="button-primary" value="<?php if($this->xydac_core_editmode) _e('Update '.$this->xydac_core_label,$this->xydac_core_name); else  _e('Add '.$this->xydac_core_label,$this->xydac_core_name); ?>"></p>
						</form>
					<?php do_action('xydac_core_leftfoot'); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php do_action('xydac_core_foot'); ?>
	<?php if(!xydac()->is_xydac_ucms_pro())xydac()->xydac_show_donate_link(false); ?>
	
	<?php }
	
} ?>