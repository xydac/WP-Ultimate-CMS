<?php

/*
 * XYDAC DAO CLASS FOR WordPress
* This is an essential Data access class to use WordPress options as a datasource.
*
* @version 1.0
*/
class xydac_options_dao{
	private $registered_option = array();
	private $backup_option;
	public $namefield_name;


	public function set_backup_option($option){
		if(!$this->is_option_registered($option))
			return;
		$this->backup_option = $option;
	}

	public function get_backup_option(){
		return $this->backup_option;
	}

	public function register_option($option){
		//check if array registered_option has $option, if doesn't have then add to array.
		if(!is_array($option))
			$option = array($option=>'');
		$this->registered_option = array_merge($this->registered_option, $option);
	}

	private function is_option_registered($option)
	{
		if(is_array($option) || empty($option) || !in_array($option,array_keys($this->registered_option)))
			return false;
		else
			return true;
	}
	/**
	 *
	 * @param unknown_type $option
	 * @param array $args['values'] This the the array from which some key value pair can be compared with the key value pair of the object fetched.
	 * @param String $args['match_keys'] if you want to match both key and value from the $values array to fetched $option_values array then set this to true, else only value will be compared
	 * @param array $args['fields'] This the the array of fields
	 * @param String $args['is_value_array'] Defines if the fetched $option_values array has its value equal to an array of array.
	 * @return void|Ambigous <mixed, boolean>|Ambigous <multitype:unknown , mixed, boolean>
	 */
	public function get_options($option,$args=null){
		//xydac()->log('get_option',$this->registered_option);
			
		if(!$this->is_option_registered($option))
			return;
		
		//Step 1: Define variables
		$is_value_array = isset($args['is_value_array']) && 'true'===$args['is_value_array'] ? true :false;
		$match_keys = isset($args['match_keys']) && 'true'===$args['match_keys'] ? true :false;
		$final_val_array = isset($args['final_val_array']) && 'true'===$args['final_val_array'] ? true :false;
		if(is_array($args)&& isset($args['fields']))
			$fields = $args['fields'];
		else $fields = null;
		if(is_array($args)&& isset($args['values']))
			$values = $args['values'];
		else $values = null;
		if(is_array($args)&& isset($args['filter']))
			$filter = $args['filter'];

		//Step 2: Gets the actual data from WordPress.
		$option_values = get_option($option);

		//xydac()->log('get_option delta '.$option,$fields);

		$backtrace = debug_backtrace();
		xydac()->log(' dao->get_options for '.$option.' called by:'. $backtrace[1]['function'].'<-'.$backtrace[2]['function'],$args);

		
		//Step 4: If values is given then handle values
		if(isset($values) && is_array($values)){//'values'=>array('name'=>'hhh')
			$final = array();
			//values is array from which we get known values
			foreach($values as $val_key=>$val_val)//$val_key=name $val_val = 'hhh'
			{
				if($is_value_array){
					foreach($option_values as $k=>$option_value)//[1]=>array('name'=>'abc')
						foreach($option_value as $key=>$value)//$key = name $value=abc
						if(is_array($val_val)){
							foreach($val_val as $vall)
								if($key==$val_key && $vall == $value)
									if(count($values)>1 || $final_val_array)
										$final[$k][$vall]=$option_value;
									else
										$final[$vall]=$option_value;
						}else{
							if(($match_keys==true && $key==$val_key && $val_val == $value) || ($match_keys==false &&$val_val == $value))
								if(count($values)>1 || $final_val_array)
									$final[$k]=$option_value;
								elseif(!is_array($option_value))
									$final[$k]=$option_value;
							else
								$final=$option_value;
						}
				}
			}
			$option_values = $final;
		}
		//Step 3: If values and fields are not defined then returns the option fetched.
		// moved step 3 after 4 because active gave wrong result set.
		if(empty($fields) && empty($values))
			return $option_values;
		

		//Step 5: If fields are given the Handle Fields
		if(isset($fields) && !empty($fields)){
			if(!is_array($fields))
				$fields = array($fields);
			if($is_value_array && is_array($option_values)){
				foreach($option_values as $k=>$option_value)
					foreach($option_value as $key=>$value)
					if(!in_array($key,$fields))
					unset($option_values[$k][$key]);
			}else if( is_array($option_values)){
				foreach($option_values as $k=>$option_value)
					if(!in_array($k,$fields))
					unset($option_values[$k]);
			}
		}

		if(!empty($filter))
			if(is_array($filter)){
			foreach($filter as $f)
				$option_values = call_user_func(array($this,$f.'_filter'),$option_values,$is_value_array);
		}else{
			$option_values = call_user_func(array($this,$filter.'_filter'),$option_values,$is_value_array);
		}
		/* if(is_array($option_values))
			asort($option_values); */			
		return $option_values;
	}
	
