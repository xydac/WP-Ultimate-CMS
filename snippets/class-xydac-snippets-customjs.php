<?php
/*
Module Name:	Custom Javascript
Type:			Snippet
Description:	This adds the code snippets which enables you to add Custom Javascript using a given form, which will be added to all the post and pages on your website. 
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0
*/


class xydac_snippets_customjs extends xydac_snippets{
    
    function __construct(){        
        $this->name         = 'cutom_js';
        $this->label        = 'Frontend Custom Javascript';
        $this->description  = 'You can provide Custom Javascript to be included on your website. These javascript will not be included in admin zone of website.';
        $this->type         = 'textarea';
        $this->order        = 1;
        parent::__construct();
        
        add_filter('xydac_cms_site_script',array($this,'xydac_cms_site_script'),60,1);
    }
    
    function xydac_cms_site_script($script){
        $script.= "\n".xydac()->options->get($this->name);
        return $script;
    }
    
    
    
}

?>