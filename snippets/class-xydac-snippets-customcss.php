<?php
/*
Module Name:	Custom CSS
Type:			Snippet
Description:	This adds the code snippets which enables you to add Custom CSS using a given form, which will be added to all the pages and posts on your website. 
Author:			deepak.seth
Author URI:		http://www.xydac.com/
Version:		1.0
*/


class xydac_snippets_customcss extends xydac_snippets{
    
    function __construct(){        
        $this->name         = 'cutom_css';
        $this->label        = 'Frontend Custom CSS';
        $this->description  = 'You can provide Custom CSS to be included on your website.';
        $this->type         = 'textarea';
        $this->order        = 1;
        parent::__construct();
        
        add_filter('xydac_cms_site_style',array($this,'xydac_cms_site_style_func'),60,1);
    }
    
    function xydac_cms_site_style_func($style){
        $style.= "\n".xydac()->options->get($this->name);
        return $style;
    }
    
    
    
}

?>