	/***
	 * This method is used to insert an object into WordPress Options.
	 * added support to insert multiple object at once by sending the object as array and setting isDataArray as true;
	 */
	public function insert_object($option,$data,$isDataArray=false){
		if(!$this->is_option_registered($option))
			return new WP_Error('err', __("Not Insterted [Cause]: Option Not Registered.",XYDAC_CMS_NAME));
	
		$xydac_options = get_option($option);
		if(!$xydac_options || (!is_array($xydac_options) && trim($xydac_options)==""))
		{
			$temp = array();
			if($isDataArray)
				foreach($data as $v)
					array_push($temp,$v);
			else
				array_push($temp,$data);
			update_option($option,$temp);
			return true;
		}
		if(is_array($xydac_options))
		{
			if($isDataArray)
				foreach($data as $v)
				array_push($xydac_options,$v);
			else
				array_push($xydac_options,$data);
			if($this->namefield_name=='')
				$this->namefield_name = 'name';
			usort($xydac_options, array($this,'xy_cmp'));
			update_option($option,$xydac_options);
			return true;
		}
		return false;
	}
	public function update_object($option,$data,$oldname,$namefieldname){
		if(!$this->is_option_registered($option))
			return false;;
		$xydac_options = get_option($option);;
	
		if(is_array($xydac_options))
		{
			foreach($xydac_options as $k=>$xydac_option){
				if($xydac_option[$namefieldname]==$oldname)
				{
					unset($xydac_options[$k]);break;
				}
			}
			array_push($xydac_options,$data);
				$this->namefield_name = $namefieldname;
			usort($xydac_options, array($this,'xy_cmp'));
			update_option($option,$xydac_options);
			return true;
		}
		else
			return false;
	}
	public function delete_object($option,$name,$namefieldname){
		if(!$this->is_option_registered($option))
			return false;
		$xydac_options = get_option($option);
		$name = sanitize_title_with_dashes($name);
		foreach($xydac_options as $k=>$xydac_option)
			if($xydac_option[$namefieldname]==$name)
			{
				
				unset($xydac_options[$k]);
				$this->namefield_name = $namefieldname;
				usort($xydac_options, array($this,'xy_cmp'));
				update_option($option,$xydac_options);
				return true;
			}
			else
				return false;
		return false;
	}
	public function delete_all_object($option){
		update_option($option, '');
		return true;
	}
	
	//--------
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
	private function value_filter($data,$is_value_array)
	{
		$output = array();
		if(!is_array($data))
			return $data;
		if($is_value_array){
			foreach($data as $k=>$option_value){
				if(is_array($option_value))
					foreach($option_value as $key=>$value)
					array_push($output,$value);
				else
					return $option_value;
			}
		}else{
			foreach($data as $key=>$value)
				array_push($output,$value);
		}
		//if(count($output)==1)
		//return $output[0];
		return $output;
	}

	function set_options($option,$data,$args=null)
	{
		if(!$this->is_option_registered($option))
			return;
		update_option($option,$data);
	}
	/*
	 * This function takes a backup of given option
	*
	* @version 1.0
	*/
	function save_backup($option)
	{
		if(!$this->is_option_registered($option))
			return;
		$option_data = $this->get_options($option);
		$backup_data = $this->get_options($this->backup_option);
		$backup_data[$option] = $option_data;
		$this->set_options($this->backup_option,$backup_data);
	}

	function restore_backup($option)
	{
		if(!$this->is_option_registered($option))
			return;
		$backup_data = $this->get_options($this->backup_option);
		$option_data = $backup_data[$option];
		$this->set_options($option,$option_data);
	}
}
?>