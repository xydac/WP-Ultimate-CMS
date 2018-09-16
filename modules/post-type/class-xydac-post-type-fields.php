<?php

class xydac_post_type_fields extends xydac_ultimate_cms_core{
	var $name;

	function __construct($name)
	{
		$this->name = $name;
		
		global $xydac_cms_fields;
		$form_variables = array(
				'field_label' => array( 'arr_label' => __('Field Label',XYDAC_CMS_NAME) , 'name' => '[field_label]', 'type'=>'string', 'desc'=> __('Label used for Page type Field ',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_type' => array( 'arr_label' => __('Field Type',XYDAC_CMS_NAME) , 'name' => '[field_type]', 'type'=>'array', 'desc'=> __('Field Type.',XYDAC_CMS_NAME) , 'default'=>' ', 'values'=>$xydac_cms_fields['fieldtypes']['posttype']),
				'field_has_multiple' => array( 'arr_label' => __('Multiple Input',XYDAC_CMS_NAME) , 'name' => '[field_has_multiple]', 'type'=>'boolean', 'desc'=> __('Does this Field take multiple values.',XYDAC_CMS_NAME) , 'default'=>'false'),
				'field_desc' => array( 'arr_label' => __('Description',XYDAC_CMS_NAME) , 'name' => '[field_desc]', 'type'=>'string', 'desc'=> __('Enter the short description for the field',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_val' => array( 'arr_label' => __('Field Value',XYDAC_CMS_NAME) , 'name' => '[field_val]', 'type'=>'string', 'desc'=> __('Please enter the values for the field (This is optional)',XYDAC_CMS_NAME) , 'default'=>' '),
				'field_order' => array( 'arr_label' => __('Field Order ',XYDAC_CMS_NAME) , 'name' => '[field_order]', 'type'=>'string', 'desc'=> __('Enter 1,2,3.. order in which you want the Custom Field to appear.',XYDAC_CMS_NAME) , 'default'=>' '),
		);
		
		add_filter('xydac_core_leftdiv',array($this,'xydac_core_leftdiv'));
		//add_action('xydac_core_head',array($this,'core_head'),1);
		add_action('xydac_core_righthead',array($this,'right_head'));
		add_filter('xydac_core_field_name',array($this,'field_name'));
		add_filter('xydac_core_headfootcolumn',array($this,'headfootcolumn'));
		add_filter('xydac_core_editlink',array($this,'xydac_core_editlink_func'));

		
		//parent::__construct(xydac()->modules->post_type->get_module_name().__("_field",XYDAC_CMS_NAME),xydac()->modules->post_type->get_module_label().__(" Field",XYDAC_CMS_NAME),xydac()->modules->post_type->get_base_path()."&manage_".xydac()->modules->post_type->get_module_name()."=".$name,xydac()->modules->post_type->get_registered_option('field')."_".$name,$form_variables);
		$args = array('field_val' => $name);
		parent::__construct(xydac()->modules->post_type,'field',$form_variables,$args);
		
	}

	function field_name()
	{
		return "field_name";
	}
	function xydac_core_editlink_func($str)
	{
		return $str.'&manage_post_type='.$this->name;
	}
	function headfootcolumn()
	{
		$headfootcolumn = array('field_name'=>__("Name",XYDAC_CMS_NAME),
							'[field_label]'=>__("Label",XYDAC_CMS_NAME),
							'[field_type]'=>__("Type",XYDAC_CMS_NAME),
							//'[field_has_multiple]'=>__("Multiple",XYDAC_CMS_NAME),
							//'[field_desc]'=>__("Description",XYDAC_CMS_NAME),
							//'[field_val]'=>__("Value",XYDAC_CMS_NAME),
							'[field_order]'=>__("Field Order",XYDAC_CMS_NAME)
						);
		return $headfootcolumn;
	}
	function right_head()
	{
		echo '<p><strong> '.__('Custom Post Type Name : ',XYDAC_CMS_NAME).'<span style="color:red">'.$this->name.'</span></strong></p>';
	}
	function xydac_core_leftdiv()
	{
		return "class=xydacfieldform";
	}

	/** Nothing below this is used, to use uncomment the xydac_core_head action */


	function getField($formItem, $editdata, $index){
		extract($formItem);
		$formName = $this->xydac_core_name."[".$index."]".$name;
		if($type=='boolean')
		{?>
				<input type="hidden" name="xydac_form_<?php echo $formName; ?>" value="<?php echo ($this->get_array_val($editdata,$name)=='true')?'true':'false'; ?>"/>
				<input type='checkbox' style="width:15px;margin-left:20px" name="xydac_form_<?php echo $formName; ?>" id="<?php echo $name; ?>" value="true" <?php if(true && $this->get_array_val($editdata,$name)=='true') echo "checked=checked"; ?>  />
			
		<?php } elseif($type=='array') { ?>
			
			<select id='<?php echo $name; ?>' name='xydac_form_<?php echo $formName ?>' class='postform' >
				<?php  foreach($values as $n=>$c) {   ?>
					<option value='<?php echo $n; ?>' <?php if($default==$n && !true) {echo 'selected';}elseif(true) { if($n==$this->get_array_val($editdata,$name)) echo 'selected'; }  ?>><?php echo $c ?></option>
					<?php } ?>
			</select>
			<div class="clear"></div>
		<?php } elseif($type=='string') {
			?>
			
			<input type='text' name='xydac_form_<?php echo $formName ?>' class='name' id='<?php echo $name ?>' value="<?php if(true) { echo $this->get_array_val($editdata,$name);} ?>"/>
		
		<?php } elseif($type=='checkbox') { ?>
			
				<?php $_checkboxeditdata = $this->get_array_val($editdata,$name);$_i=0; 
			if(!is_array($_checkboxeditdata)) {$_checkboxeditdata= array($_checkboxeditdata);}
				foreach($values as $val_name=>$val_label){ ?>
					<div style="width:180px;float:left;"><input type='checkbox' style="width:15px;margin-left:20px" name="xydac_form_<?php echo $formName."[]"; ?>" id="<?php echo $name; ?>" value="<?php _e($val_name,'xydac'); ?>" <?php if(true && in_array($val_name,$_checkboxeditdata)) echo "checked=checked"; ?>  />&nbsp;<?php _e($val_label,'xydac'); ?></div><?php if($_i==1) {$_i=0;echo "<br />";}else $_i++; ?>
				<?php } ?>
		<?php } elseif($type=='textarea') { ?>
			
			<textarea style="height:<?php if(isset($height)) echo $height; else echo "300px"; ?>" name='xydac_form_<?php echo $formName; ?>' class='name' id='<?php echo $name; ?>'><?php if(true) {echo $this->get_array_val($editdata,$name); } ?></textarea>
		<?php }
	}
	function get_array_val($arr,$key)
	{
		//echo $arr.' '. $key;
		$key = substr(preg_replace('/\]\[/', '$$', $key),1,-1);
		
		$e= explode('$$',$key);
		$ar = &$arr;
		foreach($e as $v)
			@$ar = $ar[$v];
		unset($ar);
		return $arr;
	}
	function core_head($data)
	{
		$headfootcolumn = array('name'=>__("Name",XYDAC_CMS_NAME));
		$headfootcolumn = apply_filters( 'xydac_core_headfootcolumn', $headfootcolumn );
		$xydac_rowdata = $data->parent_class->get_field($data->field_val);

		?>
		<form class="xydac_custom_field_form" <?php if($this->xydac_core_editmode) echo "id='form_edit_".$this->xydac_core_name."'"; else echo "id='form_create_".$this->xydac_core_name."'"; ?> action='<?php if($this->xydac_core_editmode) echo $this->xydac_core_form_action.'&edit_'.$this->xydac_core_name.'=true&'.$this->xydac_core_name.'_name='.$this->xydac_editdata[$this->namefield_name]; else echo $this->xydac_core_form_action; ?>' method='post'>
		<div class="alignright topbar">
			<input type="submit"  name="xydac_form_<?php if($this->xydac_core_editmode) echo $this->xydac_core_name.'_update_submit'; else  echo $this->xydac_core_name.'_add_submit'; ?>" class="button-primary" value="<?php _e('Save '.$this->xydac_core_label,$this->xydac_core_name); ?>">
		</div>

		<table class='wp-list-table widefat fixed striped pages xydac_fields_edit' id="someee">
			<thead>
		<?php //form-table
		foreach($headfootcolumn as $name=>$label){
			if(($this->xydac_core_editmode && ($name=='name' || $name=='field_name')) || (!$this->xydac_core_editmode)){
				if($name=='[field_name]')
					echo '<th scope="col" class="manage-column column-title column-primary xydac-col-'.$this->xydac_core_name.'-'.str_replace(array('[',']',' '),'',$name).'" id="'.$name.'" scope="col">'.$label.'</th>';
				else
					echo '<th scope="col" class="manage-column xydac-col-'.$this->xydac_core_name.'-'.str_replace(array('[',']',' '),'',$name).'" id="'.$name.'" scope="col">'.$label.'</th>';
				
			}
		} ?>

		</thead>

		<?php
		
		echo "<tbody id='fields_tbody_".$this->xydac_core_name."'>";
		if(is_array($xydac_rowdata))
			foreach($xydac_rowdata as $id=>$value)
			{ 
				$name = $value[$data->namefield_name];
				$this->print_input_row($id, $name, $value, true);
			} 
			echo "<tr><td colspan='7' class='info'> Add New Fields Below</td></tr>";
			$this->print_input_row($id+1, '', [], false);
			$this->print_input_row($id+2, '', [], false);  
			$this->print_input_row($id+3, '', [], false);  
			$this->print_input_row($id+4, '', [], false);  
		echo "</tbody>";
				?>

		</table>
		
		<div class="clear"></div>
		<input type="hidden" name="xydac_form_<?php echo $this->xydac_core_name."_old"; ?>" value="<?php echo $this->xydac_editdata[$this->xydac_core_name.'_old'];?>">
		<script>
 		jQuery("tbody").on("click", ".toggle-row", function() {
            jQuery(this).closest("tr").toggleClass("is-expanded");
        });
			</script>
		
		</form>
		<?php
		wp_die();
	}

	function print_input_row($id, $name, $value=[], $readonlyName = true){

		$classname = $name == '' ? 'newfield' : $name;

		echo '<tr id="content-type-'.$name.'" class="'.$classname.'">';
		if($readonlyName){
			echo "<td class='column-primary'>".$name.'<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
			echo '<input type="hidden" name="xydac_form_'.$this->xydac_core_name."[".$id."][".$this->namefield_name.']" value="'.$name.'"/>';
		}
		else
			echo '<td class="column-primary"><input type="text" placeholder="Field Name" name="xydac_form_'.$this->xydac_core_name."[".++$id."][".$this->namefield_name.']" value=""/><button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td>';
		foreach ($this->xydac_core_form_array as $formItem){
			
			echo "<td  data-colname='".$formItem['arr_label']."'>";
				$this->getField($formItem, $value, $id);
			echo "</td>";
		}
		echo '</tr>';

	}
}

?>