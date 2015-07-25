<?php
/*
Module Name:	Custom Stuff
Type:			Snippet
Description:	Adds a collection of useful Custom Snippets of WordPress tweaks from http://www.wphub.com/tutorials/code-snippets-wordpress/. Please check site for more details.
Author:			deepak.seth
Module URI:     http://www.wphub.com/tutorials/code-snippets-wordpress/
Author URI:		http://www.xydac.com/
Version:		1.0
*/


class xydac_snippets_customstuff extends xydac_snippets{
    
    function __construct(){
        $this->name         = 'cutom_stuff';
        $this->label        = 'Custom Stuffs';
        $this->description  = '';
        $this->type         = '';
        $this->order        = 1;
        $this->contenttype  = 'array';
        //$this->create_item('cusm_jasss','Custom String 1','','checkbox',array('option1'=>'option1','option2'=>'option 2'));
        //$this->create_item('cusm_rasss','Custom String 2','','radio',array('option1'=>'option1','option2'=>'option 2'));
        
        $this->create_item('wphub_removecommenturl','1. Remove the url field from WordPress comment form','','checkbox',array('yes'=>'Yes'));
        $this->create_item('wphub_admindisplayauthorownpost','2. Display comments in admin to authors own posts only','','checkbox',array('yes'=>'Yes'));
        
        $this->create_item('wphub_temporarymaint','3. Temporary Maintenance','','checkbox',array('yes'=>'Yes'));
        $this->create_item('wphub_temporarymaintmessage','Temporary Maintenance message','','text');
        
        $this->create_item('wphub_breakoutofframes','4. Break Out of Frames for WordPress','','checkbox',array('yes'=>'Yes'));
        $this->create_item('wphub_firstattchasfeat','5. Set first attachment as featured image','','checkbox',array('yes'=>'Yes'));
        
        $this->create_item('wphub_featimginfeed','6. Add featured images to WordPress feeds','','checkbox',array('yes'=>'Yes'));
        $this->create_item('wphub_excludepagefrmsearch','8. Exclude pages from WordPress search results','','checkbox',array('yes'=>'Yes'));
        
        $this->create_item('wphub_redirectpostonsingleres','9. Redirect to post if search results only returns one post','','checkbox',array('yes'=>'Yes'));
        
        $this->create_item('wphub_minimumcommentlimit','10. Set Minimal Comment Limit In WordPress','','text');
        
        
        parent::__construct();
        
        $this->validate();    
    }
    
    function validate(){
        if(xydac()->options->get('wphub_removecommenturl',true)=='yes'){
            add_filter('comment_form_default_fields',array($this,'remove_comment_fields'));   
        }
        
        if(xydac()->options->get('wphub_admindisplayauthorownpost',true)=='yes'){
            if(!current_user_can('edit_others_posts'))
                add_filter('comments_clauses', array($this,'wps_get_comment_list_by_user'));
                   
        }
        
        if(xydac()->options->get('wphub_temporarymaint',true)=='yes'){
            add_action('get_header', array($this,'wp_maintenance_mode'));
        }
        
        if(xydac()->options->get('wphub_breakoutofframes',true)=='yes'){
            add_action('wp_head', array($this,'break_out_of_frames'));
        }
        
        if(xydac()->options->get('wphub_firstattchasfeat',true)=='yes'){
            add_filter('the_content', array($this,'wphub_firstattchasfeat'));
        }
        
         
        if(xydac()->options->get('wphub_featimginfeed',true)=='yes'){
            add_filter('the_excerpt_rss', array($this,'wphub_featimginfeed'));
            add_filter('the_content_feed', array($this,'wphub_featimginfeed'));
        }
        
        if(xydac()->options->get('wphub_excludepagefrmsearch',true)=='yes'){
            add_filter('pre_get_posts', array($this,'wphub_excludepagefrmsearch'));
        }
        
        
        if(xydac()->options->get('wphub_redirectpostonsingleres',true)=='yes'){
            add_filter('template_redirect', array($this,'wphub_redirectpostonsingleres'));
        }
        
        $commentlimit = intval(xydac()->options->get('wphub_minimumcommentlimit'));
        if($commentlimit>0){
            add_filter('preprocess_comment', array($this,'wphub_minimumcommentlimit'));
        }
        
        
        
        
        
        
    }
    
    
    
    /*Custom Methods */
    //Remove the url field from WordPress comment form
    function remove_comment_fields($fields) {
        unset($fields['url']);
        return $fields;
    }
    
    //Display comments in admin to authors own posts only
    function wps_get_comment_list_by_user($clauses) {
        if (is_admin()) {
                global $user_ID, $wpdb;
                $clauses['join'] = ", wp_posts";
                $clauses['where'] .= " AND wp_posts.post_author = ".$user_ID." AND wp_comments.comment_post_ID = wp_posts.ID";
        }
        return $clauses;
    }
    
    
    // Temp Maintenance - with http response 503 (Service Temporarily Unavailable)
    // This will only block users who are NOT an administrator from viewing the website.
    function wp_maintenance_mode(){
        if(!current_user_can('edit_themes') || !is_user_logged_in() ){
            $msg = xydac()->options->get('wphub_temporarymaintmessage');
            if(empty($msg))
                wp_die('Maintenance, please come back soon.', 'Maintenance - please come back soon.', array('response' => '503'));
            else
               wp_die($msg, array('response' => '503'));
        }
    }
    
    // Break Out of Frames for WordPress
    function break_out_of_frames() {
        if (!is_preview()) {
            echo "\n<script type=\"text/javascript\">";
            echo "\n<!--";
            echo "\nif (parent.frames.length > 0) { parent.location.href = location.href; }";
            echo "\n-->";
            echo "\n</script>\n\n";
        }
    }
    
    //Set attachment as featured image
    function wphub_firstattchasfeat($content) {
        global $post;
        if (has_post_thumbnail()) {
            // display the featured image
            $content = the_post_thumbnail() . $content;
        } else {
            // get & set the featured image
            $attachments = get_children(array(
                'post_parent' => $post->ID, 
                'post_status' => 'inherit', 
                'post_type' => 'attachment', 
                'post_mime_type' => 'image', 
                'order' => 'ASC', 
                'orderby' => 'menu_order'
            ));
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    set_post_thumbnail($post->ID, $attachment->ID);
                    break;
                }
                // display the featured image
                $content = the_post_thumbnail() . $content;
            }
        }
        return $content;
    }
    
    //Add featured images to WordPress feeds
    function wphub_featimginfeed($content) {
        global $post;
        if(has_post_thumbnail($post->ID)) {
            $content = get_the_post_thumbnail($post->ID) . $content;
        }
        return $content;
    }
    
    //Exclude pages from WordPress search results
    function wphub_excludepagefrmsearch($query) {
        if ($query->is_search) {
            $query->set('post_type', 'page');
        }
        return $query;
    }
    
    // redirect to post if search results only returns one post
    function wphub_redirectpostonsingleres() {
        if (is_search()) {
            global $wp_query;
            if ($wp_query->post_count == 1 && $wp_query->max_num_pages == 1) {
                wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
                exit;
            }
        }
    }
    
    
    //Set Minimal Comment Limit In WordPress
    function wphub_minimumcommentlimit( $commentdata ) {
        $minimalCommentLength = intval(xydac()->options->get('wphub_minimumcommentlimit'));
        if ( strlen( trim( $commentdata['comment_content'] ) ) < $minimalCommentLength )
            {
            wp_die( 'All comments must be at least ' . $minimalCommentLength . ' characters long.' );
            }
        return $commentdata;
    }
    
}

?>