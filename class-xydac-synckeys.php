<?php
if ( !defined( 'XYDAC_CMS_SYNCKEYS' ) )define('XYDAC_CMS_SYNCKEYS',"XYDAC_CMS_SYNCKEYS");
/**
 * This class is used to maintain a associative key value pair aloong with sync id for all the various types synced.
 * @author Xydac
 *
 */
class xydac_synckeys{
	var $synkeysarr;
	
	public function __construct(){
		$this->synkeysarr = get_option(XYDAC_CMS_SYNCKEYS);
		if(!is_array($this->synkeysarr))
			$this->synkeysarr = array();
	}
	
	public function update(){
		update_option(XYDAC_CMS_SYNCKEYS,$this->synkeysarr);
	}
	
	public function add($type,$name,$val)
	{
		$this->synkeysarr[$type][$name] = $val;
	}
	public function remove($type,$name)
	{
		$this->synkeysarr[$type][$name] = -1;
	}
	public function get($type,$name)
	{
		if(isset($this->synkeysarr[$type][$name]) && $this->synkeysarr[$type][$name]!=-1)
			return $this->synkeysarr[$type][$name];
		else
			return false;
	}
	public function isValid($type,$name){
		if(isset($this->synkeysarr[$type][$name]) && $this->synkeysarr[$type][$name]!=-1)
			return true;
		else
			return false;
	}
	public function get_all_sync_keys(){
		return $this->synkeysarr;
	}
	
}