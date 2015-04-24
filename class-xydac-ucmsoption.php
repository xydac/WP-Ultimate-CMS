<?php

class xydac_ucmsoption{
    var $option;
    function __construct(){
        xydac()->dao->register_option(XYDAC_UCMS_FORMOPTION);
        $this->option = xydac()->dao->get_options(XYDAC_UCMS_FORMOPTION);
    }
    function get($key,$bool=false){
        if(isset($this->option[$key])){
            if(!$bool)
                return $this->option[$key];
            elseif(is_array($this->option[$key]) && count($this->option[$key])==1)
                return current($this->option[$key]);
            else
                return $this->option[$key];
        }else
            return '';
    }
    function get_all(){
        return $this->option;
    }
    
    //This method reloads the data in this class and reinitializes all the snippets.
    private function flushvalues(){
        $this->option = xydac()->dao->get_options(XYDAC_UCMS_FORMOPTION);
        $mods = xydac()->dao->get_options(XYDAC_CMS_MODULES,array('is_value_array'=>'true','match_keys'=>'true','values'=>array('type'=>'snippets'),'final_val_array'=>'true'));
        $modsactive = xydac()->dao->get_options(XYDAC_CMS_MODULES.'_active');
        remove_all_filters('xydac_cms_homeformoption');
        foreach($mods as $m)
            if(in_array($m['name'],$modsactive))
                new $m['classname']();
        
    }
    
    function set($key,$value){
        $this->option[$key] = $value;
    }
    
    function formsubmit(){
        $values = $_POST[xydac_ucms_form];
        $arr = array();
        foreach($values as $key=>$val){
            $arr[sanitize_key($key)] = wp_kses($val,array('strong'=>array()));
        }
        xydac()->dao->set_options(XYDAC_UCMS_FORMOPTION,$arr);
        $this->flushvalues();
    }
        
        

}
